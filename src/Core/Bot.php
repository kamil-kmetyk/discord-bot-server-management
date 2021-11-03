<?php
namespace DarkFox\BotSM\Core;

use DarkFox\BotSM\Config\ChannelsConfig;
use DarkFox\BotSM\Config\DiscordConfig;
use DarkFox\BotSM\Config\GuildConfig;
use DarkFox\BotSM\Events\Core\EventFactory;
use Discord\Discord;
use Discord\Slash\Client;
use Discord\WebSockets\Intents;
use ReflectionClass;

final class Bot
{
  protected DiscordConfig $config;
  protected Discord $discord;
  protected Client $client;

  public function __construct() {
    $errors = $this->verifyConfigs();

    if (0 < count($errors)) {
      echo PHP_EOL.PHP_EOL;
      echo '[Error] Got some errors while trying to verify configs.'.PHP_EOL;
      print_r($errors);
      echo PHP_EOL.PHP_EOL;

      return;
    }

    $this->connect()->setup();
    $this->discord->run();
  }

  protected function setup(): Bot {
    $this->initializeEvents();

    return $this;
  }

  protected function connect(): Bot {
    $this->config = new DiscordConfig;
    $this->discord = new Discord([
      'token' => $this->config->token,
      'retrieveBans' => true,
      'loadAllMembers' => true,
      'storeMessages' => true,
       'intents' => Intents::getAllIntents(),
    ]);
    $this->client = new Client([ 'loop' => $this->discord->getLoop() ]);

    $this->client->linkDiscord($this->discord);

    return $this;
  }

  protected function initializeEvents(): void {
    $this->discord->once('ready', function(Discord $discord) {
      (new EventFactory)->initializeHandlers($discord, $this->client);
    });
  }

  protected function verifyConfigs(): array {
    $errors = [];
    $configs = [
      DiscordConfig::class => [ 'token' => '' ],
      GuildConfig::class => [ 'id' => -1 ],
      ChannelsConfig::class => [ 'log' => -1, 'notifications' => -1 ],
    ];

    foreach ($configs as $config => $properties) {
      $reflection = new ReflectionClass($config);

      foreach ($properties as $propertyName => $defaultValue) {
        if (!$reflection->hasProperty($propertyName)) {
          $errors[$config][] = sprintf('Required property "%s" does not exists.', $propertyName);
        } else {
          $property = $reflection->getProperty($propertyName);

          if (!$property->isPublic()) {
            $errors[$config][] = sprintf('Required property "%s" must be accessible. %s', $propertyName, $propertyName);
            $property->setAccessible(true);
          }

          if ($defaultValue === $property->getValue(new $config)) {
            $errors[$config][] = sprintf('Required property "%s" should have different value than default. %s', $propertyName, $property);
          }

          unset($property);
        }
      }

      unset($reflection);
    }

    return $errors;
  }

}
