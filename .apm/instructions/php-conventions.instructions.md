---
description: PHP conventions including SilverStripe DI patterns and PHPStan rules
applyTo: "**/*.php"
---

# PHP Conventions

- `declare(strict_types=1);` in every file
- Instantiate through Injector: `Foo::create()` / `Foo::singleton()`; property injection via `private static array $dependencies`. Never `Injector::inst()->get()` in `src/`
- Controllers, DataObjects and Extensions cannot use constructor injection — the framework instantiates them without DI args
- Extensions extend `SilverStripe\Core\Extension`; read config via `$this->getOwner()->config()->get('key')` — `static::config()` does not work in an extension
- PHPStan runs at `level: max` with 100% type coverage and no baseline. Fix types or add a stub (its directory needs a mount in `.docker/compose.yml`) before reaching for `@phpstan-ignore`; justify any ignore inline
- Use the narrowest PHPDoc type: `positive-int` for written record IDs, `non-empty-string` for labels, `class-string<T>` for class names
