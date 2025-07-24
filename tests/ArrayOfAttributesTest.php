<?php

namespace MAIO\Tests\MergeArrayIntoObject;

use MAIO\Attributes\ArrayOf;
use MAIO\MergeArrayIntoObject;
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
}
