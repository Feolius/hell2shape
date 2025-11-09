# Product Context

## Target Users
- PHP developers working with legacy codebases
- Developers adding type annotations to existing code
- Teams adopting PHPStan for static analysis

## Problem Statement
Developers often need to add type annotations to existing PHP code but struggle with:
- Determining array structures from complex var_dump output
- Creating accurate PHPStan array shape annotations
- Manual type inference being time-consuming and error-prone

## User Experience Goals
- Simple CLI interface for quick type generation
- Clear output showing suggested type annotations
- Ability to pipe var_dump output directly to the tool
- Helpful error messages for malformed input

## Key Scenarios
1. Developer runs `var_dump($complexArray)` and pipes output to hell2shape
2. Tool analyzes the structure and suggests type annotations
3. Developer copies suggestions into their codebase
4. Developer refines annotations based on actual usage patterns