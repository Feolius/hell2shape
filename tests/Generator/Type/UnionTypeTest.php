<?php

namespace Feolius\Hell2Shape\Tests\Generator\Type;

use Feolius\Hell2Shape\Generator\Type\HashmapKey;
use Feolius\Hell2Shape\Generator\Type\HashmapType;
use Feolius\Hell2Shape\Generator\Type\ListType;
use Feolius\Hell2Shape\Generator\Type\ObjectType;
use Feolius\Hell2Shape\Generator\Type\ScalarType;
use Feolius\Hell2Shape\Generator\Type\StdObjectType;
use Feolius\Hell2Shape\Generator\Type\UnionType;
use PHPUnit\Framework\TestCase;

final class UnionTypeTest extends TestCase
{
    public function testDeduplicateScalarTypes(): void
    {
        $int1 = new ScalarType('int');
        $int2 = new ScalarType('int');
        $string1 = new ScalarType('string');

        $union = new UnionType([$int1, $int2, $string1]);

        $this->assertSame('int|string', $union->toString());
    }

    public function testDeduplicateMultipleScalarTypes(): void
    {
        $int1 = new ScalarType('int');
        $int2 = new ScalarType('int');
        $int3 = new ScalarType('int');
        $string1 = new ScalarType('string');
        $string2 = new ScalarType('string');
        $bool1 = new ScalarType('bool');

        $union = new UnionType([$int1, $string1, $int2, $bool1, $string2, $int3]);

        $this->assertSame('int|string|bool', $union->toString());
    }

    public function testMergeSingleHashmapType(): void
    {
        $hashmap1 = new HashmapType([
            new HashmapKey('id', new ScalarType('int')),
            new HashmapKey('name', new ScalarType('string')),
        ]);

        $hashmap2 = new HashmapType([
            new HashmapKey('id', new ScalarType('int')),
            new HashmapKey('email', new ScalarType('string')),
        ]);

        $union = new UnionType([$hashmap1, $hashmap2]);

        $result = $union->toString();
        $this->assertSame('array{id: int, name?: string, email?: string}', $result);
    }

    public function testMergeMultipleHashmapTypes(): void
    {
        $hashmap1 = new HashmapType([
            new HashmapKey('a', new ScalarType('int')),
        ]);

        $hashmap2 = new HashmapType([
            new HashmapKey('b', new ScalarType('string')),
        ]);

        $hashmap3 = new HashmapType([
            new HashmapKey('c', new ScalarType('bool')),
        ]);

        $union = new UnionType([$hashmap1, $hashmap2, $hashmap3]);

        $result = $union->toString();
        $this->assertSame('array{a?: int, b?: string, c?: bool}', $result);
    }

