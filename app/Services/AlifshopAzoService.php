<?php

namespace App\Services;

use App\Shop;
use App\ImanOrder;
use App\Order;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Request;
use Throwable;

class AlifshopAzoService
{
    protected $token;
    protected $client;

    public function __construct()
    {
        $baseUrl = config('services.alifshop.azo.url');
        $this->token = config('services.alifshop.azo.merchant_token');
        $this->client = new GuzzleClient([
            'base_uri' => $baseUrl,
            'timeout' => 10.0,
        ]);
    }

    public function clientsCheck($phoneNumber)
    {
        $data = [
            'phone' => $phoneNumber,
        ];
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'clients/check', $params);
    }

    public function clientsLimit($phoneNumber)
    {
        $data = [
            'phone' => $phoneNumber,
        ];
        $params = [
            'json' => $data,
        ];
        return $this->send('GET', 'clients/limit', $params);
    }

    public function requestOTP($phoneNumber)
    {
        $data = [
            'phone' => $phoneNumber,
        ];
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'applications/request-otp', $params);
    }

    public function applicationCreate($data, $cart, $partnerInstallment)
    {
        $items = [];
        foreach ($cart->getContent() as $cartItem) {
            $ikpu = random_int(10**16, (10**17) - 1);
            $ikpu = (string) $ikpu;
            for ($i = 0; $i < $cartItem->quantity; $i++) {
                $items[] = [
                    'good' => $cartItem->name,
                    'good_type' => $cartItem->associatedModel->categories()->first()->name ?? config('app.name'),
                    'price' => $cartItem->price * 100, // tiyin
                    'sku' => $cartItem->associatedModel->sku ?: 'product-id-' . $cartItem->associatedModel->id,
                    'ikpu' => $cartItem->associatedModel->categories()->first()->ikpu ?? $ikpu
                ];
            }
        }
        $data = [
            'phone' => session()->get('alifshop_phone_number'),
            'otp' => $data['alifshop_otp'],
            'condition' => [
                'commission' => $partnerInstallment->percent,
                'duration' => $partnerInstallment->duration,
            ],
            'items' => $items,
        ];
        $params = [
            'json' => $data,
        ];
        return $this->send('POST', 'applications/store', $params);
    }

    public function send($method, $url, $params = [])
    {
        if (empty($params['headers']['Merchant-Token'])) {
            $params['headers']['Merchant-Token'] = $this->token;
        }
        if (empty($params['headers']['Accept'])) {
            $params['headers']['Accept'] = 'application/json';
        }

        try {
            return $this->client->request($method, $url, $params);
        } catch (ClientException $e) {
            // Log::debug(Message::toString($e->getRequest()));
            // Log::debug($e->getResponse()->getBody()->getContents());
            return $e->getResponse();
        } catch (Throwable $e) {
            Log::debug($e->getMessage());
        }
        return false;
    }
}
