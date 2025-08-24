# Merge Array Into Object

[![Testes](https://github.com/Isaque-Claudino-dos-Santos/merge-array-into-object/actions/workflows/php-tests.yml/badge.svg)](https://github.com/Isaque-Claudino-dos-Santos/merge-array-into-object/actions/workflows/php-tests.yml)

## Introdução

Pacote `composer` para mesclar um `array` em uma `class/object` de forma padronizada.

### Instalação

```bash
composer require isq/maio
```

## Básico

Para todos o exemplo vamos usar a classe `CreateUserDTO` e `CreateUserAddressDTO`.

### Mesclando Dados de um `Array` para `Class/Object`

Para isso você deve usar o método `merge` da classe `ISQ\MAIO\MergeArrayIntoObject`.

```php
use ISQ\MAIO\MergeArrayIntoObject;

class CreateUserDTO {
	public readonly string $firstName;
	public readonly string $lastName;
}

$data = [
	'firstName' => 'Foo',
	'lastName' => 'Baz'
];

$maio = MergeArrayIntoObject::getInstance();
$createUserDTO = $maio->merge(new CreateUserDTO, $data);

echo $createUserDTO::class; // CreateUserDTO
echo $createUserDTO->firstName; // Foo
echo $createUserDTO->lastName; // Baz
```

Perceba que todos os dados de `$data` foi mesclado conforme o nome dadas propriedades class classe, quando usado o método `merge` é retornado a instância da class `CreateUserDTO` com as propriedades preenchi.

### Definir Chave Externa com `#[Key]`

Tem casos que você tem um nome de propriedade que não combina com os dados recebidos ou estão em cadeia, para resolver isso é possível usar o atributo `ISQ\MAIO\Attributes\Key`.

```php
use ISQ\MAIO\MergeArrayIntoObject;
use ISQ\MAIO\Attributes\Key;

class CreateUserDTO {
	public readonly string $firstName;
	public readonly string $lastName;
	#[Key('user_age')]
	public readonly int $age;
	#[Key('address.postal_code')]
	public readonly int $postalCode;
}

$data = [
	'firstName' => 'Foo',
	'lastName' => 'Baz',
	'user_age' => 34,
	'address' => [
		'postal_code' => 12312311
	]
];

$maio = MergeArrayIntoObject::getInstance();
$createUserDTO = $maio->merge(new CreateUserDTO, $data);

echo $createUserDTO::class; // CreateUserDTO
echo $createUserDTO->firstName; // Foo
echo $createUserDTO->lastName; // Baz
echo $createUserDTO->age; // 34
echo $createUserDTO->postalCode; // 12312311
```

Perceba que as propriedades `$age` e `$postalcode` não combinam com o que tem dentro da variável `$data` então foi “renomeado” com o atributo `Key`.

> Quando é passado para `Key` o texto `address.postal_code` é referente a estrutura: 
`{ 
   ”address”: {
      “postal_code”: 12312311
   }
 }`
> 

### Executar Função em uma Propriedade com o Atributo `#[Call]`

Caso precise manipular o valor vindo de `$data` é possível usar o atributo `ISQ\MAIO\Attributes\Call`.

```php
use ISQ\MAIO\MergeArrayIntoObject;
use ISQ\MAIO\Attributes\Call;

class MyHelper {
	public static function add(int $value): int
	{
		return $value + 1;
	}
	
	public function toUpperCase(string $value): string
	{
		return strtoupper($value);
	}
}

class CreateUserDTO {
	#[Call(MyHelper::class, 'add')]
	public readonly int $id;
	
	#[Call('strtoupper')]
	public readonly string $firstName;
	
	#[Call(new MyHelper, 'toUpperCase')]
	public readonly string $lastName;
}

$data = [
	'id' => 0,
	'firstName' => 'Foo',
	'lastName' => 'Baz'
];

$maio = MergeArrayIntoObject::getInstance();
$createUserDTO = $maio->merge(new CreateUserDTO, $data);

echo $createUserDTO::class; // CreateUserDTO
echo $createUserDTO->id; // 1
echo $createUserDTO->firstName; // FOO
echo $createUserDTO->lastName; // BAZ
```

Perceba que o assim que o valor `id` de `$data` é recuperado, a propriedade `$id` usa o atributo `Call` para invocar o método `add` estático dentro de `MyHelper` classe, assim fazendo a repassando o valor de `id` dentro de `$data` para o método e atribuindo o retorno a propriedade `$id`.

### Definindo Listas com `ArrayOf`

Para definir uma lista de itens para uma class especifica é possível com `ISQ\MAIO\Attributes\ArrayOf`.

```php
use ISQ\MAIO\MergeArrayIntoObject;
use ISQ\MAIO\Attributes\ArrayOf;

class User {
	public readonly string $id;
}

class UsersDTO {
	#[ArrayOf(User::class)]
	public readonly array $data;
}

$data = [
	'data' => [
		['id' => 1],
		['id' => 2],
		['id' => 3],
		['id' => 4]
	]
];

$maio = MergeArrayIntoObject::getInstance();
$usersDTO = $maio->merge(new CreateUserDTO, $data);

echo $createUserDTO::class; // UsersDTO
echo $usersDTO[0]::class; // User
echo $usersDTO[0]->id; // 1
echo $usersDTO[1]->id; // 2
echo $usersDTO[2]->id; // 3
echo $usersDTO[3]->id; // 4
```

Perceba que a lista dentro `$data` é transformada em uma lista da classe `User`.
