<?php

namespace App\Tests\Generator\Type;

use App\Generator\KeyQuotingStyle;
use App\Generator\Type\HashmapType;
use App\Generator\Type\ListType;
use App\Generator\Type\ScalarType;
use App\Generator\Type\StdObjectType;
use App\Generator\Type\UnionType;
use PHPUnit\Framework\TestCase;

final class UnionTypeTest extends TestCase
{
    public function testDeduplicateScalarTypes(): void
    {
        $int1 = new ScalarType('int');
        $int2 = new ScalarType('int');
        $string1 = new ScalarType('string');

        $union = new UnionType([$int1, $int2, $string1]);

        $this->assertSame('int|string', $union->toString(KeyQuotingStyle::NoQuotes));
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

        $this->assertSame('int|string|bool', $union->toString(KeyQuotingStyle::NoQuotes));
    }

    public function testMergeSingleHashmapType(): void
    {
        $hashmap1 = new HashmapType();
        $hashmap1->addKey('id', new ScalarType('int'));
        $hashmap1->addKey('name', new ScalarType('string'));

        $hashmap2 = new HashmapType();
        $hashmap2->addKey('id', new ScalarType('int'));
        $hashmap2->addKey('email', new ScalarType('string'));

        $union = new UnionType([$hashmap1, $hashmap2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('array{id: int, name?: string, email?: string}', $result);
    }

    public function testMergeMultipleHashmapTypes(): void
    {
        $hashmap1 = new HashmapType();
        $hashmap1->addKey('a', new ScalarType('int'));

        $hashmap2 = new HashmapType();
        $hashmap2->addKey('b', new ScalarType('string'));

        $hashmap3 = new HashmapType();
        $hashmap3->addKey('c', new ScalarType('bool'));

        $union = new UnionType([$hashmap1, $hashmap2, $hashmap3]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('array{a?: int, b?: string, c?: bool}', $result);
    }

    public function testMergeSingleListType(): void
    {
        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));

        $union = new UnionType([$list1, $list2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('list<int|string>', $result);
    }

    public function testMergeMultipleListTypes(): void
    {
        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));
        $list3 = new ListType(new ScalarType('bool'));

        $union = new UnionType([$list1, $list2, $list3]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('list<int|string|bool>', $result);
    }

    public function testMergeSingleStdObjectType(): void
    {
        $obj1 = new StdObjectType();
        $obj1->addKey('id', new ScalarType('int'));
        $obj1->addKey('name', new ScalarType('string'));

        $obj2 = new StdObjectType();
        $obj2->addKey('id', new ScalarType('int'));
        $obj2->addKey('email', new ScalarType('string'));

        $union = new UnionType([$obj1, $obj2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('object{id: int, name?: string, email?: string}', $result);
    }

    public function testMergeMultipleStdObjectTypes(): void
    {
        $obj1 = new StdObjectType();
        $obj1->addKey('a', new ScalarType('int'));

        $obj2 = new StdObjectType();
        $obj2->addKey('b', new ScalarType('string'));

        $obj3 = new StdObjectType();
        $obj3->addKey('c', new ScalarType('bool'));

        $union = new UnionType([$obj1, $obj2, $obj3]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('object{a?: int, b?: string, c?: bool}', $result);
    }

    public function testUnpackNestedUnionTypes(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');

        $innerUnion = new UnionType([$int, $string]);
        $outerUnion = new UnionType([$innerUnion, $bool]);

        $result = $outerUnion->toString(KeyQuotingStyle::NoQuotes);
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

        $result = $union3->toString(KeyQuotingStyle::NoQuotes);
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

        $result = $outerUnion->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('int|string|bool|float', $result);
    }

    public function testMixedTypesWithScalarsAndHashmap(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $hashmap1 = new HashmapType();
        $hashmap1->addKey('id', new ScalarType('int'));

        $hashmap2 = new HashmapType();
        $hashmap2->addKey('name', new ScalarType('string'));

        $union = new UnionType([$int, $hashmap1, $string, $hashmap2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('int|string|array{id?: int, name?: string}', $result);
    }

    public function testMixedTypesWithScalarsAndList(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));

        $union = new UnionType([$int, $list1, $string, $list2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('int|string|list<int|string>', $result);
    }

    public function testMixedTypesWithScalarsAndStdObject(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $obj1 = new StdObjectType();
        $obj1->addKey('id', new ScalarType('int'));

        $obj2 = new StdObjectType();
        $obj2->addKey('name', new ScalarType('string'));

        $union = new UnionType([$int, $obj1, $string, $obj2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('int|string|object{id?: int, name?: string}', $result);
    }

    public function testComplexMixedTypes(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');

        $hashmap = new HashmapType();
        $hashmap->addKey('x', new ScalarType('int'));

        $list = new ListType(new ScalarType('string'));

        $obj = new StdObjectType();
        $obj->addKey('y', new ScalarType('bool'));

        $union = new UnionType([$int, $hashmap, $string, $list, $bool, $obj]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('int|string|bool|array{x: int}|list<string>|object{y: bool}', $result);
    }

    public function testNestedUnionWithHashmapMerging(): void
    {
        $hashmap1 = new HashmapType();
        $hashmap1->addKey('a', new ScalarType('int'));

        $hashmap2 = new HashmapType();
        $hashmap2->addKey('b', new ScalarType('string'));

        $innerUnion = new UnionType([$hashmap1, $hashmap2]);

        $hashmap3 = new HashmapType();
        $hashmap3->addKey('c', new ScalarType('bool'));

        $outerUnion = new UnionType([$innerUnion, $hashmap3]);

        $result = $outerUnion->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('array{a?: int, b?: string, c?: bool}', $result);
    }

    public function testNestedUnionWithListMerging(): void
    {
        $list1 = new ListType(new ScalarType('int'));
        $list2 = new ListType(new ScalarType('string'));

        $innerUnion = new UnionType([$list1, $list2]);

        $list3 = new ListType(new ScalarType('bool'));

        $outerUnion = new UnionType([$innerUnion, $list3]);

        $result = $outerUnion->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('list<int|string|bool>', $result);
    }

    public function testNestedUnionWithStdObjectMerging(): void
    {
        $obj1 = new StdObjectType();
        $obj1->addKey('a', new ScalarType('int'));

        $obj2 = new StdObjectType();
        $obj2->addKey('b', new ScalarType('string'));

        $innerUnion = new UnionType([$obj1, $obj2]);

        $obj3 = new StdObjectType();
        $obj3->addKey('c', new ScalarType('bool'));

        $outerUnion = new UnionType([$innerUnion, $obj3]);

        $result = $outerUnion->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('object{a?: int, b?: string, c?: bool}', $result);
    }

    public function testMergeMethodWithScalarType(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');

        $union = new UnionType([$int]);
        $merged = $union->merge($string);

        $this->assertSame('int|string', $merged->toString(KeyQuotingStyle::NoQuotes));
    }

    public function testMergeMethodWithUnionType(): void
    {
        $int = new ScalarType('int');
        $string = new ScalarType('string');
        $bool = new ScalarType('bool');

        $union1 = new UnionType([$int, $string]);
        $union2 = new UnionType([$bool]);

        $merged = $union1->merge($union2);

        $this->assertSame('int|string|bool', $merged->toString(KeyQuotingStyle::NoQuotes));
    }

    public function testMergeMethodWithHashmapType(): void
    {
        $int = new ScalarType('int');

        $hashmap = new HashmapType();
        $hashmap->addKey('id', new ScalarType('int'));

        $union = new UnionType([$int]);
        $merged = $union->merge($hashmap);

        $result = $merged->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('int|array{id: int}', $result);
    }

    public function testHashmapWithDifferentValueTypes(): void
    {
        $hashmap1 = new HashmapType();
        $hashmap1->addKey('id', new ScalarType('int'));

        $hashmap2 = new HashmapType();
        $hashmap2->addKey('id', new ScalarType('string'));

        $union = new UnionType([$hashmap1, $hashmap2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('array{id: int|string}', $result);
    }

    public function testListWithNestedHashmaps(): void
    {
        $hashmap1 = new HashmapType();
        $hashmap1->addKey('a', new ScalarType('int'));

        $hashmap2 = new HashmapType();
        $hashmap2->addKey('b', new ScalarType('string'));

        $list1 = new ListType($hashmap1);
        $list2 = new ListType($hashmap2);

        $union = new UnionType([$list1, $list2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('list<array{a?: int, b?: string}>', $result);
    }

    public function testStdObjectWithDifferentValueTypes(): void
    {
        $obj1 = new StdObjectType();
        $obj1->addKey('id', new ScalarType('int'));

        $obj2 = new StdObjectType();
        $obj2->addKey('id', new ScalarType('string'));

        $union = new UnionType([$obj1, $obj2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('object{id: int|string}', $result);
    }

    public function testComplexNestedStructure(): void
    {
        // Create nested hashmaps
        $innerHashmap1 = new HashmapType();
        $innerHashmap1->addKey('x', new ScalarType('int'));

        $innerHashmap2 = new HashmapType();
        $innerHashmap2->addKey('y', new ScalarType('string'));

        $outerHashmap1 = new HashmapType();
        $outerHashmap1->addKey('nested', $innerHashmap1);

        $outerHashmap2 = new HashmapType();
        $outerHashmap2->addKey('nested', $innerHashmap2);

        $union = new UnionType([$outerHashmap1, $outerHashmap2]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
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

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame('int|string|bool|float|null', $result);
    }

    public function testDeeplyNestedHashmapMergingInList(): void
    {
        // First hashmap: { user: { profile: { name: string } } }
        $innerHashmap1 = new HashmapType();
        $innerHashmap1->addKey('name', new ScalarType('string'));

        $middleHashmap1 = new HashmapType();
        $middleHashmap1->addKey('profile', $innerHashmap1);

        $outerHashmap1 = new HashmapType();
        $outerHashmap1->addKey('user', $middleHashmap1);

        // Second hashmap: { user: { profile: { age: int } } }
        $innerHashmap2 = new HashmapType();
        $innerHashmap2->addKey('age', new ScalarType('int'));

        $middleHashmap2 = new HashmapType();
        $middleHashmap2->addKey('profile', $innerHashmap2);

        $outerHashmap2 = new HashmapType();
        $outerHashmap2->addKey('user', $middleHashmap2);

        // Third hashmap: { user: { settings: { theme: string } } }
        $innerHashmap3 = new HashmapType();
        $innerHashmap3->addKey('theme', new ScalarType('string'));

        $middleHashmap3 = new HashmapType();
        $middleHashmap3->addKey('settings', $innerHashmap3);

        $outerHashmap3 = new HashmapType();
        $outerHashmap3->addKey('user', $middleHashmap3);

        // Create list of these hashmaps
        $list1 = new ListType($outerHashmap1);
        $list2 = new ListType($outerHashmap2);
        $list3 = new ListType($outerHashmap3);

        $union = new UnionType([$list1, $list2, $list3]);

        $result = $union->toString(KeyQuotingStyle::NoQuotes);
        $this->assertSame(
            'list<array{user: array{profile?: array{name?: string, age?: int}, settings?: array{theme: string}}}>',
            $result
        );
    }
}
