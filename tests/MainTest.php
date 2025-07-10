<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MergeArrayIntoObject\Attributes\Call;
use MergeArrayIntoObject\Attributes\Key;
use MergeArrayIntoObject\Attributes\StaticCall;
use MergeArrayIntoObject\MergeArrayIntoObject;

class User
{
    public static function find(int $id): User
    {
        return new User();
    }
}

class MyObject
{
    public string $name;
    public int $age;

    #[Key('user'), StaticCall('findOrFail')]
    public User $user;
}

class MainTest
{
    public function run()
    {



        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'user' => 12312321
        ];

        $myObject = new MergeArrayIntoObject()->merge(new MyObject(), $data);

        print_r($myObject);
    }
}

new MainTest()->run();