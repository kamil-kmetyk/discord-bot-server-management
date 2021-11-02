<?php
namespace DarkFox\BotSM\Events\Core;

use DarkFox\BotSM\Config\ChannelsConfig;
use DarkFox\BotSM\Config\GuildConfig;
use DarkFox\BotSM\Core\BotConstructor;
use Discord\Discord;
use Discord\Slash\Client;

abstract class EventBase extends BotConstructor implements IEvent
{
  protected GuildConfig $guildConfig;
  protected ChannelsConfig $channelsConfig;

  public function __construct(protected Discord $discord, protected Client $client) {
    parent::__construct($discord, $client);

    $this->guildConfig = new GuildConfig;
    $this->channelsConfig = new ChannelsConfig;
  }

  abstract public function handle(): EventBase;

}
