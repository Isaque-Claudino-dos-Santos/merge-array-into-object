<?php

namespace MAIO\Tests\MergeArrayIntoObject;

use Illuminate\Support\Str;
use MAIO\Attributes\Call;
use MAIO\Attributes\Key;
use MAIO\Exceptions\KeyInArrayNotFoundException;
use MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

class MergeArrayIntoObjectTest extends TestCase
{

    #[Test]
    #[Ticket('#1')]
    public function it_should_call_static_method_on_static_call_is_present_in_property_successfully()
    {
        $object = new class {
            #[Key('full_name')]
            #[Call(Str::class, 'snake')]
            #[Call(Str::class, 'upper')]
            public string $name;

            public static function toUpperCase(string $value): string
            {
                return strtoupper($value);
            }
        };

        $array = ['full_name' => 'john doe'];

        $maio = new MergeArrayIntoObject();
        $mergedObject = $maio->merge($object, $array);
        $this->assertEquals('JOHN_DOE', $mergedObject->name);
    }

    #[Test]
    #[Ticket('#5')]
    public function it_should_return_default_value_on_not_found_key_in_array_successfully()
    {
        $object = new class {
            public string $name = 'Marry Jane';
        };

        $array = ['full_name' => 'john doe'];

        $maio = new MergeArrayIntoObject();
        $mergedObject = $maio->merge($object, $array);

        $this->assertEquals('Marry Jane', $mergedObject->name);
    }

    #[Test]
    #[Ticket('#9')]
    public function it_should_throw_key_in_array_not_found_on_set_define_key_not_existent_in_class_and_without_default_value_with_error()
    {
        $this->expectException(KeyInArrayNotFoundException::class);

        $object = new class {
            public string $name;
        };

        $array = ['age' => 12];

        $maio = new MergeArrayIntoObject();
        $mergedObject = $maio->merge($object, $array);
    }
}
