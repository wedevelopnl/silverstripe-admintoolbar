---
description: Docker development environment setup and usage
applyTo: "**/*"
---

# Docker Dev Environment

- `task up` auto-generates `.docker/.env` if missing
- `.docker/.env` holds `COMPOSE_PROJECT_NAME`, `WEB_PORT`, `DB_PORT` (deterministic, hashed from the directory name — worktree-safe) and `SS_GRID_ADAPTER=tailwind`
- Regenerate after renaming/copying a worktree: `rm .docker/.env && task up`
- Testbed: `https://localhost:<WEB_PORT>`; admin login `admin` / `admin`
- The module is mounted piecewise at `/module`; `src/`, `lang/` and `phpstan/` are writable from the container, the rest is read-only
- A new exposed `client/` subdirectory needs a volume mount in `.docker/compose.yml` and a symlink line in `.docker/entrypoint.sh`