    public function testMergeSingleListType(): void
    {
        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));

        $union = new UnionType([$list1, $list2]);

        $result = $union->toString();
        $this->assertSame('list<int|string>', $result);
    }

    public function testMergeMultipleListTypes(): void
    {
        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));
        $list3 = new ListType(new ScalarType('bool'));

        $union = new UnionType([$list1, $list2, $list3]);

        $result = $union->toString();
        $this->assertSame('list<int|string|bool>', $result);
    }

    public function testMergeSingleStdObjectType(): void
    {
        $obj1 = new StdObjectType([
            new HashmapKey('id', new ScalarType('int')),
            new HashmapKey('name', new ScalarType('string')),
        ]);

        $obj2 = new StdObjectType([
            new HashmapKey('id', new ScalarType('int')),
            new HashmapKey('email', new ScalarType('string')),
        ]);

        $union = new UnionType([$obj1, $obj2]);

        $result = $union->toString();
        $this->assertSame('object{id: int, name?: string, email?: string}', $result);
    }

    public function testMergeMultipleStdObjectTypes(): void
    {
        $obj1 = new StdObjectType([
            new HashmapKey('a', new ScalarType('int')),
        ]);

        $obj2 = new StdObjectType([
            new HashmapKey('b', new ScalarType('string')),
        ]);

        $obj3 = new StdObjectType([
            new HashmapKey('c', new ScalarType('bool')),
        ]);

        $union = new UnionType([$obj1, $obj2, $obj3]);

        $result = $union->toString();
        $this->assertSame('object{a?: int, b?: string, c?: bool}', $result);
    }

    public function testUnpackNestedUnionTypes(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');

        $innerUnion = new UnionType([$int, $string]);
        $outerUnion = new UnionType([$innerUnion, $bool]);

        $result = $outerUnion->toString();
        $this->assertSame('int|string|bool', $result);
    }

    public function testUnpackDeeplyNestedUnionTypes(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');
        $float = new ScalarType('float');

        $union1 = new UnionType([$int, $string]);
        $union2 = new UnionType([$union1, $bool]);
        $union3 = new UnionType([$union2, $float]);

        $result = $union3->toString();
        $this->assertSame('int|string|bool|float', $result);
    }

    public function testUnpackMultipleNestedUnionTypes(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');
        $float = new ScalarType('float');

        $union1 = new UnionType([$int, $string]);
        $union2 = new UnionType([$bool, $float]);
        $outerUnion = new UnionType([$union1, $union2]);

        $result = $outerUnion->toString();
        $this->assertSame('int|string|bool|float', $result);
    }

    public function testMixedTypesWithScalarsAndHashmap(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $hashmap1 = new HashmapType([
            new HashmapKey('id', new ScalarType('int')),
        ]);

        $hashmap2 = new HashmapType([
            new HashmapKey('name', new ScalarType('string')),
        ]);

        $union = new UnionType([$int, $hashmap1, $string, $hashmap2]);

        $result = $union->toString();
        $this->assertSame('int|string|array{id?: int, name?: string}', $result);
    }

    public function testMixedTypesWithScalarsAndList(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));

        $union = new UnionType([$int, $list1, $string, $list2]);

        $result = $union->toString();
        $this->assertSame('int|string|list<int|string>', $result);
    }

    public function testMixedTypesWithScalarsAndStdObject(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $obj1 = new StdObjectType([
            new HashmapKey('id', new ScalarType('int')),
        ]);

        $obj2 = new StdObjectType([
            new HashmapKey('name', new ScalarType('string')),
        ]);

        $union = new UnionType([$int, $obj1, $string, $obj2]);

        $result = $union->toString();
        $this->assertSame('int|string|object{id?: int, name?: string}', $result);
    }

    public function testComplexMixedTypes(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');

        $hashmap = new HashmapType([
            new HashmapKey('x', new ScalarType('int')),
        ]);

        $list = new ListType(new ScalarType('string'));

        $obj = new StdObjectType([
            new HashmapKey('y', new ScalarType('bool')),
        ]);

        $union = new UnionType([$int, $hashmap, $string, $list, $bool, $obj]);

        $result = $union->toString();
        $this->assertSame('int|string|bool|array{x: int}|list<string>|object{y: bool}', $result);
    }

    public function testNestedUnionWithHashmapMerging(): void
    {
        $hashmap1 = new HashmapType([
            new HashmapKey('a', new ScalarType('int')),
        ]);

        $hashmap2 = new HashmapType([
            new HashmapKey('b', new ScalarType('string')),
        ]);

        $innerUnion = new UnionType([$hashmap1, $hashmap2]);

        $hashmap3 = new HashmapType([
            new HashmapKey('c', new ScalarType('bool')),
        ]);

        $outerUnion = new UnionType([$innerUnion, $hashmap3]);

        $result = $outerUnion->toString();
        $this->assertSame('array{a?: int, b?: string, c?: bool}', $result);
    }

    public function testNestedUnionWithListMerging(): void
    {
        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));

        $innerUnion = new UnionType([$list1, $list2]);

        $list3 = new ListType(new ScalarType('bool'));

        $outerUnion = new UnionType([$innerUnion, $list3]);

        $result = $outerUnion->toString();
        $this->assertSame('list<int|string|bool>', $result);
    }

    public function testNestedUnionWithStdObjectMerging(): void
    {
        $obj1 = new StdObjectType([
            new HashmapKey('a', new ScalarType('int')),
        ]);

        $obj2 = new StdObjectType([
            new HashmapKey('b', new ScalarType('string')),
        ]);

        $innerUnion = new UnionType([$obj1, $obj2]);

        $obj3 = new StdObjectType([
            new HashmapKey('c', new ScalarType('bool')),
        ]);

        $outerUnion = new UnionType([$innerUnion, $obj3]);

        $result = $outerUnion->toString();
        $this->assertSame('object{a?: int, b?: string, c?: bool}', $result);
    }

    public function testMergeMethodWithScalarType(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $union = new UnionType([$int]);
        $merged = $union->merge($string);

        $this->assertSame('int|string', $merged->toString());
    }

    public function testMergeMethodWithUnionType(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');

        $union1 = new UnionType([$int, $string]);
        $union2 = new UnionType([$bool]);

        $merged = $union1->merge($union2);

        $this->assertSame('int|string|bool', $merged->toString());
    }

    public function testMergeMethodWithHashmapType(): void
    {
        $int = new ScalarType('int');

        $hashmap = new HashmapType([
            new HashmapKey('id', new ScalarType('int')),
        ]);

        $union = new UnionType([$int]);
        $merged = $union->merge($hashmap);

        $result = $merged->toString();
        $this->assertSame('int|array{id: int}', $result);
    }

    public function testHashmapWithDifferentValueTypes(): void
    {
        $hashmap1 = new HashmapType([
            new HashmapKey('id', new ScalarType('int')),
        ]);

        $hashmap2 = new HashmapType([
            new HashmapKey('id', new ScalarType('string')),
        ]);

        $union = new UnionType([$hashmap1, $hashmap2]);

        $result = $union->toString();
        $this->assertSame('array{id: int|string}', $result);
    }

    public function testListWithNestedHashmaps(): void
    {
        $hashmap1 = new HashmapType([
            new HashmapKey('a', new ScalarType('int')),
        ]);

        $hashmap2 = new HashmapType([
            new HashmapKey('b', new ScalarType('string')),
        ]);

        $list1 = new ListType($hashmap1);
        $list2 = new ListType($hashmap2);

        $union = new UnionType([$list1, $list2]);

        $result = $union->toString();
        $this->assertSame('list<array{a?: int, b?: string}>', $result);
    }

    public function testStdObjectWithDifferentValueTypes(): void
    {
        $obj1 = new StdObjectType([
            new HashmapKey('id', new ScalarType('int')),
        ]);

        $obj2 = new StdObjectType([
            new HashmapKey('id', new ScalarType('string')),
        ]);

        $union = new UnionType([$obj1, $obj2]);

        $result = $union->toString();
        $this->assertSame('object{id: int|string}', $result);
    }

    public function testComplexNestedStructure(): void
    {
        // Create nested hashmaps
        $innerHashmap1 = new HashmapType([
            new HashmapKey('x', new ScalarType('int')),
        ]);

        $innerHashmap2 = new HashmapType([
            new HashmapKey('y', new ScalarType('string')),
        ]);

        $outerHashmap1 = new HashmapType([
            new HashmapKey('nested', $innerHashmap1),
        ]);

        $outerHashmap2 = new HashmapType([
            new HashmapKey('nested', $innerHashmap2),
        ]);

        $union = new UnionType([$outerHashmap1, $outerHashmap2]);

        $result = $union->toString();
        $this->assertSame('array{nested: array{x?: int, y?: string}}', $result);
    }

    public function testAllScalarTypesDeduplicated(): void
    {
        $types = [
            new ScalarType('int'),
            new ScalarType('string'),
            new ScalarType('int'),
            new ScalarType('bool'),
            new ScalarType('string'),
            new ScalarType('float'),
            new ScalarType('int'),
            new ScalarType('null'),
            new ScalarType('bool'),
        ];

        $union = new UnionType($types);

        $result = $union->toString();
        $this->assertSame('int|string|bool|float|null', $result);
    }

    public function testDeeplyNestedHashmapMergingInList(): void
    {
        // First hashmap: { user: { profile: { name: string } } }
        $innerHashmap1 = new HashmapType([
            new HashmapKey('name', new ScalarType('string')),
        ]);

        $middleHashmap1 = new HashmapType([
            new HashmapKey('profile', $innerHashmap1),
        ]);

        $outerHashmap1 = new HashmapType([
            new HashmapKey('user', $middleHashmap1),
        ]);

        // Second hashmap: { user: { profile: { age: int } } }
        $innerHashmap2 = new HashmapType([
            new HashmapKey('age', new ScalarType('int')),
        ]);

        $middleHashmap2 = new HashmapType([
            new HashmapKey('profile', $innerHashmap2),
        ]);

        $outerHashmap2 = new HashmapType([
            new HashmapKey('user', $middleHashmap2),
        ]);

        // Third hashmap: { user: { settings: { theme: string } } }
        $innerHashmap3 = new HashmapType([
            new HashmapKey('theme', new ScalarType('string')),
        ]);

        $middleHashmap3 = new HashmapType([
            new HashmapKey('settings', $innerHashmap3),
        ]);

        $outerHashmap3 = new HashmapType([
            new HashmapKey('user', $middleHashmap3),
        ]);

        // Create list of these hashmaps
        $list1 = new ListType($outerHashmap1);
        $list2 = new ListType($outerHashmap2);
        $list3 = new ListType($outerHashmap3);

        $union = new UnionType([$list1, $list2, $list3]);

        $result = $union->toString();
        $this->assertSame(
            'list<array{user: array{profile?: array{name?: string, age?: int}, settings?: array{theme: string}}}>',
            $result
        );
    }

    public function testDeduplicateMultipleObjectTypes(): void
    {
        $user1 = new ObjectType('App\\Models\\User');
        $user2 = new ObjectType('App\\Models\\User');
        $user3 = new ObjectType('App\\Models\\User');
        $post1 = new ObjectType('App\\Models\\Post');
        $post2 = new ObjectType('App\\Models\\Post');
        $comment = new ObjectType('App\\Models\\Comment');

        $union = new UnionType([$user1, $post1, $user2, $comment, $post2, $user3]);

        $result = $union->toString();
        $this->assertSame('App\\Models\\User|App\\Models\\Post|App\\Models\\Comment', $result);
    }
}
