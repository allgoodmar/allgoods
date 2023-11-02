<?php

namespace App\Console\Commands;

use App\Referal;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FillReferalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fillreferal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Referals have been filled';

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
        $users = User::orderBy('id', 'DESC')->get();
        $ids = Referal::orderBy('id', 'DESC')->get();
        $allIds = [];
        foreach ($ids as $key => $value) {
            $allIds[] = [
                "id" => $value->ref_user_id
            ];
        }
        $allIdVals = array_column($allIds, 'id');
        $allIdVals = array_values($allIdVals);
        foreach ($users as $key => $value) {
            if (!in_array($value->id, $allIdVals)) {
                $ref_user_name = "re-" . $value->id . '5' . "-" . Str::slug($value->name); // 5 means referal
                $ref_user_name = str_replace(" ", "", $ref_user_name);
                Referal::create([
                    'ref_user_id' => $value->id,
                    'name_ref' => $ref_user_name,
                    'amount_coupon' => 20000,
                ]);
            }
        }
        return 0;
    }
}
