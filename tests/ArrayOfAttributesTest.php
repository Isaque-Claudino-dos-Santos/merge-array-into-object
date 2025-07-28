<?php

namespace ISQ\MAIO\Tests\MergeArrayIntoObject;

use Exception;
use ISQ\MAIO\Attributes\ArrayOf;
use ISQ\MAIO\MergeArrayIntoObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;

class User
{
    public string $name;
}

class ArrayOfAttributesTest extends TestCase
{
    #[Test]
    #[Ticket('#22')]
    public function it_should_transform_array_value_in_object_passed_on_define_attribute_array_of_successfully()
    {
        $target = new class {
            /** @var array<User> */
            #[ArrayOf(User::class)]
            public array $users;
        };

        $data = [
            'users' => [
                ['name' => 'Mick'],
                ['name' => 'Michael'],
                ['name' => 'Isaque'],
                ['name' => 'Pablo'],
            ],
        ];

        $result = (new MergeArrayIntoObject())->merge($target, $data);

        $this->assertEquals('Mick', $result->users[0]->name);
        $this->assertEquals('Michael', $result->users[1]->name);
        $this->assertEquals('Isaque', $result->users[2]->name);
        $this->assertEquals('Pablo', $result->users[2]->name);
    }

    #[Test]
    #[Ticket('#22')]
    public function it_should_return_expected_array_property_to_use_array_of_attribute_on_use_array_of_in_property_with_error()
    {
        $this->expectException(Exception::class);

        $target = new class {
            /** @var array<User> */
            #[ArrayOf(User::class)]
            public string $users;
        };

        $data = [
            'users' => [
                ['name' => 'Mick'],
                ['name' => 'Michael'],
                ['name' => 'Isaque'],
                ['name' => 'Pablo'],
            ],
        ];

        (new MergeArrayIntoObject())->merge($target, $data);
    }

    #[Test]
    #[Ticket('#22')]
    public function it_should_return_expected_array_data_received_on_use_array_of_in_property_with_error()
    {
        $this->expectException(Exception::class);

        $target = new class {
            /** @var array<User> */
            #[ArrayOf(User::class)]
            public array $users;
        };

        $data = [
            'users' => 'hello error',
        ];

        (new MergeArrayIntoObject())->merge($target, $data);
    }
}
