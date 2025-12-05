# Active Context

## Current Focus
- Parser implementation complete
- Ready to move to Generator component

## Recent Decisions
- Using Lexer/Parser/Generator architecture
- PHP 8.4+ as minimum version
- Symfony Console for CLI interface
- All Node classes are now `readonly` and `final` for immutability
- Parser tests refactored to use a data provider and real `var_dump` output
- Object parsing (both regular and anonymous) skips internals by counting braces
- Extracted `skipObjectInternals()` method for code reuse
- Created dedicated `AnonymousObjectNode` class (no parameters needed)

## Recent Changes
- Fixed object parsing to properly skip object internals using brace counting
- Created `AnonymousObjectNode` class for anonymous objects
- Updated Parser to return `AnonymousObjectNode` for anonymous objects
- Added test case with nested anonymous objects to verify correct parsing


## Immediate Next Steps
1. Implement Generator component
2. Create CLI interface

## Open Questions
- Type annotation formatting details
