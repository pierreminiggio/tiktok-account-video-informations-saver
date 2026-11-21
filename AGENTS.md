# AGENTS.md

Rules for any AI agent (or human) working on this project.

## Project overview

PHP CLI script (`main.php` → `src/App.php`) that calls an API for each TikTok account stored in the
`tiktok_account` table and saves the video information (id, url, caption) into `tiktok_video`.

- Namespace: `PierreMiniggio\TiKTokAccountVideoInformationsSaver\` (PSR-4, `src/`)
- DB access goes through `pierreminiggio/database-fetcher`, wrapped in repositories (`src/Repository/`)
- `config.php` is git-ignored; `config_example.php` is the committed template
- The DB schema/migration is documented in `README.md`

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
