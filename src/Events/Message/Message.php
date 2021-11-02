<?php
namespace DarkFox\BotSM\Events\Message;

use DarkFox\BotSM\Events\Core\EventBase;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message as ChannelMessage;
use Discord\WebSockets\Event;

class Message extends EventBase
{
  public function handle(): EventBase {
    $this->messageDelete();

    return $this;
  }

  protected function messageDelete(): Message {
    $this->discord->on(Event::MESSAGE_DELETE, function (ChannelMessage $message, Discord $bot, mixed $oldMessage = null) {

      $message->channel->sendMessage(MessageBuilder::new()->setContent('test'));
    });

    return $this;
  }

}
