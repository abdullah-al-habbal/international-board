# Certification Import Investigation Report

## Root cause
1. `CertificationImportService::mapRow()` reads English keys
   (`$data['trainee_name'] ?? ''`) while production CSV headers are Arabic
   → every lookup returns `''`.
2. Header cells contain trailing spaces (e.g. `اسم المتدرب `), so even an
   exact Arabic key match would fail without trimming.
3. `ResolveTraineeHandler::handle()` creates `temp_<uuid>` → `trainee_{$id}`
   when name is empty; `ResolveDocumentTypeHandler` creates
   `temp_placeholder` types. This turns parse failures into valid-looking
   data and makes debugging impossible (missing header, null, empty, and
   parser failure all collapse to `''`).

## Files analysed
- app/Services/Certification/CertificationImportService.php
- app/Services/Certification/Handlers/ResolveTraineeHandler.php
- app/Services/Certification/Handlers/ResolveDocumentTypeHandler.php
- app/Services/Csv/CsvImportHandler.php
- app/Jobs/ImportCertificationsJob.php
- app/Jobs/ImportCertificationChunkJob.php
- app/Filament/Admin/Resources/Certifications/Pages/ListCertifications.php
- tests/Feature/CertificationImportTest.php
- public/assets/imports/import.csv

## CSV characteristics
| Property         | Value                          |
|------------------|--------------------------------|
| Encoding         | UTF-8 (Arabic)                 |
| Delimiter        | `,`                            |
| BOM              | None (verified: first bytes `d8 a7 73 6d`) |
| Header count     | 10                            |
| Data rows        | 4999 (wc -l = 5008 due to one embedded newline) |
| Column width     | All 4999 rows = exactly 10 cols (verified with csv.reader) |
| Malformed dates  | `109/2022`, `21/14/2022`, `10/9/9/2022` |
| Embedded newline | One record: IB252 `MOHAMMAD IBRAHIM TABASHAH` (inside quotes) |

## Header mapping
| Original                 | Normalized          |
|--------------------------|---------------------|
| `اسم المتدرب `           | trainee_name        |
| `الرقم المتسلسل المعتمد `| accredited_serial_number |
| `الرمز `                 | document_code       |
| `رقم الاعتماد `          | accreditation_number |
| `نوع الوثيقة `           | document_type       |
| `تاريخ الاعتماد`         | accreditation_date  |
| `اسم المدرب `            | trainer_name        |
| `الحنسية`                | country_name        |
| `الحصول على الوثيقة ورقيا ` | paper_delivery    |
| `ملاحظات  `              | notes               |

## Imported rows
| Metric         | Value                       |
|----------------|-----------------------------|
| Total rows     | 4999                        |
| Importable     | 4960 (estimate, offline)   |
| Failed         | 39                          |
| - missing trainee | 2 (IB3416, IB5122)      |
| - invalid date    | 37 (see below)           |

Invalid-date rows (rejected, not guessed): 29× `21/14/2022`
(IB156–IB182 batch), plus `109/2022`, `10/9/9/2022`, `2023`, `9/382023`,
`9/382024`, `9/382025`, `IB1062023`, `4/42024`, `6/92025`, `1410/2025`.

## First imported rows (after fix)
- `IB100` → trainee "Ahmed Ali", doc type Training Certificate,
  serial IB100, code 5, accreditation IB1005, date 2022-02-14.
- `IB101` → date 13/1/2025 parsed as 2025-01-13 (day-first).

## Changes implemented
- `app/Services/Certification/CsvHeaderMapper.php` — new; normalizes
  headers (BOM strip, trim, quote strip, alias map Arabic+English incl.
  `الحنسية`/`الجنسية`), enforces required headers.
- `app/Services/Certification/Support/DateParser.php` — new; tolerant
  date parser, rejects ambiguous/corrupt dates.
- `app/Services/Certification/Exceptions/{MissingHeader,MissingValue,InvalidDate,RowLength}Exception.php` — new.
- `app/Services/Certification/CertificationImportService.php` — header
  mapping, explicit validation, imports real serial/code/accreditation
  number/notes, null-safe optional columns, `import`-channel logging.
- `app/Services/Certification/Handlers/ResolveTraineeHandler.php` — removed
  `temp_<uuid>` placeholder creation; empty name now throws.
- `app/Services/Certification/Handlers/ResolveDocumentTypeHandler.php` —
  removed `temp_placeholder` document type; empty name now throws.
- `tests/Feature/CertificationImportTest.php` — Arabic headers + BOM,
  `الجنسية` alias, embedded-newline name, invalid date, extra columns,
  no-placeholder guarantee.

## Remaining risks
- 29 rows share the corrupt `21/14/2022` batch date — needs source cleanup.
- 1363 rows import with null document type; UI shows unassigned.
- Document-type strings are free-form in the CSV (520 distinct values) →
  resolution creates many `board_document_types` rows.
- `importChunk` path (queued) relies on chunk files preserving headers;
  verified in tests via the queue-dispatch test.
- Estimate of 4960 importable is from offline validation; run a real dry
  run against a DB copy before production import.
