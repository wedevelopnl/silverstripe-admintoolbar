#!/usr/bin/env bash
# Pre-push QA gate — intercepts `git push` commands and runs linters first.
# Full test suite runs in CI (GitHub Actions), so only fast checks here.
# Used as a Claude Code PreToolUse hook on the Bash tool.

set -euo pipefail

input="$(cat)"
command="$(echo "$input" | jq -r '.tool_input.command // empty')"

# Only intercept git push commands
if [[ ! "$command" =~ ^git\ push ]]; then
  exit 0
fi

echo "Pre-push QA gate: running linters before push..." >&2

cd "$(git rev-parse --show-toplevel 2>/dev/null || echo "${CLAUDE_PROJECT_DIR:-.}")"

# vite/esbuild output is not byte-identical across Node majors. The dist diff
# check below is only meaningful when the local Node major matches CI's
# (.nvmrc). Try `nvm use` first; only bail if the pinned Node isn't installed.
if [[ -f .nvmrc ]]; then
  pinned_major="$(cut -d. -f1 .nvmrc | tr -d '[:space:]v')"
  current_major="$(node --version | sed 's/^v//' | cut -d. -f1)"
  if [[ "$pinned_major" != "$current_major" ]]; then
    nvm_sh=""
    for candidate in "${NVM_DIR:-$HOME/.nvm}/nvm.sh" /opt/homebrew/opt/nvm/nvm.sh /usr/local/opt/nvm/nvm.sh; do
      if [[ -f "$candidate" ]]; then
        nvm_sh="$candidate"
        break
      fi
    done
    if [[ -n "$nvm_sh" ]]; then
      set +u
      # shellcheck source=/dev/null
      . "$nvm_sh"
      nvm use >/dev/null 2>&1 || true
      set -u
      current_major="$(node --version | sed 's/^v//' | cut -d. -f1)"
    fi
    if [[ "$pinned_major" != "$current_major" ]]; then
      echo "Node major mismatch: running v$current_major, .nvmrc pins v$pinned_major." >&2
      echo "Tried 'nvm use' but the pinned Node is unavailable — run 'nvm install' (or switch manually), then push." >&2
      exit 2
    fi
    echo "Switched Node to $(node --version) via nvm to match .nvmrc." >&2
  fi
fi

if ! npm run lint; then
  echo "Lint failed — push blocked." >&2
  exit 2
fi

if ! npm run format:check; then
  echo "Format check failed — push blocked." >&2
  exit 2
fi

if ! npm run typecheck; then
  echo "Typecheck failed — push blocked." >&2
  exit 2
fi

if ! npm run build; then
  echo "Build failed — push blocked." >&2
  exit 2
fi

# Same condition as CI js-qa's "Verify the built bundle is committed".
untracked="$(git status --porcelain --untracked-files=all -- client/dist | grep '^??' || true)"
if ! git diff --quiet -- client/dist || [[ -n "$untracked" ]]; then
  echo "client/dist is out of sync with source — rebuild and commit before pushing." >&2
  [[ -n "$untracked" ]] && echo "Untracked build output:"$'\n'"$untracked" >&2
  echo "Run: npm run build && git add client/dist && git commit" >&2
  exit 2
fi
