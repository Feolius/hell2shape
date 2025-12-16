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
   - Two-phase generation: AST → Type IR → String
   - **Phase 1**: TypeGeneratorVisitor converts AST to Type IR (TypeInterface)
   - **Phase 2**: Type IR toString() generates PHPStan type annotations
   - Configurable options:
     * KeyQuotingStyle: NoQuotes, SingleQuotes, DoubleQuotes
   - **Type IR System**:
     * TypeInterface: Base interface with merge() and toString()
     * ScalarType: Non-mergeable types (int, string, bool, etc.)
     * UnionType: Automatic deduplication of union members
     * HashmapType: Mergeable array shapes with optional key support
     * StdObjectType: Mergeable stdClass objects
     * ListType: Contains element type (can be merged or union)
   - **Merging Logic**:
     * Hashmaps/StdObjects: Missing keys become optional (?), different types create unions
     * Recursive merging for nested structures
     * Non-hashmap types create unions when merged

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
