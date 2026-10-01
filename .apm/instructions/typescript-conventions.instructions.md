---
description: TypeScript conventions for the toolbar behaviour modules and their tests
applyTo: "**/*.ts"
---

# TypeScript Conventions

- Each module exports `init*(root = document, …)`; `location`, `reload` and `now` are injectable parameters with browser defaults
- An `init*` is a no-op when its `data-*` hook is absent
- Never `innerHTML` with page or server content — build nodes with `document.createElement` + `textContent`
- Visibility is the class `ssat:hidden`, never the `hidden` attribute
- Write every class string literally (the Tailwind scanner reads `client/src/ts`); never build one from fragments
- localStorage only through `storage.ts` (`readItem`/`writeItem` swallow storage errors)
- No client-side i18n: strings come from template `data-*` attributes or translated server responses
- Tests live beside their module (`*.test.ts`), build their DOM from the template hooks, stub `fetch` with `vi.stubGlobal`
- Coverage thresholds: statements 90, branches 85, functions 90, lines 90
