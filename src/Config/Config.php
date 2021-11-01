<?php
namespace DarkFox\BotSM\Config;

use DarkFox\BotSM\Tools\Directory;
use Exception;
use ReflectionClass;

abstract class Config
{
  private array $config = [];

  /**
   * @throws Exception
   */
  public function __construct(protected ?string $fileName = null, protected string $configDir = 'config') {
    $this->setConfigFileName()->loadConfig()->assignConfiguration();
  }

  /**
   * If Config's file name isn't defined, then it should be generated using class name.
   * @return Config
   */
  private function setConfigFileName(): Config {
    if (is_null($this->fileName)) {
      $classReflection = new ReflectionClass($this);
      $this->fileName = str_replace(['config', 'Config'], '', lcfirst($classReflection->getShortName()));
      $this->fileName = mb_strtolower(preg_replace('/([A-Z])/', '-$0', $this->fileName)).'.json';
    }

    return $this;
  }

  /**
   * Load config file.
   *
   * @return Config
   * @throws Exception
   */
  private function loadConfig(): Config {
    $configDir = join(DIRECTORY_SEPARATOR, [
      rtrim((new Directory)->project(), DIRECTORY_SEPARATOR),
      $this->configDir ?? 'config',
      $this->fileName,
    ]);

    if (!file_exists($configDir)) {
      throw new Exception(sprintf('Expected "%s" config file does not exists.', $configDir));
    }

    if (!is_readable($configDir)) {
      throw new Exception(sprintf('The "%s" config file is not readable.', $configDir));
    }

    $config = json_decode(file_get_contents($configDir), true);

    if (is_null($config)) {
      throw new Exception(sprintf('The "%s" config file is not a valid JSON. Error: %s', $configDir, json_last_error_msg()));
    }

    $this->config = $config;

    return $this;
  }

  /**
   * Assign values from configuration file to class variables.
   */
  private function assignConfiguration(): void {
    if (0 < count($this->config)) {
      $fields = get_class_vars(get_class($this));

      foreach ($this->config as $field => $value) {
        if (array_key_exists($field, $fields)) {
          $this->{$field} = $value;
        } else {
          // todo debugger: field doesn't exits
        }
      }
    } else {
      // todo debugger: empty config
    }
  }

}
