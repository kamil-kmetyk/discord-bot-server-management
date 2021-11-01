<?php
namespace DarkFox\BotSM\Core;

use DarkFox\BotSM\Config\DiscordConfig;
use Discord\Discord;
use Discord\Slash\Client;

final class Bot
{
  protected DiscordConfig $config;
  protected Discord $discord;
  protected Client $client;

  public function __construct() {
    $this->config = new DiscordConfig;
    $this->discord = new Discord([ 'token' => $this->config->token ]);
    $this->client = new Client([ 'loop' => $this->discord->getLoop() ]);

    $this->client->linkDiscord($this->discord);

    $this->setup();
  }

  public function __destruct() {
    $this->discord->run();
  }

  protected function setup() {

  }

  protected function handleEvents() {

  }

}
