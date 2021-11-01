<?php
namespace DarkFox\BotSM\Core;

use Discord\Discord;
use Discord\Slash\Client;

abstract class BotConstructor implements IBotConstructor
{
  public function __construct(protected Discord $discord, protected Client $client) { }

}
