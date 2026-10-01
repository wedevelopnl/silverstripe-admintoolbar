---
name: warn-phpstan-ignore
enabled: true
event: file
action: warn
conditions:
  - field: new_text
    operator: regex_match
    pattern: @phpstan-ignore
  - field: file_path
    operator: regex_match
    pattern: \.php$
---

**PHPStan Suppression Requires Justification**

This project runs PHPStan at level max with 100% type coverage. Every `@phpstan-ignore` is a type-safety hole.

**Before adding a suppression, in order:**

1. **Fix the types** — most errors yield to correct PHPDoc (`positive-int`, `non-empty-string`, array shapes).
2. **Write a stub** in `phpstan/` for framework limitations (magic methods, extension-provided fields) and register it under `stubFiles` in `.docker/app/phpstan.neon.dist`.
3. **Last resort:** `@phpstan-ignore <identifier>` with a comment on the same or preceding line explaining why neither option works.
