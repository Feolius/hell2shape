# hell2shape

> Generate PHPStan type annotations from var_dump output

A CLI tool that analyzes PHP `var_dump()` output and generates PHPStan-compatible type annotations, helping you add type hints to legacy code or complex data structures.

## Use It Online

🌐 **[Try hell2shape in your browser](https://hell2shape.netlify.app/)** - No installation required. Works locally in your browser, no data transferred -- thanks to php-wasm.

## Installation

### Option 1: Download PHAR (Standalone Executable)
Download the latest `hell2shape.phar` from the [releases page](https://github.com/Feolius/hell2shape/releases) and use it directly:

```bash
# Download the PHAR
curl -L https://github.com/Feolius/hell2shape/releases/latest/download/hell2shape.phar -o hell2shape.phar

# Make it executable
chmod +x hell2shape.phar

# Use it
php -r 'var_dump($myArray);' | ./hell2shape.phar
```

### Option 2: Install via Composer
```bash
composer require feolius/hell2shape
```

**Requirements:**
- PHP 8.3 or higher

## Quick Start

```bash
# If installed via Composer
php -r 'var_dump($myArray);' | vendor/bin/hell2shape

# If using PHAR
php -r 'var_dump($myArray);' | ./hell2shape.phar
```

### Option 3: Use [cpx](https://cpx.dev/)
```bash
php -r 'var_dump($myArray);' | cpx feolius/hell2shape
```

## Usage

Pipe any `var_dump()` output to `hell2shape`:

```bash
# From a PHP script
php script.php | vendor/bin/hell2shape

# From a one-liner
php -r 'var_dump(["name" => "John", "age" => 30]);' | vendor/bin/hell2shape
```

### Output Example

**Input (var_dump):**
```
array(2) {
  ["name"]=>
  string(4) "John"
  ["age"]=>
  int(30)
}
```

**Output (PHPStan type):**
```php
array{
    name: string,
    age: int
}
```

## Options

### `--indent` / `-i`
Control indentation for multi-line output (default: 4 spaces)

```bash
# Single-line output
hell2shape --indent=0

# 2-space indentation
hell2shape -i 2

# Default 4-space indentation
hell2shape
```

### `--quotes`
Control key quoting style in array shapes

```bash
# No quotes (default)
hell2shape --quotes=none
# Output: array{name: string}

# Single quotes
hell2shape --quotes=single
# Output: array{'name': string}

# Double quotes
hell2shape --quotes=double
# Output: array{"name": string}
```

### `--class` / `-c`
Control how class names are formatted (default: unqualified)

```bash
# Unqualified - just the class name (default)
hell2shape --class=uqn
# Output: User

# Qualified - with namespace
hell2shape -c qn
# Output: App\Models\User

# Fully qualified - with leading backslash
hell2shape --class=fqn
# Output: \App\Models\User
```

## Features

- ✅ Scalar types (int, string, bool, float, null)
- ✅ Arrays and nested arrays
- ✅ Array shapes with optional keys
- ✅ Objects (class names)
- ✅ stdClass objects as object shapes
- ✅ Lists with union types
- ✅ Complex nested structures
- ✅ Configurable formatting

## Examples

### Simple Array
```bash
php -r 'var_dump(["id" => 1, "name" => "Alice"]);' | hell2shape
```
Output:
```php
array{
    id: int,
    name: string
}
```

### Nested Structure
```bash
php -r 'var_dump(["user" => ["name" => "Bob", "roles" => ["admin", "user"]]]);' | hell2shape
```
Output:
```php
array{
    user: array{
        name: string,
        roles: list<string>
    }
}
```

### Single-line Output
```bash
php -r 'var_dump(["id" => 1, "active" => true]);' | hell2shape --indent=0
```
Output:
```php
array{id: int, active: bool}
```

### Optional Keys
When arrays have different structures, hell2shape marks missing keys as optional:
```php
array{
    id: int,
    name: string,
    email?: string
}
```

### Object Types
```bash
php -r 'namespace App\Models; class User {} var_dump(new User());' | hell2shape
```
Output (default - unqualified):
```php
User
```

With qualified names:
```bash
php -r 'namespace App\Models; class User {} var_dump(new User());' | hell2shape -c qn
```
Output:
```php
App\Models\User
```

With fully qualified names:
```bash
php -r 'namespace App\Models; class User {} var_dump(new User());' | hell2shape --class=fqn
```
Output:
```php
\App\Models\User
```

## Limitations

⚠️ **Important:** This tool provides a **starting point** for type annotations, not a complete solution.

- var_dump doesn't show all possible values, only the current state
- Union types are inferred from visible data only
- You should review and refine the generated types based on your actual usage
- Empty arrays are typed as `array` (not `list<mixed>`)

## Development

```bash
# Run tests
vendor/bin/phpunit

# Run PHPStan
vendor/bin/phpstan analyse

# Run code style checks
vendor/bin/ecs check
```

## License

MIT License - see [LICENSE](LICENSE) file for details

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
