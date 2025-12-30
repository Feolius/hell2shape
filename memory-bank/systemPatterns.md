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
   - Object parsing strategy:
     * Regular objects: Extract class name only, skip internals
     * Anonymous objects: Use dedicated AnonymousObjectNode
     * Brace counting mechanism to handle nested objects correctly

3. Generator:
   - Three-phase generation: AST → Type IR → Formatter → String
   - **Phase 1**: TypeGeneratorVisitor converts AST to Type IR (TypeInterface)
   - **Phase 2**: TypeFormatterVisitor formats Type IR with indentation
   - **Phase 3**: String output with proper formatting
   - Configurable options:
     * KeyQuotingStyle: NoQuotes, SingleQuotes, DoubleQuotes
     * indentSize: 0 (single-line), 2, 4 (default), or custom
   - **Type IR System**:
     * TypeInterface: Base interface with merge(), accept(), and toString()
     * TypeVisitorInterface: Generic visitor for operations on Type IR
     * TypeFormatterVisitor: Handles all formatting logic
     * ScalarType: Non-mergeable types (int, string, bool, etc.)
     * UnionType: Automatic deduplication of union members
     * HashmapType: Mergeable array shapes with optional key support (immutable)
     * StdObjectType: Mergeable stdClass objects (immutable)
     * ListType: Contains element type (can be merged or union)
     * HashmapKey: Represents hashmap keys (int|string) with optional flag
     * StdObjectKey: Represents object keys (string only) with optional flag
   - **Merging Logic**:
     * Hashmaps/StdObjects: Missing keys become optional (?), different types create unions
     * Recursive merging for nested structures
     * Non-hashmap types create unions when merged
   - **Formatting Logic**:
     * Multi-line: Hashmaps and objects get newlines after each element
     * Single-line: Unions stay on one line (e.g., `int|string|float`)
     * Recursive indentation: Nested structures properly indented
     * Configurable indent size for different coding standards
     * **Integer key rule**: Integer keys always output without quotes, regardless of KeyQuotingStyle
     * String keys respect KeyQuotingStyle (NoQuotes, SingleQuotes, DoubleQuotes)

4. CLI Interface:
   - Symfony SingleCommandApplication for streamlined single-command tool
   - STDIN input handling for piping var_dump output
   - Command-line options:
     * `--indent/-i`: Configurable indentation size (default: 4)
     * `--quotes`: Key quoting style selection (none, single, double)
   - Comprehensive error handling:
     * Empty input validation with usage examples
     * Option validation (indent must be non-negative integer)
     * Quoting style validation with available options
     * Pipeline error handling (LexerException, ParserException, generic errors)
   - User-friendly help text and error messages

## Data Flow
1. STDIN → var_dump text input
2. var_dump → Lexer → tokens
3. tokens → Parser → AST
4. AST → TypeGeneratorVisitor → Type IR
5. Type IR → TypeFormatterVisitor → formatted string
6. formatted string → STDOUT
7. Each component handles its own error cases
8. AST nodes implement accept() for NodeVisitorInterface
9. Type IR implements accept() for TypeVisitorInterface

## Key Design Decisions
- Strict separation of lexical analysis, parsing and generation
- AST as intermediate representation between parsing and generation
- Visitor pattern with double dispatch for type generation
- All Node classes are readonly and final for immutability
- Focus on producing syntactically valid type annotations
- Configurable formatting options for flexibility
- Extensible architecture for future type system improvements

## Node Hierarchy
- AbstractNode (abstract base with generic accept() method)
  - Scalar nodes: BoolNode, IntNode, FloatNode, StringNode, NullNode
  - Special nodes: ResourceNode, ObjectNode, AnonymousObjectNode
  - Container nodes: ListNode, HashmapNode, StdObjectNode
  - Item nodes: ListItemNode, HashmapItemNode, StdObjectItemNode

## Visitor Pattern Architecture

### AST Visitor Pattern (Parser → Generator)
- **NodeVisitorInterface<R>**: Generic visitor interface in Parser namespace
  - Template parameter R defines the return type of visit methods
  - All visit methods return mixed (actual type determined by R)
  - Enables different visitor implementations with different return types
- **TypeGeneratorVisitor**: Implements NodeVisitorInterface<TypeInterface>
  - Converts AST nodes to Type IR
  - Returns TypeInterface for all visit methods
- **Benefits**:
  - Decoupling: Parser namespace independent of Generator namespace
  - Extensibility: New visitors can be created without modifying nodes
  - Type safety: Generic R parameter ensures type consistency
  - Flexibility: Different visitors can return different types

### Type IR Visitor Pattern (Type IR → Output)
- **TypeVisitorInterface**: Generic visitor interface in Generator\Type namespace
  - Defines visit methods for all Type IR classes
  - Returns mixed to allow different visitor implementations
- **TypeFormatterVisitor**: Implements TypeVisitorInterface
  - Formats Type IR into strings with configurable indentation
  - Handles KeyQuotingStyle application
  - Manages recursive indentation for nested structures
- **Benefits**:
  - Separation of concerns: Types handle structure, formatters handle presentation
  - Extensibility: Easy to add new visitors (JSON export, analysis, etc.)
  - Flexibility: Different formatters for different output needs
  - Testability: Formatting logic isolated and independently testable
