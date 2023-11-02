<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;

class BillzElisiumService {

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
            "iss": "topar.uz",
            "iat": 1656588038,
            "exp": 1688106038,
            "sub": "topar.allgood.uz"
        }

        SUB: topar.allgood.uz
        SECRET_KEY = iMiekFowRFrEwLRWapRVkHBWodUWexNtYxbEjkkPyCujjIretovpb0iVQCBNDBprdrjfwCLYRLeuQR1GRp4FxvPPZCTnMzfbtEPhKCDjazJrRjD2s4uAXDFdS1SJCs94rtBbtFr6Pvw5fRwsNMMkdLEL2HQnUdUQTkskPuXhhrNDvCUfPeuFdEV8asMoikHiFNyUku9UcBMyNzSwmjsxHrMTcFyxeFPnEnxGWuBSGhDsEoSMbFmQBucHTwSQzohHpmeKTtotRVpmEffDkjofARUxJrVwiiZVtiCxHGfSbBc2aILeGGVZfbeYCAH1BNPJDrjmnLwmKrmaaF1RXRwPjeUUijyIUubESkRMvhrRxHaGHiQAsdQktjEmAHUhhxrDBDovMtjvYwvQMkvBMQtnEGrSTLUCfGvYKNBUC97R0mbfuZMNxNvKdGVuP3wtaDuk4wKojrsNbUtXfdfrZPMjnQSBWXMCCpCminUdQEcYINij4bKPXjcMTLkhuyQmGuAJ

        */
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJlbGlzaXVtLnV6IiwiaWF0IjoxNjc0NzQ1MjA1LCJleHAiOjE3MDYyNjMyMDUsInN1YiI6ImVsaXNpdW0uYWxsZ29vZCJ9.n9SjXdGGBNi9GYOA7x8rFBFJeCuerId8hPg5_NAMKRU";
        return $token;

    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
