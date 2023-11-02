<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class BillzDivaService {

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

        /* for one year

        {
            "iss": "diva.uz",
            "iat": 1668768039,
            "exp": 1700286039,
            "sub": "diva.allgood"
        }

        SUB: diva.allgood
        SECRET_KEY = ehVSNiLhkW2rEofoBUUFTdjhfmwUEfkJuo9QrWVTMcWXBH2R7oExBKiktSotMwrFKeHVLbHoxRcumryZZBSFQtu4hMJEtMy8HESmJNfsfhsmBHJsTGydPksSRRYEoGMevVFvEEReiJRsBdeatiHiWRrXvRr8vtwBcxhGLbw0QrwpoxSJYGnmxDdrM8tZxofJHYyMEcbjYd8KoiiuYEjSwAsPUzfjpDKXouCsYutxkKwmLkKmbCSTNXYBXEWtFMGnYBCNDbSjPfEHkdaUCxdDBjYnYymeGZkjmy9pmdvpHwBYBYwzCyWGw2AXzNSJAdTbedWwGVkJUeccjvnBxImdFk2LMs9LforMaXWHUHJwDZJFbnyXwDoFoansvwJMjCWvcYkECWXbHvJhXFjjvnSUN1ew7F7EIhsFyUjEjVYeyxMLGwBribHyfCRWe2iLDoHGptKHBiouJE3UBGnCGcdpjiC1ytQMKhmNQXTHfpkS2p7GIdYmhmAMWjkNmBumpYIa

        */
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJkaXZhLnV6IiwiaWF0IjoxNjY4NzY4MDM5LCJleHAiOjE3MDAyODYwMzksInN1YiI6ImRpdmEuYWxsZ29vZCJ9.jECFxX_oWF3o385y2MQUlZeHp5-00pdLXEP-7CvN7bM";
        return $token;

    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
