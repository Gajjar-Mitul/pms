<?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;
    use Config;

    class MailConfigServiceProvider extends ServiceProvider{
        /**
         * Register services.
         *
         * @return void
         */
        public function register(){
            //
        }

        /**
         * Bootstrap services.
         *
         * @return void
         */
        public function boot(){
            $config = [
                'driver' => 'smtp',
                'host' => 'mail.cypherocean.com',
                'port' => '465',
                'username' => 'info@cypherocean.com',
                'password' => 'cyPhe2!@',
                'encryption' => 'ssl',
                'from'       => ['address' => 'info@cypherocean.com', 'name' => 'CypherOcean'],
                'sendmail'   => '/usr/sbin/sendmail -bs',
                'pretend'    => false,
            ];

            Config::set('mail', $config);
        }
    }
