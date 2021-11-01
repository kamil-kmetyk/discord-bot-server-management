<?php
namespace DarkFox\BotSM\Events\Core;

use DarkFox\BotSM\Core\BotConstructor;

abstract class EventBase extends BotConstructor implements IEvent
{
  abstract public function handle(): EventBase;

}
