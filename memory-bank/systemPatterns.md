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
   - Transforms AST into PHPStan-compatible type annotations
   - Uses Visitor pattern with double dispatch
   - TypeGeneratorVisitor with methods for each node type
   - Configurable options:
     * KeyQuotingStyle: NoQuotes, SingleQuotes, DoubleQuotes
     * maxListUnionTypes: Threshold for list union types (default 3)
   - Type generation rules:
     * Scalars: Direct mapping (bool, int, float, string, null, resource)
     * Objects: Class name or "object" for anonymous
     * Hashmaps: `array{key: type, ...}` with configurable key quoting
     * StdObjects: `object{key: type, ...}` syntax
     * Lists: Smart union handling (1 type → `list<T>`, 2-3 → `list<T1|T2|T3>`, >3 → `list<mixed>`)
     * Empty arrays: `array` (generic type)

## Data Flow
1. var_dump → Lexer → Parser → Generator → type annotation
2. Each component handles its own error cases
3. AST nodes implement accept() for generic visitor pattern

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
- **NodeVisitorInterface<R>**: Generic visitor interface in Parser namespace
  - Template parameter R defines the return type of visit methods
  - All visit methods return mixed (actual type determined by R)
  - Enables different visitor implementations with different return types
- **TypeGeneratorVisitor**: Implements NodeVisitorInterface<string>
  - Generates PHPStan type annotations from AST nodes
  - Returns string type for all visit methods
- **Benefits**:
  - Decoupling: Parser namespace independent of Generator namespace
  - Extensibility: New visitors can be created without modifying nodes
  - Type safety: Generic R parameter ensures type consistency
  - Flexibility: Different visitors can return different types
