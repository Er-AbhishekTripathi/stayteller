<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {

        if(env('APP_HTTPS')) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS','on');
        }
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        app()->setLocale('en');

        if(strpos($request->path(),'install') === false  && file_exists(storage_path().'/installed')){

            $locale = $request->segment(1);
            $languages = \Modules\Language\Models\Language::getActive();
            $localeCodes = Arr::pluck($languages,'locale');
            if(in_array($locale,$localeCodes)){
                app()->setLocale($locale);
            }else{
                app()->setLocale(setting_item('site_locale'));
            }

            if(!empty($locale) and $locale == setting_item('site_locale'))
            {
                $segments = $request->segments();
                if(!empty($segments) and count($segments) > 1) {
                    array_shift($segments);
                    return redirect()->to(implode('/', $segments))->send();
                }
            }
        }
        if(is_installed()){
            $this->initConfigFromDB();
        }
    }

    protected function initConfigFromDB(){
        // Load Config from Database
        if (!empty(setting_item('email_from_address'))) {
            Config::set('mail.from.address', setting_item("email_from_address",'bookingcore.org'));
        }
        if (!empty(setting_item('email_from_name'))) {
            Config::set('mail.from.name', setting_item("email_from_name",'BookingCore'));
        }
        if (!empty(setting_item('site_timezone'))) {
            Config::set('app.timezone', setting_item("site_timezone"));
            date_default_timezone_set(config('app.timezone'));
        }

        // Load Email Config from Database
        if(!empty(setting_item('email_driver'))){
            Config::set('mail.driver',setting_item("email_driver"));
            switch (setting_item("email_driver")){
                case 'mailgun':
                    if(!empty(setting_item('email_mailgun_domain'))){
                        Config::set('services.mailgun.domain',setting_item("email_mailgun_domain"));
                    }
                    if(!empty(setting_item('email_mailgun_secret'))){
                        Config::set('services.mailgun.secret',setting_item("email_mailgun_secret"));
                    }
                    if(!empty(setting_item('email_mailgun_endpoint'))){
                        Config::set('services.mailgun.endpoint',setting_item("email_mailgun_endpoint"));
                    }
                    break;
                case 'postmark':
                    if(!empty(setting_item('email_postmark_token'))){
                        Config::set('services.postmark.token',setting_item("email_postmark_token"));
                    }
                    break;
                case 'ses':
                    if(!empty(setting_item('email_ses_key'))){
                        Config::set('services.ses.key',setting_item("email_ses_key"));
                    }
                    if(!empty(setting_item('email_ses_secret'))){
                        Config::set('services.ses.secret',setting_item("email_ses_secret"));
                    }
                    if(!empty(setting_item('email_ses_region'))){
                        Config::set('services.ses.region',setting_item("email_ses_region"));
                    }
                    break;
                case 'sparkpost':
                    if(!empty(setting_item('email_sparkpost_secret'))){
                        Config::set('services.sparkpost.secret',setting_item("email_sparkpost_secret"));
                    }
                    break;
            }
        }
        if(!empty(setting_item('email_host'))){
            Config::set('mail.host',setting_item("email_host"));
        }
        if(!empty(setting_item('email_port'))){
            Config::set('mail.port',setting_item("email_port"));
        }
        if(!empty(setting_item('email_encryption'))){
            Config::set('mail.encryption',setting_item("email_encryption"));
        }
        if(!empty(setting_item('email_username'))){
            Config::set('mail.username',setting_item("email_username"));
        }
        if(!empty(setting_item('email_password'))){
            Config::set('mail.password',setting_item("email_password"));
        }
    }

}
