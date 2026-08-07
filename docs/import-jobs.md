# Import jobs

The CSV importer runs as two queued jobs cooperating over a two-phase pipeline: split, then import chunks in batch groups.

## `ImportCertificationsJob` — the splitter

Runs first. Its job is to turn one large CSV into many small chunk files so that the queued job payload never grows with the file size.

- Reads the file once via `SplFileObject`, strips the UTF-8 BOM from the header, splits rows into chunks of `CHUNK_SIZE = 1000`.
- Each chunk is written to `storage/app/import_chunks/{md5(file)}/chunk-N.csv` (header repeated per chunk so each chunk is independently importable). A `manifest.json` records the chunk list.
- Chunk jobs are dispatched in **batch groups** of `BATCH_JOBS_LIMIT = 500`. Each group's completion handler dispatches the next group, so no single serialised batch payload contains thousands of jobs. The last group triggers cleanup and the success notification.
- Failure paths: a `RuntimeException` (unreadable file, no importable rows) fails the job directly; any other `Throwable` is rethrown so the queue retries. Either way the chunk directory is deleted and the user is notified.
- `#[Tries(3)]`, `#[Backoff([60, 120])]`.

## `ImportCertificationChunkJob` — the worker

Runs per chunk. Streams the chunk rows through a `LazyCollection` and hands them to `CertificationImportService::importChunk($rows, $creatorId, $headers)`, which performs the batched resolution described in the import-pipeline doc.

- A `UniqueConstraintViolationException` (duplicate accreditation number inside one chunk) fails the job immediately rather than letting it retry — it is a data problem, not a transient one.
- The unique `name_key` index means two chunk jobs meeting the same new trainee cannot both insert: the loser's insert becomes a no-op and the follow-up SELECT reads the winner's id.
- `#[Tries(3)]`, `#[Backoff([60, 120])]`. `failed()` logs the terminal state.

## One source of truth

The pipeline is driven by `CertificationImportService`, which both `ImportCertificationsJob` and `ImportCertificationChunkJob` receive via method injection. Header mapping, BOM handling, batch buffering and the resolution ladder all live there; the jobs are thin IO wrappers. `CsvImportHandler` remains available but is not used for certification imports (see the import-pipeline doc for why).
