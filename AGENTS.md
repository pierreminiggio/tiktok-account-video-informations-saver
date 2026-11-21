# AGENTS.md

Rules for any AI agent (or human) working on this project.

## Project overview

PHP CLI script (`main.php` → `src/App.php`) that calls an API for each TikTok account stored in the
`tiktok_account` table and saves the video information (id, url, caption) into `tiktok_video`.

- Namespace: `PierreMiniggio\TiKTokAccountVideoInformationsSaver\` (PSR-4, `src/`)
- DB access goes through `pierreminiggio/database-fetcher`, wrapped in repositories (`src/Repository/`)
- `config.php` is git-ignored; `config_example.php` is the committed template
- The DB schema and its migrations are in `README.md` (see "Database migrations" below)
- `README.md` points to this file; keep that pointer in place

## Database migrations

All database migrations live in the SQL block of the "Migration" section of `README.md`. There is no separate
migrations folder.

- A schema change is a new SQL statement **appended at the end of that block**, after the last existing
  statement and before the closing `/*!40101 SET CHARACTER_SET_CLIENT ... */` restore lines.
- Existing statements are never edited or reordered: the block is the history of the schema, and people who
  already ran it only need to run the new statements.
- Each new migration is preceded by a short SQL comment saying what it is for.
- An increment that needs a migration includes the updated `README.md` and says in the chat which new
  statement(s) the project owner has to run on the database.

## Development workflow: TDD

Every feature, change or bug fix is developed test-first:

1. **Red**: write a failing test that describes the expected behavior, and run it to see it fail for the right reason.
2. **Green**: write the minimum code that makes the test pass.
3. **Refactor**: clean up while keeping the tests green.

Rules:

- No production code without a failing test that requires it.
- Tests written for a change are real tests: they are added to the project (not throwaway scripts) and are
  part of the delivered increment.
- All tests must pass before an increment is delivered. Run the whole suite, not only the new tests.
- Business logic should be testable without network or real database access: keep HTTP calls and DB access
  behind small classes (repositories, clients) that can be replaced by fakes in tests.
- Tests use PHPUnit (`composer test` or `vendor/bin/phpunit`), live in `tests/`, and mirror the `src/` structure
  (`src/Repository/AccountRepository.php` → `tests/Repository/AccountRepositoryTest.php`).
- Repositories are tested with a real `DatabaseFetcher` built on a mocked `DatabaseConnection`, asserting the exact
  SQL and parameters sent. There is no test database.
- Code must stay compatible with PHP 8.0 (no intersection types, no `readonly`, no enums, etc.).
- Keep the code style already used in the project (PSR-4, typed properties/returns, constructor promotion).

## Delivery workflow: small increments as zip files

Features and changes are delivered to the project owner as **small increments**, not as one big change.
Break a feature into the smallest steps that each leave the project in a working state (tests passing).

Each increment is delivered as a `.zip` file containing:

- **Every file created or edited** by the increment (tests included), with their full content, at their path
  relative to the project root, so the zip can be extracted directly at the project root and overwrite files.
  No wrapping folder.
- Only files that were created or edited. Untouched files are not included.

Alongside the zip, the chat message must include:

- A **list of `rm` commands** (run from the project root) for every file and folder that must be deleted to
  apply the increment, in a code block.
- If nothing has to be deleted, say explicitly in the chat that there is nothing to delete.
- A one or two sentence summary of what the increment does, and what the next increment will be, if any.

Naming: `increment-NN-short-description.zip` (e.g. `increment-02-add-phpunit.zip`).
