<?php
namespace  Modules\Agencies;

use Modules\Core\Abstracts\BaseSettingsClass;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'id' => 'agencies',
                'title' => __("Agencies Settings"),
                'position' => 20,
                'view' => "Agencies::admin.settings.agency",
                "keys"=>[
                    'agency_disable',
                    'agency_page_search_title',
                    'agency_page_search_banner',
                    'agency_layout_search',
                    'agency_location_search_style',
                    'agency_enable_review',
                    'agency_review_approved',
                    'agency_review_number_per_page',
                    'agency_review_stats',
                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}
