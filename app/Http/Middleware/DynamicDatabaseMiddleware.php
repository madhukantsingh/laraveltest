<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class DynamicDatabaseMiddleware
{
    public function handle($request, Closure $next)
    {
        $url = $request->url();
        $jsonFile = $this->getJsonFile($url);
        $jsonFilePath = database_path("database_config_files\\{$jsonFile}.json");
        if ($jsonFile && File::exists($jsonFilePath)) {
            $this->loadConnectionFromJson($jsonFile);
            
        } 

        return $next($request);
        
    }

    protected function getJsonFile($url)
    {
    
        // if (Str::startsWith($url, 'http://lavaeldb.')) {
            
        //     return 'lavaeldb';
        // }elseif(Str::startsWith($url, 'http://afs.')){
        //     return 'afs';
        // }
        // elseif(Str::startsWith($url, 'http://lavaeldb2.')){
        //     return 'lavaeldb2';
        // }
        // return null;
        $urlMappings = config('database_connections');

        foreach ($urlMappings as $urlPrefix => $connectionName) {
            if (Str::startsWith($url, $urlPrefix)) {
                return $connectionName;
            }
        }

        return null;
    }

    protected function loadConnectionFromJson($jsonFile)
    {
        $jsonContent = File::get(database_path("database_config_files\\{$jsonFile}.json"));
        $config = json_decode($jsonContent, true);
        // print_r($config);
        // die;

        // Ensure the JSON file has the required database connection parameters
        if ($config && isset($config['DB_CONNECTION'])) {
            $connectionName = "{$jsonFile}_dynamic";
            Config::set("database.connections.{$connectionName}", [
                'driver' => $config['DB_CONNECTION'],
                'host' => $config['DB_HOST'],
                'port' => $config['DB_PORT'],
                'database' => $config['DB_DATABASE'],
                'username' => $config['DB_USERNAME'],
                'password' => $config['DB_PASSWORD'],
            ]);
            Config::set("database.default", $connectionName);
        } else {
            // Handle the case when the JSON file is missing required parameters
            // You can throw an exception, log an error, or set a default connection
            \Log::error("Missing required parameters in JSON file: {$jsonFile}");
        }
    }
}
