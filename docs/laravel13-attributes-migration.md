# Laravel 13 property → attribute migration playbook

Codified runbook for migrating Laravel 13 class-property configuration to PHP class attributes. Written so an AI agent in another project can scan a codebase, convert the properties, and verify the result without behaviour drift.

Status: applied in this repo (22 models, 2 jobs, 4 commands). Gates green: pint, phpstan, 35 tests, `config:cache`/`route:cache`.

## 1. Prerequisites

- Laravel `^13.0` (attributes shipped in 12.x; the full set and `#[Initialize]` resolution path are stable in 13.x).
- PHP `>= 8.4`.
- `composer show laravel/framework` to confirm the version before touching anything.

## 2. Rule of thumb

`$property` → `#[Attribute]` on the **class declaration line**, with the **same value**. The old property is deleted; the attribute fully replaces it. Attributes are resolved lazily through `Model::resolveClassAttribute()` (`Model.php:2678`) or `Command::configureFromAttributes()` (`Command.php:155`), so behaviour is identical at runtime.

## 3. Attribute reference (verified against Laravel 13.24.0 vendor source)

### Eloquent models — `Illuminate\Database\Eloquent\Attributes`

| Attribute | Replaces | Constructor |
|---|---|---|
| `#[Fillable(...)]` | `protected $fillable` | `(array\|string ...$columns)` |
| `#[Guarded(...)]` | `protected $guarded` | `(array\|string ...$columns)` |
| `#[Unguarded]` | marker (disable mass-assignment protection) | no args |
| `#[Hidden(...)]` | `protected $hidden` | `(array\|string ...$columns)` |
| `#[Visible(...)]` | `protected $visible` | `(array\|string ...$columns)` |
| `#[Appends(...)]` | `protected $appends` | `(array\|string ...$columns)` |
| `#[Table(name, key, keyType, incrementing, timestamps, dateFormat)]` | `protected $table` (+ key/timestamps/dateFormat) | named/positional, all optional |
| `#[Connection(name)]` | `protected $connection` | `(UnitEnum\|string $name)` |
| `#[Touches(...)]` | `protected $touches` | `(array\|string ...$relations)` |
| `#[ObservedBy(classes)]` | manual `Model::observe()` in a service provider | `(array\|string $classes)` |

### Queues and jobs — `Illuminate\Queue\Attributes`

| Attribute | Replaces | Constructor |
|---|---|---|
| `#[Tries(3)]` | `public $tries` | `(int $tries)` |
| `#[Timeout(60)]` | `public $timeout` | `(int $timeout)` |
| `#[Backoff([60, 120])]` | `public $backoff` | `(array\|int ...$backoff)` — one arg stays as-is, multiple become an array |
| `#[MaxExceptions(3)]` | `public $maxExceptions` | `(int $maxExceptions)` |
| `#[Queue('name')]` | `public $queue` | `(UnitEnum\|string $queue)` |
| `#[UniqueFor(3600)]` | `public $uniqueFor` | `(int $uniqueFor)` |
| `#[FailOnTimeout]` | `public $failOnTimeout = true` | marker, no args |
| `#[Connection(name)]` | `public $connection` | `(UnitEnum\|string $name)` |

### Console commands — `Illuminate\Console\Attributes`

| Attribute | Replaces | Constructor |
|---|---|---|
| `#[Signature('cmd {--opt= : desc}')]` | `protected $signature` | `(string $signature, ?array $aliases = null)` |
| `#[Description('...')]` | `protected $description` | `(string $description)` |
| `#[Help('...')]` | `protected $help` | `(string $help)` |
| `#[Usage('cmd [options]')]` | — (repeatable) | `(string $usage)` |
| `#[Hidden]` | `protected $hidden = true` | marker, no args |

### Controllers — `Illuminate\Routing\Attributes\Controllers` (replaces `__construct` middleware / `authorizeResource`)

| Attribute | Replaces |
|---|---|
| `#[Middleware(...)]` | `$this->middleware(...)` in the constructor |
| `#[Authorize(...)]` | `authorizeResource()` / manual gates in the constructor |

### Form requests — `Illuminate\Foundation\Http\Attributes`

| Attribute | Replaces |
|---|---|
| `#[ErrorBag('x')]` | `protected $errorBag` |
| `#[RedirectTo('/route')]` | `protected $redirect` |
| `#[RedirectToRoute('name')]` | `protected $redirectRoute` |
| `#[StopOnFirstFailure]` | `protected $stopOnFirstFailure = true` |

### HTTP resources — `Illuminate\Http\Resources\Attributes`

| Attribute | Replaces |
|---|---|
| `#[Collects(Resource::class)]` | `public $collects` |
| `#[PreserveKeys(true)]` | `public $preserveKeys` |

