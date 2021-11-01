<?php
namespace DarkFox\BotSM\Tools;

abstract class Factory
{
  protected string|bool $implementationName = false;
  protected array $implementations = [];

  public function create(string $instanceName, ... $args): object|bool {
    if (!$this->canBeCreated($instanceName)) {
      return false;
    }

    $reflection = new \ReflectionClass($instanceName);
    return $reflection->newInstance($args);
  }

  protected function canBeCreated(string $instanceName): bool {
    if (!class_exists($instanceName)) {
      return false;
    }

    if (!in_array($instanceName, $this->implementations, true)) {
      return false;
    }

    if (is_string($this->implementationName) && !class_implements($this->implementationName)) {
      return false;
    }

    return true;
  }

}
