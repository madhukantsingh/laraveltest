<?php
// app/Http/Middleware/SwitchDatabaseMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SwitchDatabaseMiddleware
{
    public function handle($request, Closure $next)
    {
        $url = $request->url();

        if (strpos($url, 'api/user2') !== false) {
            Config::set('database.default', 'mysql_second');
            DB::setDefaultConnection('mysql_second');
        } else {
            Config::set('database.default', 'mysql');
            DB::setDefaultConnection('mysql');
        }

        return $next($request);
    }
}