> Do **not** trust these signatures from memory in a new project — they are stable but verify against `vendor/laravel/framework/src/Illuminate/**/Attributes/*.php` before mass-converting.

## 4. Scan commands

```bash
# Models
grep -rn "protected \$fillable\|protected \$guarded\|protected \$hidden\|protected \$visible\|protected \$appends\|protected \$table\|protected \$connection\|protected \$touches" app/Models/ --include="*.php"

# Jobs
grep -rn "public \$tries\|public \$timeout\|public \$backoff\|public \$maxExceptions\|public \$queue\|public \$uniqueFor\|public \$failOnTimeout\|public \$connection" app/Jobs/ --include="*.php"

# Commands
grep -rn "protected \$signature\|protected \$description\|protected \$help\|protected \$hidden" app/Console/Commands/ --include="*.php"

# Controllers (constructor middleware / authorizeResource)
grep -rn "middleware(\|authorizeResource(" app/Http/Controllers/ --include="*.php"

# Form requests
grep -rn "protected \$errorBag\|protected \$redirect\|protected \$redirectRoute\|protected \$stopOnFirstFailure" app/Http/Requests/ --include="*.php"

# Resource collections
grep -rn "public \$collects\|public \$preserveKeys" app/Http/Resources/ --include="*.php"
```

## 5. Conversion rules

### Models

```php
// BEFORE
class BlogPost extends Model
{
    protected $fillable = ['title', 'slug', 'content'];
    protected $hidden = ['secret'];
    protected $table = 'blog_posts';
    protected $appends = ['summary'];
}

// AFTER
#[Fillable(['title', 'slug', 'content'])]
#[Hidden(['secret'])]
#[Table('blog_posts')]
#[Appends(['summary'])]
class BlogPost extends Model
{
}
```

- Put every attribute immediately above the class declaration, one per line.
- `#[Table('name')]` uses the positional first arg — only pass `key`/`keyType`/`timestamps`/`dateFormat` when the property set them.
- Array args: single-line `['a', 'b']` is fine; multiline arrays are also valid.
- Do not convert a model that declares **no** property and relies on defaults (nothing to migrate).
- Watch for trait-provided `$fillable = []` defaults — they are framework defaults, not "properties to convert".
- If a model has both a real `$table` property and an inherited one, the reflection check `getProperty('table')->getDeclaringClass() === static::class` decides who wins — after conversion the attribute wins via `initializeModelAttributes()` (`Model.php:435`).

### Jobs

```php
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;

#[Tries(3)]
#[Backoff([60, 120])]
class ImportCertificationsJob implements ShouldQueue
{
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels
}
```

- Remove `public int $tries = 3;` and `public array $backoff = [60, 120];` entirely.
- `#[Backoff([60, 120])]` passes one array arg → stored as-is. `#[Backoff(60, 120)]` also works (two args → array).
- Consumption points: `Illuminate\Queue\Queue.php:234` (Tries), `:251` (Backoff). Marker/one-liner job attributes resolve before dispatch, so a plain `dispatch()` picks them up.

### Commands

```php
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('entities:merge
            {--entity=all : trainees|trainers|countries|document-types|all}
            {--auto-exact : Merge byte-identical collisions}')]
#[Description('Merge duplicate entities and teach the resolver new spellings')]
final class MergeEntitiesCommand extends Command
{
}
```

- **Preserve multiline signatures byte-for-byte** (same newlines, same indentation of the `{--...}` continuation lines) — the attribute value is a normal string, and Laravel's signature parser keys off the newline + option tokens, not indentation.
- `#[Signature('...', aliases: [...])]` when the command previously used the `protected $aliases` array.
- Commands with a `__construct(Filesystem $files)` dependency keep the constructor — attributes sit alongside it, untouched.

## 6. Pitfalls (learned the hard way in this repo)

