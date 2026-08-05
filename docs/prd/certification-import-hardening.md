# Certification Import Hardening — PRD

## Background
The certification import pipeline assumes CSV files use English column
names, but production files use Arabic headers (with trailing spaces and a
typo: `الحنسية` for `الجنسية`). Because the importer reads
`$data['trainee_name'] ?? ''`, unknown Arabic headers silently become empty
strings, which the resolve-handlers then turn into placeholder records
(`temp_<uuid>` → renamed `trainee_{$id}`, and `temp_placeholder` document
types). A parser failure is converted into apparently-valid data.

## Problem statement
- Headers are Arabic; code reads English keys → always empty.
- Header cells carry trailing spaces → even exact Arabic keys fail.
- Placeholder creation hides root cause and corrupts data.
- Some dates are malformed (`109/2022`, `21/14/2022`, `10/9/9/2022`).
- Some rows legitimately lack trainer / nationality / date.

## Goals
- Support Arabic and English headers, incl. `الحنسية` and `الجنسية`.
- Trim whitespace and remove UTF-8 BOM from headers.
- Validate structure before import; fail fast with explicit errors.
- Log diagnostic information to the `import` channel.
- Stop import (or reject row) when required columns are missing/invalid.
- Prevent placeholder data creation entirely.
- Preserve streaming + queued chunking behavior (no full-file memory load).

## Non-goals
- UI redesign. Queue redesign. Database redesign.

## Requirements
### Header normalization
System must: trim whitespace; strip UTF-8 BOM; normalize spacing and
capitalization; collapse aliases.

### Header alias mapping
| CSV header                 | internal key              |
|----------------------------|---------------------------|
| اسم المتدرب / trainee_name | trainee_name              |
| اسم المدرب / trainer_name  | trainer_name              |
| نوع الوثيقة                | document_type             |
| تاريخ الاعتماد             | accreditation_date        |
| الحنسية / الجنسية          | country_name              |
| رقم الاعتماد               | accreditation_number      |
| الرقم المتسلسل المعتمد     | accredited_serial_number  |
| الرمز                      | document_code             |

### Validation
Reject a row (never create placeholder data) when:
- required header `trainee_name` is missing;
- row has more columns than the header row;
- `trainee_name` is empty;
- `accreditation_date` is present but invalid.

Optional columns (`document_type`, `country_name`, `trainer_name`,
`accreditation_date`, `accredited_serial_number`, `document_code`,
`accreditation_number`, `notes`) may be empty. Decisions:
- missing `document_type` → import with null documentable (logged), because
  1363/4999 production rows (27%) have no document type; a null FK is
  honest, a placeholder type is not.
- missing `accreditation_date` → null (DB column is nullable).
- missing serial/code → generated `SN-…`/`CERT-…` (columns are NOT NULL +
  unique); real rows always carry them.
- missing `accreditation_number` → null (column nullable).

Distinguish explicitly: header missing vs null vs empty vs invalid. Never
merge into `''`.

### Date handling
Accept `d/m/Y`, `m/d/Y`, `Y/m/d` (plus backslash/`*`/`-` separators, stray
spaces, and leading-zero months like `010`). Glued/corrupt values
(`109/2022`, `21/14/2022`, `10/9/9/2022`, `2023`, `IB1062023`) are
rejected with a clear reason — never guessed. Empty → null.

### Logging (`config/logging.php` `import` channel)
Raw headers, normalized headers, first ten rows, first ten mapped rows,
failed rows (with reasons), execution statistics.

### Error handling
Placeholder records must not be created. Explicit exceptions carrying row
index + column + reason.

### Testing matrix
Arabic headers (real file headers incl. trailing spaces), English headers,
mixed/missing headers, BOM present/absent, empty file, invalid dates,
missing columns, wrong column count, embedded-newline name, `الحنسية` /
`الجنسية` alias, duplicate rows, no-placeholder guarantee.
