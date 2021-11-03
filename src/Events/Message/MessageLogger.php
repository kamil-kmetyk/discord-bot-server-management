<?php
namespace DarkFox\BotSM\Events\Message;

use DarkFox\BotSM\Events\Core\EventLogger;
use Discord\Parts\Embed\Field;
use React\Promise\ExtendedPromiseInterface;

class MessageLogger extends EventLogger
{
  public const MSG_LOGGER_TYPE_CREATE = 'create';
  public const MSG_LOGGER_TYPE_UPDATE = 'update';
  public const MSG_LOGGER_TYPE_DELETE = 'delete';

  public function sendSimple(string $type, string $messageId, string $channel, string $editor): ExtendedPromiseInterface {
    $fields = [];

    $messageField = new Field($this->discord);
    $messageField->name = 'Message id';
    $messageField->value = $messageId;

    $channelField = new Field($this->discord);
    $channelField->name = 'Channel';
    $channelField->value = $channel;

    $fields[] = $messageField;
    $fields[] = $channelField;

    return parent::sendLog($type, $fields, editor: $editor);
  }

  public function send(string $type, string $author, string $editor, string $messageId, string $newMessage, string $channel, ?string $originalMessage = null): ExtendedPromiseInterface {
    $fields = [];

    $authorField = new Field($this->discord);
    $authorField->name = 'Message author';
    $authorField->value = $author;

    $channelField = new Field($this->discord);
    $channelField->name = 'Channel';
    $channelField->value = $channel;
    $channelField->inline = true;

    $messageIdField = new Field($this->discord);
    $messageIdField->name = 'Message id';
    $messageIdField->value = $messageId;
    $messageIdField->inline = true;

    $messageField = new Field($this->discord);
    $messageField->name = 'Content';
    $messageField->value = $newMessage;

    $fields[] = $authorField;
    $fields[] = $channelField;
    $fields[] = $messageIdField;
    $fields[] = $messageField;

    if (is_string($originalMessage)) {
      $oldMessageField = new Field($this->discord);
      $oldMessageField->name = 'Old message';
      $oldMessageField->value = $originalMessage;

      $fields[] = $originalMessage;
    }

    return parent::sendLog($type, $fields, editor: $editor);
  }

  protected function getTitleByType(string $type): string {
    return match ($type) {
      static::MSG_LOGGER_TYPE_CREATE => 'Message created',
      static::MSG_LOGGER_TYPE_UPDATE => 'Message updated',
      static::MSG_LOGGER_TYPE_DELETE => 'Message removed',
      default => static::DEFAULT_TITLE,
    };
  }

  protected function getColorByType(string $type): string {
    return match ($type) {
      static::MSG_LOGGER_TYPE_CREATE => static::SUCCESS_COLOR,
      static::MSG_LOGGER_TYPE_UPDATE => static::UPDATE_COLOR,
      static::MSG_LOGGER_TYPE_DELETE => static::DELETE_COLOR,
      default => static::DEFAULT_COLOR,
    };
  }

}
