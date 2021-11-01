<?php
namespace DarkFox\BotSM\Tools;

use Exception;
use ReflectionClass;
use ReflectionException;

class Directory
{
  public const VENDOR = 'vendor';
  public const PROJECT = 'src';

  /**
   * @param object|null $instance An instance of checked class.
   */
  public function __construct(protected ?object $instance = null) {
    if (is_null($this->instance)) {
      $this->instance = $this;
    }
  }

  /**
   * Return an absolute path to project.
   *
   * @return string
   * @throws Exception
   */
  public function project(): string {
    $reflection = new ReflectionClass($this->instance);
    $classPath = $reflection->getFileName();
    $position = mb_strpos($classPath, static::VENDOR);

    if (false === $position) {
      $position = mb_strpos($classPath, static::PROJECT);
    }

    if (false === $position) {
      throw new Exception(sprintf('Directories %s or %s must appear in path.', static::VENDOR, static::PROJECT));
    }

    return mb_substr($classPath, 0, $position);
  }

  /**
   * Return class directory.
   *
   * @return string
   * @throws ReflectionException
   */
  public function class(): string {
    $reflection = new ReflectionClass($this->instance);
    return dirname($reflection->getFileName());
  }

  /**
   * Get relative path to the class.
   *
   * @return string
   * @throws Exception
   */
  public function relative(): string {
    $className = get_class($this->instance);
    $classNamespaces = explode('\\', $className);

    if (isset($classNamespaces[0])) {
      $classNamespaces[0] = static::PROJECT;
      array_pop($classNamespaces);
      return join(DIRECTORY_SEPARATOR, $classNamespaces);
    } else {
      throw new Exception(sprintf('Given class name "%s" has no namespace.', $className));
    }
  }

}
