---
description: Code style conventions for indentation, encoding, and line endings
applyTo: "**/*"
---

# Code Style

- 4 spaces: PHP, `composer.json`
- 2 spaces: YML, JS, TS, JSON, CSS (enforced via `.editorconfig`)
- LF line endings, UTF-8, trailing newline
- PHP style is enforced by Rector (`.docker/app/rector.php`, incl. `SilverstripeSetList::CODE_STYLE`); there is no php-cs-fixer
