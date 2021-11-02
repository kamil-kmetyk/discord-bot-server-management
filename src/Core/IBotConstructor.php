<?php
namespace DarkFox\BotSM\Core;

use Discord\Discord;
use Discord\Slash\Client;

interface IBotConstructor
{
  public function __construct(Discord $discord, Client $client);

}
