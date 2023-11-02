<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Log;
use Throwable;

class GrowCrmService
{
    protected $token;
    protected $client;

    public function __construct()
    {
        $baseUrl = config('services.growcrm.base_url');
        $this->token = config('services.growcrm.token');
        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 2.0,
        ]);
    }

    public function createLead($data)
    {
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'leads', $params);
    }

    public function send($method, $url, $params = [])
    {
        if (empty($params['headers']['Authorization'])) {
            $params['headers']['Authorization'] = 'Bearer ' . $this->token;
        }
        if (empty($params['headers']['Accept'])) {
            $params['headers']['Accept'] = 'application/json';
        }

        try {
            return $this->client->request($method, $url, $params);
        } catch (ClientException $e) {
            // Log::debug(Message::toString($e->getRequest()));
            Log::debug($e->getResponse()->getBody()->getContents());
        } catch (Throwable $e) {
            Log::debug($e->getMessage());
        }
        return false;
    }

    private function toMultipart(array $arr)
    {
        $result = [];
        foreach ($arr as $key => $value) {
            if (!is_array($value)) {
                $result[] = [
                    'name' => $key,
                    'contents' => is_file($value) ? Utils::tryFopen($value, 'rb') : $value,
                ];
            } else {
                foreach ($value as $key1 => $value1) {
                    if (!is_array($value1)) {
                        $result[] = [
                            'name' => $key . '[' . $key1 . ']',
                            'contents' => is_file($value1) ? Utils::tryFopen($value1, 'rb') : $value1,
                        ];
                    } else {
                        foreach ($value1 as $key2 => $value2) {
                            $result[] = [
                                'name' => $key . '[' . $key1 . ']' . '[' . $key2 . ']',
                                'contents' => is_file($value2) ? Utils::tryFopen($value2, 'rb') : $value2,
                            ];
                        }
                    }
                }
            }
        }
        return $result;
    }
}
