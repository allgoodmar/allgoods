<?php

namespace App;

use App\Shop;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class Banner extends Model
{
    use Resizable;
    use Translatable;

    protected $translatable = ['name', 'description_top', 'description', 'description_bottom', 'button_text', 'url'];

    /**
     * Statuses.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 2;

    /**
     * Types.
     */
    const TYPE_SLIDE = 'slide';
    const TYPE_HOME_1 = 'home_1';
    const TYPE_HOME_2 = 'home_2';
    const TYPE_HOME_3 = 'home_3';
    const TYPE_LINE_1 = 'line_1';
    const TYPE_MIDDLE_1 = 'middle_1';
    const TYPE_MIDDLE_2 = 'middle_2';
    const TYPE_MIDDLE_3 = 'middle_3';
    const TYPE_MOBILE_MENU = 'mobile_menu';
    const TYPE_SIDEBAR_1 = 'sidebar_1';
    const TYPE_SIDEBAR_2 = 'sidebar_2';
    const TYPE_MENU_CATEGORY = 'menu_category';
    const TYPE_APP_SLIDE = 'app_slide';
    const TYPE_APP_HOME = 'app_home';
    const TYPE_APP_CATEGORY = 'app_category';


    /**
     * List of statuses.
     *
     * @var array
     */
    public static $statuses = [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_PENDING];

    protected $guarded = [];

    /**
     * Scope a query to only include active banners.
     *
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include active banners.
     *
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNowActive($query)
    {
        return $query->where([['active_from', '>=', date('Y-m-d H:i:s')], ['active_to', '<=', date('Y-m-d H:i:s')]]);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     *
     */
    public function bannerStats()
    {
        return $this->hasMany(BannerStats::class);
    }

    /**
     * Get main image
     */
    public function getImgAttribute()
    {
        $locale = app()->getLocale();
        $localeImage = 'image_' . $locale;
        $image = !empty($this->$localeImage) ? $this->$localeImage : $this->image;
        return $image ? Voyager::image($image) : asset('images/no-image.jpg');
    }

    /**
     * Get increment clicks url
     */
    public function getIncrementClicksUrlAttribute()
    {
        return route('banner.increment.clicks', $this->id);
    }

    /**
     * Get increment views url
     */
    public function getIncrementViewsUrlAttribute()
    {
        return route('banner.increment.views', $this->id);
    }

    public static function types()
    {
        return [
            self::TYPE_SLIDE => 'Слайд (360x240)',
            // self::TYPE_HOME_1 => 'Главная 1',
            // self::TYPE_HOME_2 => 'Главная 2',
            // self::TYPE_HOME_3 => 'Главная 3',
            self::TYPE_LINE_1 => 'Полоска 1 (1390x130)',
            self::TYPE_MIDDLE_1 => 'Середина 1 (450x210)',
            self::TYPE_MIDDLE_2 => 'Середина 2 (450x210)',
            self::TYPE_MIDDLE_3 => 'Середина 3 (450x210)',
            // self::TYPE_MOBILE_MENU => 'Мобильное меню',
            // self::TYPE_SIDEBAR_1 => 'Левая колонка 1 (382x534)',
            // self::TYPE_SIDEBAR_2 => 'Левая колонка 2 (382x534)',
            self::TYPE_MENU_CATEGORY => 'Категории в меню (360x440)',
            self::TYPE_APP_SLIDE => 'Слайд на главной странице в приложении (175x110)',
            self::TYPE_APP_HOME => 'Главная страница приложения нижние баннеры (150x114)',
            self::TYPE_APP_CATEGORY => 'Баннер в категории приложения (396x120)',
        ];
    }

    public function scopeMenuCategory($query)
    {
        return $query->where('type', self::TYPE_MENU_CATEGORY);
    }

    public function targetable()
    {
        return $this->morphTo();
    }

    public static function targetTypes()
    {
        return [
            '-' => 'all',
            Category::class => 'category',
            Brand::class => 'brand',
            Product::class => 'product',
            Publication::class => 'publication',
            Page::class => 'page',
        ];
    }

    public function getTargetTypeAttribute()
    {
        return static::targetTypes()[$this->targetable_type] ?? '';
    }
}
