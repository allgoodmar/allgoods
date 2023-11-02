<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class BillzOmiodiobrandService {

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
            "iss": "omiodiobrand.uz",
            "iat": 1656922381,
            "exp": 1688440381,
            "sub": "od.allgood"
        }

        SUB: od.allgood
        SECRET_KEY = wQLSKWVxDtzYsKtHcRCDyQpsvCceLvmjxxWcdFjFUcfPfovcdeCLydasRPwwShGbswBVrCMWTHteeKvxfeQJYmB5MTzwnRfLy2LXQ3YWkvrcpvQXUMH6tiQErBsETtISNeCvUuUsBHMNhrnHhGnhYWrPHDCJChpybTxjpKPVuuFAyDmxhemSryHvIdeWcKrCakxwCCccBshX9cnoInCS2wTjWDfRVFxvecxBePYixtrpBujRRH5HHjmxNDo8hAWfU2YFrmwFFtiGRCboKUzDNIStGk8LPwoRxPvsfFxx9WpymLmyynZVIPyaPwsfiKxDhcyxIxmJSoBbUeREYXiXLMS44LXPQUAhzoWUjXIrCBWDTre5u9DTXP5tpwLySLRSC8VeCZuBYdAfexbFKBiNJPypJItcjZiEyRhvQzpTddyC4QXAwvMCBr2mfoGKzdEXVibUwPzoEirwfxphdfEkQpQf2NGr1TSyLAxjFXN0cDYhNVtjuLVpSVDBWhKYjRnQ

        */
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJvbWlvZGlvYnJhbmQudXoiLCJpYXQiOjE2NTY5MjIzODEsImV4cCI6MTY4ODQ0MDM4MSwic3ViIjoib2QuYWxsZ29vZCJ9.QZCD7QYX2ooBw504T64hTXVdtQnNBY0YWstxpdanG1A";
        return $token;

    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
