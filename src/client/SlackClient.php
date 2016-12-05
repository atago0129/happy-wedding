<?php

namespace acolish\client;

use GuzzleHttp\Client;

class SlackClient
{
    /** @var \GuzzleHttp\Client */
    private $httpClient;

    /** @var String  */
    private $webHookUrl;

    /** @var string */
    private $userName = 'Wedding_Slack_Notification';

    /** @var string */
    private $icon = ':wedding:';

    public function __construct($webHookUrl)
    {
        $this->webHookUrl = $webHookUrl;
        $this->httpClient = new Client();
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Slack にメッセージを送信
     * 何かしらが原因でメッセージの送信に失敗してもその後の処理は続行
     * @param $channel
     * @param $message
     * @param $attachments
     * @return bool
     */
    public function sendMessage($channel, $message, $attachments = null)
    {
        $params = [
            'channel' => $channel,
            'username' => $this->userName,
            'text' => $message,
            'icon_emoji' => $this->icon,
        ];

        if ($attachments) {
            $params['attachments'] = $attachments;
        }

        try {
        /** @var \GuzzleHttp\Psr7\Response $response */
            $response = $this->httpClient->request('POST', $this->webHookUrl, ['json' => $params]);
        } catch (\Exception $e) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }
        /** @var null|array $result */
        $result = json_decode($response->getBody(), true);
        return isset($result['ok']) && $result['ok'] === true;
    }

}