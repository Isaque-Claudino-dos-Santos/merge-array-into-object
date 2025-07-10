<?php

namespace MAIO\Tests\MergeArrayIntoObject;

use MAIO\Attributes\Key;
use MAIO\Attributes\StaticCall;
use MAIO\Exceptions\KeyInArrayNotFoundException;
use MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

class MergeArrayIntoObjectTest extends TestCase
{
    #[Test]
    #[Ticket('#1')]
    public function it_should_return_key_in_array_not_found_exception_when_key_not_found()
    {
        $this->expectException(KeyInArrayNotFoundException::class);

        $object = new class {
            public string $property;
        };

        $array = ['non_existent_property' => 'value'];

        $maio = new MergeArrayIntoObject();
        $maio->merge($object, $array);
    }

    #[Test]
    #[Ticket('#1')]
    public function it_should_call_static_method_on_static_call_is_present_in_property_successfully()
    {
        $object = new class {
            #[Key('full_name')]
            #[StaticCall('toUpperCase')]
            public string $name;

            public static function toUpperCase(string $value): string
            {
                return strtoupper($value);
            }
        };

        $array = ['full_name' => 'john doe'];

        $maio = new MergeArrayIntoObject();
        $mergedObject = $maio->merge($object, $array);

        $this->assertEquals(strtoupper($array['full_name']), $mergedObject->name);
    }
}