<?php
namespace DarkFox\BotSM\Events\Core;

use DarkFox\BotSM\Events\Guild;
use DarkFox\BotSM\Tools\Factory;

class EventFactory extends Factory
{
  protected string|bool $implementationName = IEvent::class;
  protected array $implementations = [
    Guild::class,
  ];

  public function initializeHandlers(... $args): void {
    /** @var IEvent $implementation */
    foreach ($this->implementations as $implementation) {
      $this->create($implementation, $args);
    }
  }

}
