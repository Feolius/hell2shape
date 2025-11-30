# Active Context

## Current Focus
- Parser implementation and testing

## Recent Decisions
- Using Lexer/Parser/Generator architecture
- PHP 8.4+ as minimum version
- Symfony Console for CLI interface
- All Node classes are now `readonly` and `final` for immutability
- Parser tests refactored to use a data provider and real `var_dump` output

## Immediate Next Steps
1. Finalize Parser implementation
2. Implement Generator component
3. Create CLI interface

## Open Questions
- Specific token types needed for Lexer
- Type annotation formatting details
