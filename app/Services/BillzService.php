<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class BillzService {

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
        'iss'=> 'allgood.uz',
        'iat'=> time(),
        'exp'=> time() + (24*60*60),
        'sub'=> 'allstore.ecommerce'
        );

        $header = $this->base64_url_encode(json_encode($headerArray, JSON_FORCE_OBJECT));
        $payload = $this->base64_url_encode(json_encode($payloadArray, JSON_FORCE_OBJECT));

        $unsignedToken = $header .'.'. $payload;

        $signature = hash_hmac("sha256", $unsignedToken, $secret, true);
        $encodedSignature = $this->base64_url_encode($signature);
        // $token = $unsignedToken . '.' . $encodedSignature;
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhbGxnb29kLnV6IiwiaWF0IjoxNjU0MDE0OTc4LCJleHAiOjE2NTQxNjk3NzgsInN1YiI6ImFsbHN0b3JlLmVjb21tZXJjZSJ9.V90346KiwWWUCD8LT5ROBcb-RIIHiMHD5l_zhCUaDhM";
        return $token;

    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
