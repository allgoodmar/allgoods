<?php

namespace App\Services;

use App\AmoToken;
use GuzzleHttp\Client;
use Throwable;

class AmoCrmService {

    private $token;

    const CLIENT_ID = "333b1ead-0148-42db-a703-9e0e69292f70";
    const CLIENT_SECRET = "zK50BBQac2E5zcPEXtdmWnIw6M7ExErEo75Y7QRssgH2ZEpdoLLYSVp2nreJINev";
    const GRANT_TYPE = "authorization_code";
    const REDIRECT_URI = "https://allgood.uz";

    const CODE = "def502006e9a20bbb48db54284b9274488f19106c3b64d44b2c3fa07abe84eb75cd09fa89dd9ee61afd0bf33e02acd4c33f26a86763ccf577d73065cfb00847c70409d83e764ec102570e514ab0728bb2fbffc5a04d78e76fcdae1931c0b34a00d4d15f5140a9ddca77caf728b39f3c417d45783e5121024184787e50e885574d94e54ca75273621454dd91ccfb22e60f92bf8eaf8712617946a2f04b38571d156898d400eb022f6e43ea02b261726c07a29c158e9beacf36398aeba08bfbf5e72b803f98008be793ed533c80439f4ef240eee2179b43bdbe7075d7c9decbc2907f3c6aa0956bc24effe91c3592b4ee4c0ce6debf3f9d8374c237e1e64de0115082087e32abf8b244c5034abdb06553c76b977fe760b16d0458620e52de685e57e510191de0943b8514885f695ac5878fface01b8addbf61c5b76b28feb289ee286262e6e47807f6280944f6064aa82a568a8d7804b0a605aece5739afc5951ecb69e36c02ec4b53bf09a034bd9745ab40ca2276fcbafde0a50c14c77f0ee50fa0fc6b0fbe2b68b80114642176826e80565a6e9ff1e991edccfea910c66b2521367692545c6987d56978ff43188ced593322dfb253194608a10f5de229007254426a46ae9ea27097d653ee685f466cd78bba6a";

    const ID_FROM_AMO_TABLE = 3;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://infoallgooduz.amocrm.ru/oauth2/access_token',
            'timeout'  => 300.0,
        ]);
        $this->token = $this->generateToken();
    }

    public function sendReq($body)
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token
            ],
            'json' => $body
        ];

        try {
            return $this->client->request('POST', 'https://infoallgooduz.amocrm.ru/api/v4/leads/complex', $params);
        } catch (Throwable $e) {
            // Log::debug($e);
        }
        return false;
    }

    public function auth($body)
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $body
        ];

        try {
            return $this->client->request('POST', 'https://infoallgooduz.amocrm.ru/oauth2/access_token', $params);
        } catch (Throwable $e) {
            // Log::debug($e);
        }

        return false;
    }

    public function generateToken()
    {

        $amo = AmoToken::count();

        if ($amo == 0)
        {
            $body = array(
                "client_id" => self::CLIENT_ID,
                "client_secret" => self::CLIENT_SECRET,
                "grant_type" => self::GRANT_TYPE,
                "code" => self::CODE,
                "redirect_uri" => self::REDIRECT_URI
            );
            $body = json_decode(json_encode($body));
            $go = $this->auth($body);
            $details = $go->getBody()->getContents();
            $details = json_decode($details);

            AmoToken::create([
                'access_token' => $details->access_token,
                'refresh_token' => $details->refresh_token,
                'token_type' => $details->token_type,
                'expires_in' => ''
            ]);

            $access_token = $details->access_token;
            $token = $access_token;

        }elseif($amo == 1)
        {
            $amo = AmoToken::where('id', self::ID_FROM_AMO_TABLE)->first();

            $token = $amo->access_token;

            /* REFRESH TOKEN uchun
            $body = array(
                "client_id" => self::CLIENT_ID,
                "client_secret" => self::CLIENT_SECRET,
                "grant_type" => self::GRANT_TYPE,
                "refresh_token" => $amo->refresh_token,
                "redirect_uri" => self::REDIRECT_URI
            );
            $body = json_decode(json_encode($body));
            $go = $this->auth($body);
            $details = $go->getBody()->getContents();
            $details = json_decode($details);

            $access_token = $details->access_token;
            AmoToken::where('id', self::ID_FROM_AMO_TABLE)->update([
                'access_token' => $details->token_type,
                'refresh_token' => $details->expires_in,
                'token_type' => $details->access_token,
                'expires_in' => $details->refresh_token
            ]);

            $token = $access_token;
            */

        }

        return $token;
    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }
}
