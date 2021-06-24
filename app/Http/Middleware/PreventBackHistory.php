<?php

    namespace App\Http\Middleware;

    use Closure;

    class PreventBackHistory{
        public function handle($request, Closure $next){
            $handle = $next($request);

            if(method_exists($handle, 'header')){
                $handle->header('Access-Control-Allow-Origin' , '*')
                        ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
                        ->header('Cache-Control','nocache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma','no-cache')
                        ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
            }
            return $handle;
        }
    }