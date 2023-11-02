<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ElmakonService
{
    public $client;

    private $api_url;
    private $login;
    private $password;

    public function __construct()
    {
        $this->api_url = config('services.elmakon.api_url');
        $this->login = config('services.elmakon.login');
        $this->password = config('services.elmakon.password');

        $this->client = new Client([
            'base_uri' => $this->api_url,
            'timeout'  => 300.0,
        ]);
    }

    public function getProducts()
    {
        return $this->send('GET', 'products');
    }

    public function send($method, $url, $params = [])
    {
        $params['auth'] = [$this->login, $this->password];
        try {
            return $this->client->request($method, $url, $params);
        } catch (Throwable $e) {
            // Log::debug($e);
        }
        return false;
    }
}
