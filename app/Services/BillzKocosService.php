<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class BillzKocosService {

    private $token;

    public function __construct()
    {
        $this->token = $this->generateToken();
        $this->client = new Client([
            'base_uri' => 'https://api.billz.uz/v1/',
            'timeout'  => 300.0,
        ]);
    }

    public function sendReq($body)
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'Cache-Control' => 'no-cache'
            ],
            'json' => $body
        ];

        try {
            return $this->client->request('POST', 'https://api.billz.uz/v1/', $params);
        } catch (Throwable $e) {
            // Log::debug($e);
        }
        return false;
    }

    public function generateToken()
    {
        $secret = env("JWT_SECRET");

        $headerArray = array(
            'typ'=> 'JWT',
            'alg'=> 'HS256'
        );

        $payloadArray = array(
        'iss'=> 'shopadras.uz',
        'iat'=> time(),
        'exp'=> time() + (24*60*60),
        'sub'=> 'adras.ecommerce'
        );

        $header = $this->base64_url_encode(json_encode($headerArray, JSON_FORCE_OBJECT));
        $payload = $this->base64_url_encode(json_encode($payloadArray, JSON_FORCE_OBJECT));

        $unsignedToken = $header .'.'. $payload;

        $signature = hash_hmac("sha256", $unsignedToken, $secret, true);
        $encodedSignature = $this->base64_url_encode($signature);
        // $token = $unsignedToken . '.' . $encodedSignature;

        /* for one year 14.06.2022

        {
            "iss": "kocos.uz",
            "iat": 1655202100,
            "exp": 1686204525,
            "sub": "kocos.marketplaceallgood"
        }

        */
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJrb2Nvcy51eiIsImlhdCI6MTY1NTIwMjEwMCwiZXhwIjoxNjg2MjA0NTI1LCJzdWIiOiJrb2Nvcy5tYXJrZXRwbGFjZWFsbGdvb2QifQ.D7E1TMQX-N60xhqm3fUMSZ-KwC4WuncDyW5Yl8YZ9Sc";
        return $token;

    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
