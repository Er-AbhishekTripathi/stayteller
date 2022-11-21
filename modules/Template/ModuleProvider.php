<?php
namespace Modules\Template;

use Modules\ModuleServiceProvider;

class ModuleProvider extends ModuleServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
	    $this->app->register(RouterServiceProvider::class);

    }


    public static function getTemplateBlocks(){
        return [
            // 'row'=>"\\Modules\\Template\\Blocks\\Row",
            // 'column'=>"\\Modules\\Template\\Blocks\\Column",
            'text'=>"\\Modules\\Template\\Blocks\\Text",
            'call_to_action'=>"\\Modules\\Property\\Blocks\\CallToAction",
            'faqs'=>"\\Modules\\Template\\Blocks\\FaqList",
            'testimonial'=>"\\Modules\\Property\\Blocks\\Testimonial",
            'form_search_all_service'=>"\\Modules\\Template\\Blocks\\FormSearchAllService",
            'offer_block'=>"\\Modules\\Template\\Blocks\\OfferBlock",
            'map'=>"\\Modules\\Template\\Blocks\\Map",
            'banner_property'=>"\\Modules\\Template\\Blocks\\BannerProperty"
        ];
    }
}
