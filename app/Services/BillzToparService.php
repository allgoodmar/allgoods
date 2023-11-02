<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class BillzToparService {

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
            "iss": "mylifestylee.uz",
            "iat": 1656588038,
            "exp": 1688106038,
            "sub": "mls.allgoods"
        }

        SUB: mls.allgoods
        SECRET_KEY = J2yYjhtZBmTeCWFi6tIxxkXsZbbYXtnGkSnfCPiftWXaFnwUXh2tGWwI2zXhAfGmeGm2KnniphXCwUnxiiSdQjQQSmncvpRXcJErYKDoN2vDsJGoHQkrpWCxSYhfbEfSrDtwerkwnNhHtWEAcySKmt7UIKWBPJfEHNYwzRJUESyjThxuX9snxBMzopWtARbvcf1ToPvfrsTFYSPhKNoLmTEV1WPAjkQfxDCHvSAoQKtwT1fydmeLKFsfCuTWZKnPUVnUkoVbyhIFVKuiiUmuQhnAwVEVdMdkGeReRCzctMRHQLNJPFRrSYpsRrroBZmfnzk4nUkBIdB2takLBvKvLucx8hutbL3FMC7DfYKWJswKyacSmbkntNdiHeFofTGvFVJYyVI4MHGmTeQIIEfuAnovGzRLsStpMfKXwKDYj6YnturQiKIfFvQmUAmrN09bMRJQrTiSvEhthFkP4zsxMIBIGVNkLdaWiiDJoYIEpr9MabbfADNBjbjuMxaCxiPd

        */
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJ0b3Bhci5hbGxnb29kLnV6IiwibmFtZSI6InRvcGFyLnV6IiwiaWF0IjoxNjk5NjMwMTYwfQ.5mev6v8AD3gIK9gDToE8319kio809snOOFiiiGCME3Y";
        return $token;

    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
