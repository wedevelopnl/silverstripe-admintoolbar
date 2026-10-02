#!/bin/sh
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
WORKTREE_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
# Compose accepts an explicit project name only in its normalized form, so apply
# the same rule it uses for a directory-derived one: lowercase, drop everything
# outside [a-z0-9_-], strip leading '-' and '_'.
PROJECT_NAME="$(basename "$WORKTREE_DIR" | tr '[:upper:]' '[:lower:]' | tr -cd 'a-z0-9_-' | sed 's/^[-_]*//')"

HASH=$(printf '%s' "$PROJECT_NAME" | cksum | awk '{print $1}')
OFFSET=$((HASH % 1000))

WEB_PORT=$((8000 + OFFSET))
DB_PORT=$((13000 + OFFSET))

cat > "$SCRIPT_DIR/.env" <<EOF
COMPOSE_PROJECT_NAME=${PROJECT_NAME}
WEB_PORT=${WEB_PORT}
DB_PORT=${DB_PORT}
EOF

echo "Generated .docker/.env:"
echo "  COMPOSE_PROJECT_NAME=${PROJECT_NAME}"
echo "  WEB_PORT=${WEB_PORT}"
echo "  DB_PORT=${DB_PORT}"
