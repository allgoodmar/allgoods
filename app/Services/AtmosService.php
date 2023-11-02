<?php

namespace App\Services;

use App\Helpers\Helper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class AtmosService
{
    public $client;

    private $api_url;
    private $store_id;
    private $api_key;
    private $consumer_key;
    private $consumer_secret;
    private $access_token;

    public function __construct()
    {
        $this->api_url = config('services.atmos.api_url');
        $this->store_id = config('services.atmos.store_id');
        $this->api_key = config('services.atmos.api_key');
        $this->consumer_key = config('services.atmos.consumer_key');
        $this->consumer_secret = config('services.atmos.consumer_secret');
        $this->access_token = config('services.atmos.access_token');

        $this->client = new Client([
            'base_uri' => $this->api_url,
            'timeout'  => 10,
        ]);
    }

    public function updateAccessToken()
    {

        try {
            $response = $this->client->post('token', [
                'auth' => [$this->consumer_key, $this->consumer_secret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            if (!empty($data['access_token'])) {
                Helper::updateEnv([
                    'ATMOS_ACCESS_TOKEN' => $data['access_token'],
                ]);
            }
        } catch(ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            Log::error($errorBody);
        } catch(Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    public function bindCardCreate(array $data)
    {
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'partner/bind-card/create', $params);
    }

    public function bindCardApply(array $data)
    {
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'partner/bind-card/apply', $params);
    }

    public function removeCard(array $data)
    {
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'partner/remove-card', $params);
    }

    public function merchantPayCreate(array $data)
    {
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'merchant/pay/create', $params);
    }

    public function merchantPayPreConfirm(array $data)
    {
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'merchant/pay/pre-confirm', $params);
    }

    public function merchantPayConfirm(array $data)
    {
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'merchant/pay/confirm', $params);
    }

    public function send($method, $url, $params = [])
    {
        if (empty($params['headers'])) {
            $params['headers'] = [];
        }
        $params['headers']['Authorization'] = 'Bearer ' . $this->access_token;
        try {
            return $this->client->request($method, $url, $params);
        } catch (Throwable $th) {
            Log::debug($th->getMessage());
        }
        return false;
    }
}
