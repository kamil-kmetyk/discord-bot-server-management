<?php
namespace DarkFox\BotSM\Events\Message;

use DarkFox\BotSM\Events\Core\EventBase;
use Discord\Discord;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message as ChannelMessage;
use Discord\Parts\Guild\AuditLog\AuditLog;
use Discord\Parts\Guild\AuditLog\Entry;
use Discord\Parts\Guild\Guild;
use Discord\Parts\User\Member;
use Discord\WebSockets\Event;
use stdClass;

class Message extends EventBase
{
  protected ?Channel $log;
  protected ?Channel $notifications;
  protected ?MessageLogger $logger;

  public function handle(): EventBase {
    $this->messageDelete();

    return $this;
  }

  protected function init(): void {
    $this->log = $this->discord->getChannel($this->channelsConfig->log);
    $this->notifications = $this->discord->getChannel($this->channelsConfig->notifications);
    $this->logger = new MessageLogger($this->log, $this->discord);
  }

  protected function messageDelete(): Message {
    $this->discord->on(Event::MESSAGE_DELETE, function(stdClass|ChannelMessage $message, Discord $bot) {
      if (is_null($this->log) && is_null($this->notifications)) {
        echo sprintf('[Error] Log or channel are not set. Log id: "%s", Notifications: "%s"', $this->channelsConfig->log, $this->channelsConfig->notifications);
      }


      if (isset($this->channelsConfig->exclusions['log']) && in_array($message->id, $this->channelsConfig->exclusions['log'])) {
        return; // Channel's  excluded. Don't store anything.
      }

      $bot->guilds->fetch($message->guild_id)->done(function(Guild $guild) use ($message, $bot) {
        $guild->getAuditLog([ 'action_type' => Entry::MESSAGE_DELETE ])->done(function(AuditLog $auditLog) use ($message, $bot, $guild) {
          $log = null;

          /** @var Entry $entry */
          foreach ($auditLog->audit_log_entries as $entry) {
            if (Entry::MESSAGE_DELETE === $entry->action_type) {
              $log = $entry;
              break;
            }
          }

          if ($log instanceof Entry) {
            $editorId = $log->user_id;

            $guild->members->fetch($editorId)->done(function (Member $member) use ($message, $bot) {
              if ($message instanceof ChannelMessage) {
                $this->logger->send(
                  type: MessageLogger::MSG_LOGGER_TYPE_DELETE,
                  author: vsprintf('%s | <@%s>', [ $message->user->username, $message->user->id ]),
                  editor: vsprintf('Action by: %s | @%s', [ $member->username, $member->id ]),
                  messageId: $message->id,
                  newMessage: $message->content,
                  channel: vsprintf('<#%s>', [ $message->channel->id ]),
                );
              } else {
                $this->logger->sendSimple(
                  type: MessageLogger::MSG_LOGGER_TYPE_DELETE,
                  messageId: $message->id,
                  channel: vsprintf( '<#%s>', [ $message->channel_id ] ),
                  editor: vsprintf('Action by: %s | @%s', [ $member->username, $member->id ]),
                );
              }
            });
          } else {
            $this->logger->sendSimple(
              type: MessageLogger::MSG_LOGGER_TYPE_DELETE,
              messageId: $message->id,
              channel: vsprintf( '<#%s>', [ $message->channel_id ] ),
              editor: 'unknown',
            );
          }
        });
      });
    });

    return $this;
  }

}
