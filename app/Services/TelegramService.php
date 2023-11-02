<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramService
{
    protected $token;
    protected $client;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->client = new Client([
            'base_uri' => 'https://api.telegram.org/bot' . $this->token . '/',
            'timeout' => 2.0,
        ]);
    }

    public function sendMessage($chat_id, $message, $parse_mode = 'Markdown', $otherParams = [])
    {
        $formData = [];
        $formData['chat_id'] = $chat_id;
        $formData['text'] = $message;
        if (in_array($parse_mode, ['HTML', 'Markdown', 'MarkdownV2'])) {
            $formData['parse_mode'] = $parse_mode;
        }

        if (is_array($otherParams) && count($otherParams)) {
            $formData = array_merge($otherParams, $formData);
        }

        $params = [
            'form_params' => $formData,
        ];

        return $this->send('POST', 'sendMessage', $params);
    }

    public function sendLocation($chat_id, $latitude, $longitude, $otherParams = [])
    {
        $formData = [];
        $formData['chat_id'] = $chat_id;
        $formData['latitude'] = $latitude;
        $formData['longitude'] = $longitude;
        if (is_array($otherParams) && count($otherParams)) {
            $formData = array_merge($otherParams, $formData);
        }
        $params = [
            'form_params' => $formData,
        ];

        return $this->send('POST', 'sendLocation', $params);
    }

    public function getUpdates($allowed_updates = [])
    {
        if (is_string($allowed_updates)) {
            $allowed_updates = [$allowed_updates];
        }
        $formData = [
            'allowed_updates' => $allowed_updates,
            'timeout' => 1,
        ];

        $params = [
            'form_params' => $formData,
        ];

        $res = $this->send('POST', 'getUpdates', $params);
        if ($res) {
            $content = $res->getBody()->getContents();
            $json = json_decode($content);
            if ($json) {
                return $json;
            }
        }

        return false;
    }

    public function send($method, $url, $params = [])
    {
        try {
            return $this->client->request($method, $url, $params);
        } catch (Throwable $e) {
            // Log::debug($e);
        }
        return false;
    }
}
