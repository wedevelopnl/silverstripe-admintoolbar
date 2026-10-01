---
description: Code style conventions for indentation, encoding, and line endings
applyTo: "**/*"
---

# Code Style

- 4 spaces: PHP, `composer.json`
- 2 spaces: YML, JS, TS, JSON, CSS (enforced via `.editorconfig`)
- LF line endings, UTF-8, trailing newline
- PHP style is enforced by Rector (`.docker/app/rector.php`, incl. `SilverstripeSetList::CODE_STYLE`); there is no php-cs-fixer
- TS/CSS style is enforced by Biome (`biome.json`)
- CSS: Tailwind prefix `ssat` (`ssat:flex`, `ssat:hover:text-primary`); raw hex colours only in `client/src/styles/tokens.css` / `fonts.css`
- `client/src/styles/icons.css` is a verbatim `silverstripe/admin` snapshot, excluded from Biome — refresh it by re-copying, never edit it (only its `@font-face` differs)
