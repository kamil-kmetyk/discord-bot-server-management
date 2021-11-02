<?php
namespace DarkFox\BotSM\Events\Core;

use DarkFox\BotSM\Events\Guild\Guild;
use DarkFox\BotSM\Events\Message\Message;
use DarkFox\BotSM\Tools\Factory;

class EventFactory extends Factory
{
  protected string|bool $implementationName = IEvent::class;
  protected array $implementations = [
    Guild::class,
    Message::class,
  ];

  public function initializeHandlers(... $args): void {
    foreach ($this->implementations as $implementation) {
      /** @var IEvent $instance */
      $instance = $this->create($implementation, $args);
      $instance->handle();
    }
  }

}
