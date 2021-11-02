<?php
namespace DarkFox\BotSM\Events\Guild;

use DarkFox\BotSM\Events\Core\EventBase;

class Guild extends EventBase
{
  public function handle(): EventBase {
    return $this;
  }

}
