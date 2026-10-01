---
name: block-service-locator
enabled: true
event: file
action: warn
conditions:
  - field: new_text
    operator: regex_match
    pattern: Injector::inst\(\)->(get|create)\(
  - field: file_path
    operator: regex_match
    pattern: src/
---

**SilverStripe DI Anti-Pattern Detected**

Do NOT use `Injector::inst()->get()` or `Injector::inst()->create()` in `src/` — the service-locator pattern hides dependencies and makes code hard to test.

**Use instead:**

- `Foo::create()` / `Foo::singleton()` — still resolved through Injector, so projects can replace the class.
- `private static array $dependencies = ['service' => '%$' . ServiceInterface::class];` for property injection on controllers, extensions and data objects.
