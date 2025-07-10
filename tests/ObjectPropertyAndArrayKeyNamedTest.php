<?php


namespace MAIO\Tests\MergeArrayIntoObject;

use MAIO\Attributes\Key;
use MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

class ObjectPropertyAndArrayKeyNamedTest extends TestCase
{
    #[Test]
    #[Ticket('#1')]
    public function it_should_set_value_in_object_with_named_property_successfully()
    {
        $object = new class {
            #[Key('property_named_test')]
            public string $property;
        };

        $array = ['property_named_test' => 'John Doe'];

        $maio = new MergeArrayIntoObject();
        $mergedObject = $maio->merge($object, $array);

        $this->assertEquals($array['property_named_test'], $mergedObject->property);
    }
}