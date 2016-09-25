<?php

namespace acolish\model;

use acolish\client\SlackClient;
use acolish\entity\Gift AS GiftEntity;
use acolish\entity\User AS UserEntity;

class Slack
{
    private $client;

    private $channel;

    public function __construct($slackConfig)
    {
        $this->client = new SlackClient($slackConfig['web_hook_url']);
        $this->channel = $slackConfig['channel'];
    }

    public function rsvpNotify(UserEntity $user)
    {
        $message = $user->getStatusString() . 'します';
        $this->client->setUserName($user->getName());
        $this->client->sendMessage($this->channel, $message);
    }

    public function presentNotify(UserEntity $user, GiftEntity $gift)
    {
        $message = '希望引き出物：' . $gift->getName();
        $this->client->setUserName($user->getName());
        $this->client->sendMessage($this->channel, $message);
    }
}