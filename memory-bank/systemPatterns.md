# System Architecture

## Core Components
1. Lexer:
   - Tokenizes raw var_dump output
   - Identifies array structures, types, and values
   - Produces stream of tokens for parsing

2. Parser:
   - Processes token stream from Lexer
   - Builds Abstract Syntax Tree (AST) representation
   - Handles nested structures and relationships

3. Generator:
   - Transforms AST into PHPStan-compatible type annotations
   - Formats output with proper array shape syntax
   - Ensures valid PHPStan type system compliance

## Data Flow
1. var_dump → Lexer → Parser → Generator → type annotation
2. Each component handles its own error cases

## Key Design Decisions
- Strict separation of lexical analysis, parsing and generation
- AST as intermediate representation between parsing and generation
- Focus on producing syntactically valid type annotations
- Extensible architecture for future type system improvements