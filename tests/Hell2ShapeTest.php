<?php

namespace Feolius\Hell2Shape\Tests;

use Feolius\Hell2Shape\Generator\ClassNameStyle;
use Feolius\Hell2Shape\Generator\GeneratorConfig;
use Feolius\Hell2Shape\Generator\KeyQuotingStyle;
use Feolius\Hell2Shape\Hell2Shape;
use PHPUnit\Framework\TestCase;

final class Hell2ShapeTest extends TestCase
{
    public function testGenerateSimpleArray(): void
    {
        $data = [
            'name' => 'John',
            'age' => 30,
            'active' => true,
        ];

        $config = new GeneratorConfig(indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'array{name: string, age: int, active: bool}';
        $this->assertSame($expected, $result);
    }

    public function testGenerateNestedArray(): void
    {
        $data = [
            'user' => [
                'id' => 1,
                'name' => 'John',
            ],
            'tags' => ['php', 'dev'],
        ];

        $config = new GeneratorConfig(indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'array{user: array{id: int, name: string}, tags: list<string>}';
        $this->assertSame($expected, $result);
    }

    public function testGenerateWithMultilineFormatting(): void
    {
        $data = [
            'user' => [
                'id' => 1,
                'name' => 'John',
            ],
            'active' => true,
        ];

        $config = new GeneratorConfig(keyQuotingStyle: KeyQuotingStyle::NoQuotes, indentSize: 4);
        $result = Hell2Shape::generate($data, $config);

        $expected = <<<'EXPECTED'
array{
    user: array{
        id: int,
        name: string
    },
    active: bool
}
EXPECTED;

        $this->assertSame($expected, $result);
    }

    public function testGenerateWithSingleQuotes(): void
    {
        $data = [
            'id' => 1,
            'name' => 'test',
        ];

        $config = new GeneratorConfig(keyQuotingStyle: KeyQuotingStyle::SingleQuotes, indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = "array{'id': int, 'name': string}";
        $this->assertSame($expected, $result);
    }

    public function testGenerateWithDoubleQuotes(): void
    {
        $data = [
            'id' => 1,
            'name' => 'test',
        ];

        $config = new GeneratorConfig(keyQuotingStyle: KeyQuotingStyle::DoubleQuotes, indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'array{"id": int, "name": string}';
        $this->assertSame($expected, $result);
    }

    public function testGenerateWithCustomIndent(): void
    {
        $data = [
            'user' => [
                'id' => 1,
            ],
        ];

        $config = new GeneratorConfig(keyQuotingStyle: KeyQuotingStyle::NoQuotes, indentSize: 2);
        $result = Hell2Shape::generate($data, $config);

        $expected = <<<'EXPECTED'
array{
  user: array{
    id: int
  }
}
EXPECTED;

        $this->assertSame($expected, $result);
    }

    public function testGenerateList(): void
    {
        $data = ['foo', 'bar', 'baz'];

        $config = new GeneratorConfig(indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'list<string>';
        $this->assertSame($expected, $result);
    }

    public function testGenerateMixedTypes(): void
    {
        $data = [
            'string' => 'text',
            'int' => 42,
            'float' => 3.14,
            'bool' => false,
            'null' => null,
        ];

        $config = new GeneratorConfig(indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'array{string: string, int: int, float: float, bool: bool, null: null}';
        $this->assertSame($expected, $result);
    }

    public function testGenerateEmptyArray(): void
    {
        $data = [];

        $config = new GeneratorConfig(indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'array';
        $this->assertSame($expected, $result);
    }

    public function testGenerateWithObject(): void
    {
        $user = new class() {
            public int $id = 1;

            public string $name = 'John';
        };

        $config = new GeneratorConfig(
            keyQuotingStyle: KeyQuotingStyle::NoQuotes,
            indentSize: 0,
            classNameStyle: ClassNameStyle::Unqualified
        );
        $result = Hell2Shape::generate($user, $config);

        // Anonymous objects are represented as 'object'
        $this->assertStringContainsString('object', $result);
    }

    public function testGenerateWithStdClass(): void
    {
        $data = new \stdClass();
        $data->id = 1;
        $data->name = 'John';

        $config = new GeneratorConfig(indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'object{id: int, name: string}';
        $this->assertSame($expected, $result);
    }

    public function testGenerateComplexNestedStructure(): void
    {
        $data = [
            'users' => [
                [
                    'id' => 1,
                    'name' => 'John',
                    'tags' => ['admin', 'user'],
                ],
                [
                    'id' => 2,
                    'name' => 'Jane',
                    'tags' => ['user'],
                ],
            ],
            'meta' => [
                'total' => 2,
                'page' => 1,
            ],
        ];

        $config = new GeneratorConfig(indentSize: 0);
        $result = Hell2Shape::generate($data, $config);

        $expected = 'array{users: list<array{id: int, name: string, tags: list<string>}>, meta: array{total: int, page: int}}';
        $this->assertSame($expected, $result);
    }
}
