<?php
namespace DarkFox\BotSM\Events\Core;

use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Embed\Embed;
use React\Promise\ExtendedPromiseInterface;

abstract class EventLogger
{
  protected const DEFAULT_COLOR = '#0066cc';
  protected const SUCCESS_COLOR = '#00cc00';
  protected const UPDATE_COLOR = '#006666';
  protected const DELETE_COLOR = '#cc0000';

  protected const DEFAULT_TITLE = 'Log';

  public function __construct(protected Channel $log, protected Discord $discord) {}

  protected function sendLog(string $type, array $fields, ?string $title = null, ?string $color = null, ?string $editor = null): ExtendedPromiseInterface {
    $embed = new Embed($this->discord);

    $title = is_null($title) ? $this->getTitleByType($type) : $title;
    $color = is_null($color) ? $this->getColorByType($type) : $color;

    $embed->setTitle($title)->setColor($color)->addField(...$fields)->setType(Embed::TYPE_RICH);


    if (is_string($editor)) {
      $embed->setAuthor(mb_substr($editor, 0, 256));
    }

    return $this->log->sendEmbed($embed);
  }

  abstract protected function getTitleByType(string $type): string;
  abstract protected function getColorByType(string $type): string;

}
