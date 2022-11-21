<?php
namespace Modules\Agencies;

use Illuminate\Support\ServiceProvider;
use Modules\ModuleServiceProvider;
use App\User;
use Modules\Agencies\Models\Agencies;

class ModuleProvider extends ModuleServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
    }

    public static function getReviewableServices()
    {
        return [
            'agencies' => Agencies::class,
            'agent'    => User::class
        ];
    }

    public static function getAdminMenu()
    {
        $res = [];
        if (Agencies::isEnable()) {
            $res['agencies'] = [
                "position"   => 45,
                'url'        => 'admin/module/agencies',
                'title'      => __("Owners"),
                'icon'       => 'icon ion-md-umbrella',
                'permission' => 'agencies_view',
                'children'   => [
                    'agency_view'=>[
                        'url'        => 'admin/module/agencies',
                        'title'      => __('All Owner'),
                        'permission' => 'agencies_view',
                    ],
                    'agency_create'=>[
                        'url'        => 'admin/module/agencies/form',
                        'title'      => __("Add Owner"),
                        'permission' => 'agencies_create',
                    ],
                    // 'agency_contact'=>[
                    //     'url'        => '',
                    //     'title'      => __('Agency contact'),
                    //     'permission' => 'agencies_view',
                    // ],
                ],
            ];
        }
        return $res;
    }


    public static function getUserMenu()
    {
        $res = [];
        if(is_agency_owner()){
            if(Agencies::isEnable()){
                $res['agencies'] = [
                    'title'      => __("Manage Owner"),
                    'icon'       => 'icon ion-md-umbrella',
                    'permission' => 'agencies_view',
                    'position'   => 31,
                    'url'        => route('agency.vendor.index'),
                ];
            }
        }

        return $res;
    }

    public static function getMenuBuilderTypes()
    {
        if(!Agencies::isEnable()) return [];

        return [
            [
                'class' => Agencies::class,
                'name'  => __("Owners"),
                'items' => Agencies::searchForMenu(),
                'position'=> 20
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Agencies::isEnable()) return [];

        return [
            'our_team'=>"\\Modules\\Agencies\\Blocks\\OurTeam",
            'partners'=>"\\Modules\\Agencies\\Blocks\\Partners",
        ];
    }
}