1. **Multiline properties break simple line-based regex removal.** A `protected $fillable = ['a',\n 'b',\n];` does not match a single-line regex expecting `value;` on the same line. Removal must be line-stateful: match `^\s*protected \$(fillable|guarded|hidden|visible|appends|table)\s*=\s*`, then consume lines until one ends with `;` (inclusive), then swallow the following blank line. With Python `re`, remember `re.MULTILINE` or `^` anchors to string start only.
2. **`newInstanceWithoutConstructor()` is a liar for verification.** It skips `__construct`, which is where `initializeTraits()` fires the `#[Initialize]`-marked methods (`initializeGuardsAttributes`, `initializeModelAttributes`, ...). Attribute-driven `$fillable`/`$table` appear empty/pluralised under that instantiation — false alarms. Verify with a **real** `new $class()` (no DB touched by the constructor).
3. **`#[Initialize]` methods are the wiring.** `Model.php` keeps a static `$traitInitializers` map; attributes are merged per-instance in the constructor. Never bypass the constructor when asserting behaviour.
4. **`#[Override]` on FormRequest `rules()`/`authorize()` is a trap.** They are duck-typed, not overrides of a parent method — PHP raises a fatal ("cannot use #[Override] when no parent method exists"). Do not apply. (Separate concern from the CSV, but it was proposed and cancelled in this repo.)
5. **Run pint after conversion, not before.** It fixes the generated `#[Fillable([...])]` array indentation and import ordering (`array_indentation`, `ordered_imports`, `class_attributes_separation`). Keep it to the directories you touched to avoid reformatting unrelated files.
6. **Import ordering:** add `use` lines for the attribute classes in alphabetical position, or let pint's `ordered_imports` fix it.
7. **Do not double-apply.** If a script already added attributes, re-running it will duplicate them. Idempotent scripts must check for an existing `#[Fillable`/`#[Table]` on the class before inserting.
8. **`#[\Attribute]` instantiation throws are swallowed** by `resolveClassAttribute()` (try/catch returns null, `Model.php:2710`). A wrong-argument attribute silently degrades to defaults — always runtime-verify, not just syntax-check.

## 7. Verification gate (exact commands)

```bash
# 1. Runtime equality — real instantiation, before/after must match
#    (fillable, hidden, visible, appends, table, connection, touches)
./vendor/bin/pint --test app/Models app/Jobs app/Console app/Http
./vendor/bin/phpstan analyse app

# 2. Jobs — attributes resolve (tinker one-liner or a php script)
#    reflection: getAttributes(Illuminate\Queue\Attributes\Tries::class)[0]->newInstance()->tries

# 3. Commands — signature + description surfaces
php artisan list
php artisan help <command>     # options/defaults identical to pre-conversion

# 4. Caches and suite
php artisan config:cache && php artisan route:cache && php artisan config:clear && php artisan route:clear
composer test
```

## 8. Framework consumption points (for verifying attribute wiring on a given version)

| Concern | File |
|---|---|
| `resolveClassAttribute` | `Eloquent/Model.php:2678` |
| `#[Fillable]`/`#[Guarded]`/`#[Unguarded]` | `Eloquent/Concerns/GuardsAttributes.php:49,54-58` |
| `#[Hidden]`/`#[Visible]` | `Eloquent/Concerns/HidesAttributes.php:33` |
| `#[Table]`/`#[Connection]`/`#[Appends]` | `Eloquent/Model.php:435-454`, `Eloquent/Concerns/HasAttributes.php:214,217` |
| `#[Touches]` | `Eloquent/Concerns/HasTimestamps.php` |
| `#[Tries]`/`#[Backoff]`/`#[MaxExceptions]`/`#[Timeout]`/`#[FailOnTimeout]` | `Queue/Queue.php:179-254` |
| `#[Signature]`/`#[Description]`/`#[Help]`/`#[Usage]`/`#[Hidden]` | `Console/Command.php:155+` |

## 9. Scope applied in this repo

- **Models (22):** `$fillable`→`#[Fillable]`, `$hidden`→`#[Hidden]` (3: `CertifiedCenter`, `Trainer`, `User`), `$table`→`#[Table]` (9), `$appends`→`#[Appends]` (2: financial requests → `remaining_amount`). 26 properties removed. Runtime getters byte-identical to pre-conversion baseline.
- **Jobs (2):** `ImportCertificationsJob`, `ImportCertificationChunkJob` → `#[Tries(3)] #[Backoff([60, 120])]`.
- **Commands (4):** `BackfillNameKeysCommand`, `FindDuplicateEntitiesCommand`, `MergeEntitiesCommand` (multiline signature preserved), `CleanupStorageCommand` → `#[Signature]`/`#[Description]`.
- **Earlier, same campaign:** `#[ObservedBy]` on 6 models (+ deleted `ObserverServiceProvider`); spatie `#[Translatable]` on 8 models (replaced `public $translatable`); Laravel 13.24.0 upgrade (`VerifyCsrfToken`→`PreventRequestForgery`, `Pdo\Mysql::ATTR_SSL_CA`, `serializable_classes`).
- **Not present / not applicable:** `#[Guarded]`, `#[Visible]`, `#[Unguarded]`, `#[Touches]`, `#[Connection]`, queue timeout/queue/maxExceptions/uniqueFor/failOnTimeout, controller `#[Middleware]`/`#[Authorize]`, form-request attributes, resource `#[Collects]`/`#[PreserveKeys]`, command `#[Help]`/`#[Usage]`/`#[Hidden]`.
