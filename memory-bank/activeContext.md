# Active Context

## Current Focus
- Generator implementation complete
- Ready to move to CLI interface

## Recent Decisions
- Using Lexer/Parser/Generator architecture
- PHP 8.4+ as minimum version
- Symfony Console for CLI interface
- All Node classes are now `readonly` and `final` for immutability
- Generic visitor pattern with NodeVisitorInterface<R> for extensibility
- Visitor pattern with double dispatch for Generator
- KeyQuotingStyle enum for array shape key formatting (NoQuotes, SingleQuotes, DoubleQuotes)
- List union type threshold: max 3 types before falling back to `list<mixed>`
- Empty arrays generate `array` type (not `list<mixed>`)
- StdObject generates `object{...}` syntax (not `array{...}`)

## Recent Changes
- Refactored visitor pattern to use generic NodeVisitorInterface<R>
- Created NodeVisitorInterface in Parser namespace for proper separation of concerns
- Updated AbstractNode to accept generic NodeVisitorInterface instead of concrete TypeGeneratorVisitor
- Updated TypeGeneratorVisitor to implement NodeVisitorInterface<string>
- Updated all 14 Node classes to use generic visitor interface
- All tests passing (33 tests, 44 assertions)

## Generator Implementation Details
- **Scalar types**: Direct mapping (bool, int, float, string, null, resource)
- **Objects**: Class name for regular objects, "object" for anonymous
- **Hashmaps**: `array{key: type, ...}` with configurable key quoting
- **StdObjects**: `object{key: type, ...}` syntax
- **Lists**: Smart union handling with threshold (1 type → `list<T>`, 2-3 types → `list<T1|T2|T3>`, >3 → `list<mixed>`)
- **Empty arrays**: `array` (generic type)

## Immediate Next Steps
1. Create CLI interface with Symfony Console

## Open Questions
None - Generator design is complete and tested
