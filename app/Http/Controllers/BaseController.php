<?php
// app/Http/Controllers/BaseController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class BaseController extends Controller
{
    protected function switchConnection()
    {
        $url = request()->url();

        if (strpos($url, 'api/user2') !== false) {
            config(['database.default' => 'mysql_second']);
            DB::setDefaultConnection('mysql_second');
        } else {
            config(['database.default' => 'mysql']);
            DB::setDefaultConnection('mysql');
        }
    }
}
