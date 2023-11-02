<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{
	private $check = 'mkudqgctu5wk6bxa';

    public function optimize($check)
	{
		$this->check($check);
        Artisan::call('debugbar:clear');
        Artisan::call('optimize:clear');
        // Artisan::call('optimize');
		echo 'Done!';
		exit;
	}

    public function viewClear($check)
	{
		$this->check($check);
		$exitCode = Artisan::call('view:clear');
		echo Artisan::output();
		exit;
	}

	private function check($check)
	{
		if ($check != $this->check) {
			abort(403);
		}
	}
}
