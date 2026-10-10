# Merging entities and the alias learning loop

`app/Services/Entity/EntityMerger.php` folds a duplicate entity into a surviving one.

## The learning loop

The important side effect is the last step, not the first: every spelling the duplicate answered to is rewritten as an alias of the survivor. That is what makes the system get better instead of merely getting cleaned. A reviewer confirms "allebanon is Lebanon" exactly once; from then on it is a rung-one exact hit on every future import, with no fuzzy matching involved and no chance of drift.

## Mechanics

- Merges are **transactional and reference-complete**: nothing is deleted until every foreign key pointing at the duplicate has been repointed.
- The duplicate's aliases are repointed at the survivor, and the duplicate's own `name_key` is added as an alias (so existing references keep resolving).
- The duplicate row is then deleted.

### Foreign-key reference map

`EntityMerger` keeps a map of the tables and columns that reference each entity type:

| Entity | Table | References |
|---|---|---|
| `Trainee` | `trainees` | `certifications.trainee_id` |
| `Trainer` | `trainers` | `certifications.assigned_trainer_id` |
| `Country` | `countries` | `certifications.country_id`, `trainees.country_id` |
| `DocumentType` | `board_document_types` | `certifications.documentable_id` (polymorphic — only rows whose `documentable_type` matches are touched) |

Keep this map in sync with the schema. A missing entry does not error — it orphans rows — so it is checked against the live schema before every merge.

Polymorphic columns must be constrained by their type column when repointing, or a trainee id would be rewritten because it happens to equal a document-type id.

## Aliases

`addAlias()` registers an extra spelling for an entity without deleting anything — the "teach it 'like Syria' means Syria" operation. Writes a `manual` source alias with `created_by`.

Aliases are unique per `(aliasable_type, alias_key)`; the same spelling can only ever point at one entity of a given type.

## API

- `merge(string $entityType, int $survivorId, int $duplicateId, ?int $reviewerId = null): array` — returns `['moved' => table.column => row count, 'aliases' => written]`. Throws `InvalidArgumentException` for unknown entity types or self-merges.
- `addAlias(string $entityType, int $entityId, string $rawSpelling, ?int $reviewerId = null): bool` — `false` when the spelling normalises to an empty key.

## From the importer side

`ResolvesEntities::rememberAlias()` writes `import`-source aliases automatically when a derived key (noise-stripped / article-stripped) resolves — so a spelling the ladder *did* figure out is persisted for next time.
