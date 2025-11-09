# Project Brief: hell2shape

## Purpose
Provide developers with a starting point for PHPStan array shape type annotations by analyzing var_dump output

## Core Features
- Parse var_dump array structures
- Generate reasonable type annotation suggestions
- Provide CLI interface via Symfony Console
- Support basic nested array structures
- Include unit tests for core functionality

## Key Considerations
- Output is a starting point, not guaranteed to be 100% accurate
- var_dump has inherent limitations for type inference
- Focus on usability and developer productivity