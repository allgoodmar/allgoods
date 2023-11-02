<?php

namespace App\Console\Commands;

use App\Services\AtmosService;
use Illuminate\Console\Command;


class AtmosTokenUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atmos:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Atmos access token';

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
        $service = new AtmosService();
        $service->updateAccessToken();
    }
}
