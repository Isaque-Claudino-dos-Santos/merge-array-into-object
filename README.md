# Merge Array Into Object (MAIO)

A package to merge arrays into objects in PHP.

## Installation

```bash
composer require isq/maio
```

## Basic Usage

```php
use MAIO\MergeArrayIntoObject;

class CreateUserDTO {
    public string $firstName;
    public string $lastName;
    public string $email;
    public int $age;
}

$data = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'email' => 'john.doe@example.com',
    'age' => 30,
];

$dto = new MergeArrayIntoObject()->merge(new CreateUserDTO, $data);

print_r($dto);

// Output:
// CreateUserDTO Object
// (
//     [firstName] => John
//     [lastName] => Doe
//     [email] => john.doe@example.com
//     [age] => 30
// )

```

## Define Key to Merge

You can define the key to merge using the `Key` attribute.

```php
use MAIO\MergeArrayIntoObject;

class CreateUserDTO {
    #[Key('first_name')]
    public string $firstName;
    public string $lastName;
    public string $email;
    public int $age;
}

$data = [
    'first_name' => 'John',
    'lastName' => 'Doe',
    'email' => 'john.doe@example.com',
    'age' => 30,
];

$dto = new MergeArrayIntoObject()->merge(new CreateUserDTO, $data);

print_r($dto);

// Output:
// CreateUserDTO Object
// (
//     [firstName] => John
//     [lastName] => Doe
//     [email] => john.doe@example.com
//     [age] => 30
// )
```

## Define Static Method to Merge

You can call method static of the type defined in property with attribute `StaticCall`. 

```php
use MAIO\MergeArrayIntoObject;

class UserModel {
    public static function find(int $id): UserModel
    {
        // Find user by id
    }
}

class CreateUserDTO {
    #[Key('user_id')]
    #[StaticCall('find')]
    public UserModel $user;
}


$data = [
    'user_id' => 1,
];

$dto = new MergeArrayIntoObject()->merge(new CreateUserDTO, $data);

print_r($dto);

// Output:
// CreateUserDTO Object
// (
//     [user] => UserModel Object
// )
```
