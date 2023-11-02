<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TrendyolService
{
    public $client;

    private $api_url;
    private $username;
    private $password;

    public function __construct()
    {
        $this->api_url = config('services.trendyol.api_url');
        $this->username = config('services.trendyol.username');
        $this->password = config('services.trendyol.password');

        $this->client = new Client([
            'base_uri' => $this->api_url,
            'timeout'  => 300.0,
        ]);
    }

    public function stock(array $barcodes)
    {
        $url = 'v2/offers/' . $this->username . '/stock-status';
        foreach ($barcodes as $key => $barcode) {
            $url .= ($key == 0 ? '?' : '&') . 'barcodeList=' . $barcode;
        }
        return $this->send('GET', $url);
    }

    public function offers($page, $barcode = null)
    {
        $queryParams = [
            'buyerCode' => $this->username,
            'page' => $page,
            'pageSize' => '20',
            'order' => 'asc',
            'orderBy' => 'barcode',
        ];
        if ($barcode) {
            $queryParams['Barcodes'] = $barcode;
        }
        return $this->send('GET', 'v1_1/offers', [
            'query' => $queryParams,
        ]);
    }

    public function purchase(array $items)
    {
        $url = 'v2/offers/' . $this->username . '/items';
        $json = [];
        foreach ($items as $item) {
            $json[] = [
                'barcode' => $item['barcode'],
                'requestedQuantity' => $item['quantity'],
            ];
        }
        return $this->send('POST', $url, [
            'json' => $json,
        ]);
    }

    public function b2bs($requestNumber)
    {
        $url = 'v2/offers/' . $requestNumber . '/B2Bs';
        return $this->send('GET', $url);
    }

    public function cancel($requestNumber, array $item)
    {
        $url = 'v2/offers/' . $requestNumber . '/cancel-items';
        $json = [
            'barcode' => $item['barcode'],
            'quantity' => $item['quantity'],
        ];
        return $this->send('POST', $url, [
            'json' => $json,
        ]);
    }

    public function send($method, $url, $params = [])
    {
        $params['auth'] = [$this->username, $this->password];
        try {
            return $this->client->request($method, $url, $params);
        } catch (Throwable $th) {
            Log::debug($th->getMessage());
        }
        return false;
    }
}
