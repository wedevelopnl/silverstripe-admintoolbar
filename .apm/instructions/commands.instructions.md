---
description: Available Task (Taskfile) commands for development, testing, and QA
applyTo: "**/*"
---

# Commands

Run with [Task](https://taskfile.dev) (`task <name>`); everything PHP runs in Docker. List all with `task --list`.

| Command | Description |
|---------|-------------|
| `task up` | Start Docker services (build if needed) |
| `task down` | Stop Docker services |
| `task destroy` | Stop services and remove volumes |
| `task build` | Build Docker images without starting |
| `task sh` | Shell in the app container (interactive) |
| `task test` | Run all PHP test suites |
| `task test-unit` | PHPUnit `unit` suite (no database) |
| `task test-integration` | PHPUnit `integration` suite (database fixtures) |
| `task test-functional` | PHPUnit `functional` suite (HTTP) |
| `task coverage` | All suites with coverage (HTML + Clover in `coverage/`) |
| `task coverage-check` | Coverage, then fail below 90% |
| `task mutate` | Infection (MSI ≥ 99) — maintainer-run; slow |
| `task analyse` | PHPStan (level max, PHP 8.3–8.5 range) |
| `task analyse-php85` | PHPStan with the analysis target pinned to PHP 8.5 |
| `task rector` | Rector (applies changes) |
| `task rector-dry` | Rector dry-run |
| `task class-leak` | Report classes nothing references |
| `task relock` | Regenerate `.docker/app/composer.lock` (follows SS6.x) |
| `task verify-lock` | Fail if the committed lock no longer covers the module's requires |
| `task flush` | Clear SilverStripe cache |
| `task dev-build` | Rebuild database and manifest |
| `task qa` | Full QA: PHPStan ×2 + Rector + class-leak + PHPUnit with the coverage gate, in parallel |

- Non-interactive tasks exec with `-T`; call `docker compose -f .docker/compose.yml exec -T app …` the same way when running a tool directly.
