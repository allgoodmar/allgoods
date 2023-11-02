<?php

namespace App\Console\Commands;

use App\AmoToken;
use App\Helpers\Helper;
use App\ImportPartner;
use App\Product;
use App\Services\AmoCrmService;
use App\Services\BillzAdrasService;
use Illuminate\Console\Command;


class AmoTokenUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Amo:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh AmoCRM access token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new AmoCrmService();
        $amo = AmoToken::where('id', AmoCrmService::ID_FROM_AMO_TABLE)->first();
        $body = array(
            "client_id" => AmoCrmService::CLIENT_ID,
            "client_secret" => AmoCrmService::CLIENT_SECRET,
            "grant_type" => "refresh_token",
            "refresh_token" => $amo->refresh_token,
            "redirect_uri" => AmoCrmService::REDIRECT_URI
        );
        $body = json_decode(json_encode($body));
        $go = $service->auth($body);
        $details = $go->getBody()->getContents();
        $details = json_decode($details);

        AmoToken::where('id', AmoCrmService::ID_FROM_AMO_TABLE)->update([
            'access_token' => $details->access_token,
            'refresh_token' => $details->refresh_token,
            'token_type' => $details->token_type,
            'expires_in' => $details->expires_in
        ]);
    }
}
