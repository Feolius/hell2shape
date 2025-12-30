<?php

namespace Feolius\Hell2Shape\Tests\Parser;

use Feolius\Hell2Shape\Lexer\Lexer;
use Feolius\Hell2Shape\Parser\Node\AbstractNode;
use Feolius\Hell2Shape\Parser\Node\AnonymousObjectNode;
use Feolius\Hell2Shape\Parser\Node\FloatNode;
use Feolius\Hell2Shape\Parser\Node\HashmapItemNode;
use Feolius\Hell2Shape\Parser\Node\HashmapNode;
use Feolius\Hell2Shape\Parser\Node\IntNode;
use Feolius\Hell2Shape\Parser\Node\ListItemNode;
use Feolius\Hell2Shape\Parser\Node\ListNode;
use Feolius\Hell2Shape\Parser\Node\NullNode;
use Feolius\Hell2Shape\Parser\Node\ObjectNode;
use Feolius\Hell2Shape\Parser\Node\ResourceNode;
use Feolius\Hell2Shape\Parser\Node\StdObjectItemNode;
use Feolius\Hell2Shape\Parser\Node\StdObjectNode;
use Feolius\Hell2Shape\Parser\Node\StringNode;
use Feolius\Hell2Shape\Parser\Parser;
use Feolius\Hell2Shape\Tests\VarDumper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class A
{
    public string $prop;

    private readonly int $интProp;

    private $obj;

    private \stdClass $obj2;

    public function __construct(
        protected int $prop2
    ) {
        $this->интProp = PHP_INT_MAX;
        $this->obj = new class() {
            private string $test = 'sfsd';
        };
        $obj2 = new \stdClass();
        $obj2->prop1 = 5;
        $obj2->prop2 = [1];
        $this->obj2 = $obj2;
    }
}

final class ParserTest extends TestCase
{
    #[DataProvider('dumpProvider')]
    public function testParse(mixed $input, AbstractNode $expected): void
    {
        $dump = VarDumper::dump($input);
        $lexer = new Lexer();
        $tokens = $lexer->tokenize($dump);
        $parser = new Parser();
        $ast = $parser->parse($tokens);

        $this->assertEquals($expected, $ast);
    }

    public static function dumpProvider(): iterable
    {
        yield 'Simple Hashmap' => [
            'input' => [
                'foo' => 'bar',
                0 => 42,
                'baz' => null,
                1 => 4.5,
                'gut' => fopen('php://memory', 'r'),
            ],
            'expected' => new HashmapNode([
                new HashmapItemNode(new StringNode('foo'), new StringNode('bar')),
                new HashmapItemNode(new IntNode(0), new IntNode(42)),
                new HashmapItemNode(new StringNode('baz'), new NullNode()),
                new HashmapItemNode(new IntNode(1), new FloatNode(4.5)),
                new HashmapItemNode(new StringNode('gut'), new ResourceNode('stream')),
            ]),
        ];

        yield 'Simple List' => [
            'input' => ['bar', 42, null, 4.5, fopen('php://memory', 'r')],
            'expected' => new ListNode([
                new ListItemNode(new StringNode('bar')),
                new ListItemNode(new IntNode(42)),
                new ListItemNode(new NullNode()),
                new ListItemNode(new FloatNode(4.5)),
                new ListItemNode(new ResourceNode('stream')),
            ]),
        ];

        yield 'Inner array and std object' => [
            'input' => [
                'list' => ['foo', 'bar'],
                'object' => (object)['foo' => 'bar',
                    'baz' => 42],
            ],
            'expected' => new HashmapNode([
                new HashmapItemNode(
                    new StringNode('list'),
                    new ListNode([
                        new ListItemNode(new StringNode('foo')),
                        new ListItemNode(new StringNode('bar')),
                    ])
                ),
                new HashmapItemNode(
                    new StringNode('object'),
                    new StdObjectNode([
                        new StdObjectItemNode(new StringNode('foo'), new StringNode('bar')),
                        new StdObjectItemNode(new StringNode('baz'), new IntNode(42)),
                    ])
                ),
            ]),
        ];

        yield 'Array and object' => [
            'input' => [
                'foo' => new A(23),
            ],
            'expected' => new HashmapNode([
                new HashmapItemNode(
                    new StringNode('foo'),
                    new ObjectNode(A::class),
                ),
            ]),
        ];

        yield 'Nested anonymous objects' => [
            'input' => [
                'anon' => new class() {
                    private string $test = 'value';

                    private object $nested;

                    public function __construct()
                    {
                        $this->nested = new class() {
                            private string $inner = 'nested';
                        };
                    }
                },
            ],
            'expected' => new HashmapNode([
                new HashmapItemNode(
                    new StringNode('anon'),
                    new AnonymousObjectNode(),
                ),
            ]),
        ];
    }
}
