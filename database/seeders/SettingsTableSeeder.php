<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $setting = $this->findSetting('site.title');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.site.title'),
                //'value'        => __('seeders.settings.site.title'),
                'value'        => 'AllGood.UZ',
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
                'group'        => 'Site',
            ])->save();
        }

        // $setting = $this->findSetting('site.description');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.site.description'),
        //         'value'        => __('seeders.settings.site.description'),
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 2,
        //         'group'        => 'Site',
        //     ])->save();
        // }

        $setting = $this->findSetting('site.logo');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.site.logo'),
                'value'        => 'settings/logo_dark.png',
                'details'      => '',
                'type'         => 'image',
                'order'        => 3,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.logo_light');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.site.logo_light'),
                'value'        => 'settings/logo_light.png',
                'details'      => '',
                'type'         => 'image',
                'order'        => 4,
                'group'        => 'Site',
            ])->save();
        }

        // $setting = $this->findSetting('site.favicon');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.site.favicon_ico'),
        //         'value'        => '',
        //         'details'      => '',
        //         'type'         => 'file',
        //         'order'        => 4,
        //         'group'        => 'Site',
        //     ])->save();
        // }

        $setting = $this->findSetting('site.google_analytics_code');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.site.google_analytics_code'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text_area',
                'order'        => 10,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.yandex_metrika_code');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.site.yandex_metrika_code'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text_area',
                'order'        => 11,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.facebook_pixel_code');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.site.facebook_pixel_code'),
                'value'        => '',
                'details'      => '',
                'type'         => 'text_area',
                'order'        => 12,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.jivochat_code');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Код Jivochat',
                'value'        => '',
                'details'      => '',
                'type'         => 'text_area',
                'order'        => 13,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.share_buttons_code');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Код кнопки Поделиться',
                'value'        => '<img src="/images/share.jpg" alt="Share" class="img-fluid">',
                'details'      => '',
                'type'         => 'text_area',
                'order'        => 20,
                'group'        => 'Site',
            ])->save();
        }

        // $setting = $this->findSetting('site.day_product');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => 'Товар дня (ID товара)',
        //         'value'        => '',
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 30,
        //         'group'        => 'Site',
        //     ])->save();
        // }

        $setting = $this->findSetting('site.catalog_category_ids');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Каталог (ID категорий)',
                'value'        => '1,2,3',
                'details'      => '',
                'type'         => 'text',
                'order'        => 31,
                'group'        => 'Site',
            ])->save();
        }
        $setting = $this->findSetting('site.home_category_ids');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Категории на главной стр (ID категорий)',
                'value'        => '1,2,3',
                'details'      => '',
                'type'         => 'text',
                'order'        => 32,
                'group'        => 'Site',
            ])->save();
        }

        $setting = $this->findSetting('site.allgo_main_category_id');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Основная категория страницы Allgo (ID)',
                'value'        => '1',
                'details'      => '',
                'type'         => 'text',
                'order'        => 33,
                'group'        => 'Site',
            ])->save();
        }
        $setting = $this->findSetting('site.allgo_subcategory_ids');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'Подкатегории страницы Allgo (ID категорий)',
                'value'        => '1,2,3',
                'details'      => '',
                'type'         => 'text',
                'order'        => 34,
                'group'        => 'Site',
            ])->save();
        }



        // $setting = $this->findSetting('site.counters');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => 'Счетчики',
        //         'value'        => '
        //                             <a href="#" class="d-inline-block mb-2 mb-lg-0">
        //                                 <img src="/images/counter-1.png" alt="">
        //                             </a>
        //                             <a href="#" class="d-inline-block mb-2 mb-lg-0">
        //                                 <img src="/images/counter-2.png" alt="">
        //                             </a>
        //                             <a href="#" class="d-inline-block mb-2 mb-lg-0">
        //                                 <img src="/images/counter-3.png" alt="">
        //                             </a>
        //                             ',
        //         'details'      => '',
        //         'type'         => 'text_area',
        //         'order'        => 15,
        //         'group'        => 'Site',
        //     ])->save();
        // }

        // $setting = $this->findSetting('admin.bg_image');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.admin.background_image'),
        //         'value'        => '',
        //         'details'      => '',
        //         'type'         => 'image',
        //         'order'        => 5,
        //         'group'        => 'Admin',
        //     ])->save();
        // }

        $setting = $this->findSetting('admin.title');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.admin.title'),
                'value' => __('seeders.settings.admin.title_value'),
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
                'group'        => 'Admin',
            ])->save();
        }

        $setting = $this->findSetting('admin.description');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.admin.description'),
                'value'        => __('seeders.settings.admin.description_value'),
                'details'      => '',
                'type'         => 'text',
                'order'        => 2,
                'group'        => 'Admin',
            ])->save();
        }

        // $setting = $this->findSetting('admin.loader');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.admin.loader'),
        //         'value'        => '',
        //         'details'      => '',
        //         'type'         => 'image',
        //         'order'        => 3,
        //         'group'        => 'Admin',
        //     ])->save();
        // }

        // $setting = $this->findSetting('admin.icon_image');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.admin.icon_image'),
        //         'value'        => '',
        //         'details'      => '',
        //         'type'         => 'image',
        //         'order'        => 4,
        //         'group'        => 'Admin',
        //     ])->save();
        // }

        // $setting = $this->findSetting('admin.google_analytics_client_id');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.admin.google_analytics_client_id'),
        //         'value'        => '',
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 1,
        //         'group'        => 'Admin',
        //     ])->save();
        // }

        $setting = $this->findSetting('contact.email');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.email'),
                'value'        => 'info@allgood.uz',
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
                'group'        => 'Contact',
            ])->save();
        }

        $setting = $this->findSetting('contact.phone');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.phone'),
                'value'        => '+998 99 3987700',
                'details'      => '',
                'type'         => 'text',
                'order'        => 2,
                'group'        => 'Contact',
            ])->save();
        }

        // $setting = $this->findSetting('contact.fax');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.contact.fax'),
        //         'value'        => __('seeders.settings.contact.fax_value'),
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 3,
        //         'group'        => 'Contact',
        //     ])->save();
        // }

        // $setting = $this->findSetting('contact.address');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.contact.address'),
        //         'value'        => __('seeders.settings.contact.address_value'),
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 4,
        //         'group'        => 'Contact',
        //     ])->save();
        // }

        // $setting = $this->findSetting('contact.landmark');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.contact.landmark'),
        //         'value'        => __('seeders.settings.contact.landmark_value'),
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 5,
        //         'group'        => 'Contact',
        //     ])->save();
        // }

        $setting = $this->findSetting('contact.map');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.map'),
                'value'        => '<script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Ab8b3646169d556c091b2246fdf2900904bb3f2f032e992abf228d2bf6035293a&amp;width=100%25&amp;height=400&amp;lang=ru_RU&amp;scroll=true"></script>',
                'details'      => '',
                'type'         => 'text',
                'order'        => 6,
                'group'        => 'Contact',
            ])->save();
        }

        // $setting = $this->findSetting('contact.work_hours');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => __('seeders.settings.contact.work_hours'),
        //         'value'        => '9:00–18:00',
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 7,
        //         'group'        => 'Contact',
        //     ])->save();
        // }

        $setting = $this->findSetting('contact.telegram');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.telegram'),
                'value'        => __('seeders.settings.contact.telegram_value'),
                'details'      => '',
                'type'         => 'text',
                'order'        => 20,
                'group'        => 'Contact',
            ])->save();
        }

        $setting = $this->findSetting('contact.facebook');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.facebook'),
                'value'        => __('seeders.settings.contact.facebook_value'),
                'details'      => '',
                'type'         => 'text',
                'order'        => 21,
                'group'        => 'Contact',
            ])->save();
        }

        $setting = $this->findSetting('contact.instagram');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.instagram'),
                'value'        => __('seeders.settings.contact.instagram_value'),
                'details'      => '',
                'type'         => 'text',
                'order'        => 22,
                'group'        => 'Contact',
            ])->save();
        }

        $setting = $this->findSetting('contact.youtube');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.youtube'),
                'value'        => __('seeders.settings.contact.youtube_value'),
                'details'      => '',
                'type'         => 'text',
                'order'        => 23,
                'group'        => 'Contact',
            ])->save();
        }

        $setting = $this->findSetting('contact.tiktok');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => __('seeders.settings.contact.tiktok'),
                'value'        => __('seeders.settings.contact.tiktok_value'),
                'details'      => '',
                'type'         => 'text',
                'order'        => 24,
                'group'        => 'Contact',
            ])->save();
        }

        $setting = $this->findSetting('currency.usd');
        if (!$setting->exists) {
            $setting->fill([
                'display_name' => 'USD',
                'value'        => 0,
                'details'      => '',
                'type'         => 'text',
                'order'        => 1,
                'group'        => 'Currency',
            ])->save();
        }

        // $setting = $this->findSetting('currency.eur');
        // if (!$setting->exists) {
        //     $setting->fill([
        //         'display_name' => 'EUR',
        //         'value'        => 0,
        //         'details'      => '',
        //         'type'         => 'text',
        //         'order'        => 2,
        //         'group'        => 'Currency',
        //     ])->save();
        // }
    }

    /**
     * [setting description].
     *
     * @param [type] $key [description]
     *
     * @return [type] [description]
     */
    protected function findSetting($key)
    {
        return Setting::firstOrNew(['key' => $key]);
    }
}
