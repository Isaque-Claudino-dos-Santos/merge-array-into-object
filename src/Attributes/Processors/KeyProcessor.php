<?php

namespace ISQ\MAIO\Attributes\Processors;

class KeyProcessor extends Processor
{
	private bool $keyExistsInData = false;
	private mixed $value = null;
	private ?string $key = null;

	public function __construct(
		private $data,
	) {
	}

	public function keyExists(): bool
	{
		return $this->keyExistsInData;
	}

	public function getValue(): mixed
	{
		return $this->value;
	}

	public function getKey(): ?string
	{
		return $this->key;
	}

	#[\Override]
	public function process(\ReflectionAttribute $attribute): void
	{
		$key = $attribute->getArguments()[0] ?? null;

		if (! $key) {
			return;
		}

		if (array_has_key($this->data, $key)) {
			$this->keyExistsInData = true;
			$this->key = $key;
			$this->value = array_get($this->data, $key);
		}
	}
}
