<?php
namespace DarkFox\BotSM\Events\Core;

use Discord\Discord;
use Discord\Slash\Client;

interface IEvent {
  public function __construct(Discord $discord, Client $client);
  public function handle(): EventBase;

}
