# Technical decisions — Manager Library (Laravel)

The full documentation of the spec's translation to React + Laravel lives in the
[`test-manager-library-3145556-docs`](https://github.com/thiagxrg/test-manager-library-3145556-docs) fork,
folder **`spec-php/`**, with the same structure as `spec-python/` and `spec-.net/`:

1. [`constitution.md`](../test-manager-library-3145556-docs/spec-php/constitution.md) — principles and declared stack trade-offs (including the language deviation, Article XI)
2. [`plan.md`](../test-manager-library-3145556-docs/spec-php/plan.md) — technical decisions D-01 to D-12
3. [`architecture.md`](../test-manager-library-3145556-docs/spec-php/architecture.md) and [`architecture-overview.md`](../test-manager-library-3145556-docs/spec-php/architecture-overview.md)
4. [`data-model.md`](../test-manager-library-3145556-docs/spec-php/data-model.md) and [`api-contract.md`](../test-manager-library-3145556-docs/spec-php/api-contract.md) — business-identical to the reference, only translated to English
5. [`tasks.md`](../test-manager-library-3145556-docs/spec-php/tasks.md) — tasks with their evidence
6. [`adr/`](../test-manager-library-3145556-docs/spec-php/adr/) — architecture decision records

**Note:** this fork made two explicit decisions that deviate from the reference spec, both documented above:
(1) strict hexagonal architecture (plain-PHP domain entities, ports, Eloquent only in adapters) instead of a
pragmatic Eloquent-in-domain approach; (2) all documentation and API error messages in English instead of
Spanish (constitution Article XI).
