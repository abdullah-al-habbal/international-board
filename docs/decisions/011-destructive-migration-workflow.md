# Decision 011: Destructive Migration Workflow

**Date:** 2026-07-21
**Status:** Accepted

## Context

The project has no production data. Schema changes require `migrate:fresh --seed` to rebuild cleanly. Standard `migrate` alone cannot handle column renames, type changes, or constraint modifications without manual intervention.

## Decision

Deploy via a single `production` branch on Hostinger. Two migration modes:

| Trigger | Command | Data Loss |
|---------|---------|-----------|
| Normal push | `migrate --force` | No |
| Commit with `[migrate:fresh]` | `migrate:fresh --force --seed` | Yes |
| Manual checkbox in GitHub Actions | `migrate:fresh --force --seed` | Yes |

Destructive migration keywords in commit message: `[migrate:fresh]`, `[destructive]`, `[reset-db]`, `[fresh]`.

## Consequences

- No backups — acceptable because there is no production data to lose.
- `migrate:fresh` drops all tables and re-runs all migrations + seeders.
- Standard pushes use safe `migrate --force` for additive changes.
- CI/CD pipeline at `.github/workflows/ci.yml` handles both modes.
- Documented in `CLAUDE.md` under Deployment section.
