<?php

namespace Tests;

use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

class ObjectPropertyAndArrayKeyExactTest extends TestCase
{
    #[Test]
    #[Ticket('#1')]
    #[DataProvider('propertyAndKeyNameEqualsDataProvider')]
    public function it_should_set_value_of_array_in_object_with_property_and_key_name_equals_successfully(object $object, array $array)
    {
        $maio = new MergeArrayIntoObject();

        $mergedObject = $maio->merge($object, $array);

        $this->assertEquals($array['property'], $mergedObject->property);
    }

    public static function propertyAndKeyNameEqualsDataProvider(): array
    {
        return [
            'string equals' => [
                'object' => new class {
            public string $property;
                },
                'array' => ['property' => 'John Doe'],
            ],
            'array equals' => [
                'object' => new class {
            public array $property;
                },
                'array' => ['property' => [1, 2, 3]],
            ],
            'int equals' => [
                'object' => new class {
            public int $property;
                },
                'array' => ['property' => 1],
            ],
            'object equals' => [
                'object' => new class {
            public object $property;
                },
                'array' => ['property' => (object) ['name' => 'John Doe']],
            ],
            'boolean false equals' => [
                'object' => new class {
            public bool $property;
                },
                'array' => ['property' => false],
            ],
            'boolean true equals' => [
                'object' => new class {
            public bool $property;
                },
                'array' => ['property' => true],
            ],
        ];
    }
}