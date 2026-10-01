---
name: warn-constructor-injection
enabled: true
event: file
action: warn
conditions:
  - field: new_text
    operator: regex_match
    pattern: public function __construct\(
  - field: file_path
    operator: regex_match
    pattern: src/.*\.php$
---

**Constructor Injection Warning — SilverStripe Limitation**

Controllers, `DataObject`s, `Extension`s and `ModelData` components are instantiated by the framework without DI arguments — constructor parameters will silently be missing.

**Use property injection instead:**

```php
private static array $dependencies = [
    'myService' => '%$' . MyServiceInterface::class,
];

public MyServiceInterface $myService;
```

A plain service class may use constructor injection — wire it explicitly in YAML with `constructor:` config.
