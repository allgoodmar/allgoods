<?php

namespace App\Http\Controllers\Voyager;

use App\Subscriber;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use const JSON_UNESCAPED_UNICODE;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;

class VoyagerSubscriberController extends VoyagerBaseController
{
    public function download()
    {
        $this->authorize('browse_admin');
        $filePath = Storage::path('subscribers/subscribers.txt');
        $subscribers = Subscriber::select('email')->where('status', 1)->get();
        $fp = fopen($filePath, 'w+');
        foreach ($subscribers as $subscriber) {
            fwrite($fp, $subscriber->email . "\n");
        }
        fclose($fp);
        return response()->download($filePath);
    }
}
