<?php

namespace App;

use App\Events\ModelDeleted;
use App\Events\ModelSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Translation;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class Category extends Model
{
    use Resizable;
    use Translatable;
    // use Searchable;
    use HasFactory;

    const SHOW_IN_NONE = 0;
	const SHOW_IN_EVERYWHERE = 1;
	const SHOW_IN_MENU = 2;
	const SHOW_IN_HOME = 3;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
    const STATUS_SOON = 3;

    public $additional_attributes = ['full_name'];

    public static $imgSizes = [
        // 'micro' => [70, 70],
        'small' => [212, 212],
        // 'medium' => [400, 400],
    ];

    protected $appends = [
        'url',
        'img',
    ];

    protected $translatable = ['name', 'slug', 'description', 'body', 'seo_title', 'meta_description', 'meta_keywords'];

    protected $guarded = [];

    protected $dispatchesEvents = [
        'saved' => ModelSaved::class,
        'deleted' => ModelDeleted::class,
    ];

    protected static function booted()
    {
        static::saving(function ($category) {
            if ($category->id == $category->parent_id) {
                $category->parent_id = null;
            }
        });
    }

    public function searches()
    {
        return $this->morphMany(Search::class, 'searchable');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }

    public function targetBanners()
    {
        return $this->morphMany(Banner::class, 'targetable');
    }

    public function products()
    {
        // return $this->hasMany(Product::class);
        return $this->belongsToMany(Product::class);
    }

    public function trendyolCategories()
    {
        return $this->belongsToMany(TrendyolCategory::class, 'category_trendyol_category');
    }

    /**
     * Get all products.
     */
    public function allProducts()
    {
        $childrenIds = $this->childrenIds($this);
        return Product::whereIn('category_id', $childrenIds);
        // return Product::whereHas('categories', function ($query) use ($childrenIds) {
        //     $query->whereIn('category_id', $childrenIds);
        // });
    }

    /**
     * Get all attribute values.
     */
    public function allBrands($query = false)
    {
        $locale = app()->getLocale();
        if (!$query) {
            $query = $this->products()->active();
        }
        $query->whereNotNull('products.brand_id');
        $brandIds = $query->groupBy('products.brand_id')->pluck('brand_id');
        if ($brandIds->isEmpty()) {
            return collect();
        }
        return Brand::active()->whereIn('id', $brandIds)->withTranslation($locale)->get();
    }

    /**
     * Get all attribute values.
     */
    public function allAttributeValueIds($query = false, $onlyUsedForFilter = true)
    {
        if (!$query) {
            $query = $this->products()->active();
        }
        $productIds = $query->pluck('products.id');

        $attributeValueIdsQuery = DB::table('attribute_value_product')->whereIn('attribute_value_product.product_id', $productIds)->groupBy('attribute_value_product.attribute_value_id')->leftJoin('attribute_values', 'attribute_value_product.attribute_value_id', '=', 'attribute_values.id');
        if ($onlyUsedForFilter) {
            $attributeValueIdsQuery->where('attribute_values.used_for_filter', 1);
        }
        $attributeValueIds = $attributeValueIdsQuery->pluck('attribute_value_product.attribute_value_id');
        return $attributeValueIds;
    }

    /**
     * Get all attribute values.
     */
    public function allAttributes($query = false, $onlyUsedForFilter = true)
    {
        $locale = app()->getLocale();
        $attributeValueIds = $this->allAttributeValueIds($query, $onlyUsedForFilter);
        $attributeIds = DB::table('attribute_values')
            ->leftJoin('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->whereIn('attribute_values.id', $attributeValueIds->toArray())
            ->pluck('attributes.id')
            ->unique();
        $attributesQuery = Attribute::whereIn('attributes.id', $attributeIds->toArray())
            ->withTranslation($locale)
            ->with(['attributeValues' => function ($q1) use ($locale, $attributeValueIds) {
                $q1->whereIn('id', $attributeValueIds->toArray())
                    ->withTranslation($locale);
            }]);
        if ($onlyUsedForFilter) {
            $attributesQuery->where('used_for_filter', 1);
        }
        return $attributesQuery->get();
    }

    /**
     * Get url
     */
    public function getURLAttribute()
    {
        return $this->getURL();
    }

    /**
     * Get url
     */
    public function getURL($lang = '')
    {
        if (!$lang) {
            $lang = app()->getLocale();
        }
        $slug = $this->getTranslatedAttribute('slug', $lang) ?: $this->slug;
        $url = 'category/' . $this->id . '-' . $slug;
        return LaravelLocalization::localizeURL($url, $lang);
    }

    /**
     * Get main image
     */
    public function getImgAttribute()
    {
        return $this->image ? Voyager::image($this->image) : asset('images/no-image.jpg');
    }

    /**
     * Get micro image
     */
    public function getMicroImgAttribute()
    {
        return $this->image ? Voyager::image($this->getThumbnail($this->image, 'micro')) : asset('images/no-image.jpg');
    }

    /**
     * Get small image
     */
    public function getSmallImgAttribute()
    {
        return $this->image ? Voyager::image($this->getThumbnail($this->image, 'small')) : asset('images/no-image.jpg');
    }

    /**
     * Get medium image
     */
    public function getMediumImgAttribute()
    {
        return $this->image ? Voyager::image($this->getThumbnail($this->image, 'medium')) : asset('images/no-image.jpg');
    }

    /**
     * Get large image
     */
    public function getLargeImgAttribute()
    {
        return $this->image ? Voyager::image($this->getThumbnail($this->image, 'large')) : asset('images/no-image.jpg');
    }

    /**
     * Get main icon
     */
    public function getIconImgAttribute()
    {
        return $this->icon ? Voyager::image($this->icon) : asset('images/no-image.jpg');
    }

    /**
     * Get micro icon
     */
    public function getMicroIconImgAttribute()
    {
        return $this->icon ? Voyager::image($this->getThumbnail($this->icon, 'micro')) : asset('images/no-image.jpg');
    }

    /**
     * Get svg icon
     */
    public function getSvgIconImgAttribute()
    {
        return $this->svg_icon ?: '<svg width="24" height="24" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg">
        <g id="#000000ff">
        <path fill="#000000" opacity="1.00" d=" M 250.86 0.00 L 260.12 0.00 C 299.13 0.75 338.01 10.20 372.70 28.11 C 423.71 54.10 465.45 97.78 489.05 149.95 C 503.52 181.54 511.13 216.13 512.00 250.84 L 512.00 260.12 C 511.23 302.14 500.22 343.99 479.64 380.67 C 462.82 410.83 439.81 437.50 412.50 458.62 C 379.85 483.92 340.98 501.10 300.28 508.14 C 287.09 510.53 273.69 511.55 260.30 512.00 L 251.88 512.00 C 204.51 511.18 157.39 497.17 117.59 471.39 C 65.90 438.31 26.78 386.08 10.03 327.00 C 3.69 305.34 0.57 282.83 0.00 260.30 L 0.00 251.04 C 0.49 238.36 1.42 225.67 3.61 213.16 C 10.56 171.70 27.95 132.02 53.87 98.91 C 79.43 65.92 113.23 39.36 151.33 22.33 C 182.56 8.25 216.65 0.86 250.86 0.00 M 244.22 40.35 C 205.16 42.40 166.72 55.30 134.46 77.44 C 126.27 83.55 117.82 89.37 110.53 96.58 C 103.25 102.63 97.21 109.92 90.82 116.85 C 88.41 119.52 86.37 122.49 84.21 125.36 C 51.87 166.77 36.50 220.65 40.71 272.91 C 43.53 310.44 56.45 347.18 77.86 378.13 C 82.96 384.70 87.48 391.76 93.23 397.80 C 101.02 406.99 109.89 415.13 118.99 423.00 C 128.18 429.93 137.26 437.09 147.35 442.71 C 181.54 462.77 221.39 472.88 260.99 471.89 C 306.37 470.94 351.54 455.39 387.29 427.30 C 395.05 421.87 401.71 415.09 408.72 408.76 C 415.06 401.75 421.85 395.07 427.30 387.30 C 450.82 357.32 465.65 320.70 470.28 282.91 C 476.96 230.59 463.81 175.85 433.31 132.73 C 428.48 126.55 424.24 119.89 418.79 114.23 C 414.40 109.59 410.42 104.55 405.62 100.33 C 399.59 95.01 393.95 89.24 387.29 84.69 C 347.12 53.11 295.14 37.50 244.22 40.35 Z" />
        <path fill="#000000" opacity="1.00" d=" M 339.84 175.82 C 344.40 170.90 351.26 168.06 357.97 169.18 C 367.11 170.40 374.67 178.70 374.94 187.93 C 375.36 194.07 372.49 199.99 368.08 204.14 C 323.92 248.28 279.79 292.45 235.62 336.58 C 230.45 342.07 222.23 344.50 214.99 342.15 C 210.20 340.82 206.60 337.21 203.24 333.76 C 183.43 313.91 163.59 294.11 143.77 274.27 C 135.11 266.84 134.77 252.14 143.43 244.58 C 149.02 239.26 157.83 237.78 164.88 240.92 C 168.96 242.58 171.99 245.91 175.04 248.97 C 190.30 264.26 205.57 279.53 220.84 294.81 C 260.51 255.15 300.17 215.48 339.84 175.82 Z" />
        </g>
        </svg>';
    }

    /**
     * Get full name (with parents' names)
     */
    public function getFullNameAttribute()
    {
        $names = array_reverse($this->fullName($this));
        $name = array_pop($names);
        if (count($names)) {
            $name .= ' (' . implode(' > ', $names) . ')';
        }
        return $name;
    }

    /**
     * Get full name (with parents' names)
     */
    public function getHierarchyNameAttribute()
    {
        $names = array_reverse($this->fullName($this));
        return implode(' > ', $names);
    }

    /**
     * Voyager name browse accessor
     */
    public function getNameBrowseAttribute()
    {
        return $this->full_name;
    }

    public function parentId()
    {
        return $this->belongsTo(self::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * scope active
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
        ->orWhere('status', self::STATUS_SOON);
    }

    /**
     * scope active
     */
    public function scopeParents($query)
    {
        return $query->where('parent_id', null);
    }

    public function childrenIds($category, $ids = [])
    {
        $ids[] = $category->id;
        if (!$category->children->isEmpty()) {
            foreach ($category->children as $child) {
                if (!in_array($child->id, $ids)) {
                    $ids = $this->childrenIds($child, $ids);
                }
            }
        }
        return $ids;
    }

    private function fullName($category)
    {
        $ids = self::parentIDs($category);
        $categories = Category::whereIn('id', $ids)->withTranslation(app()->getLocale())->get();
        $names = [];
        foreach ($ids as $id) {
            $c = $categories->where('id', $id)->first();
            if ($c) {
                $names[] = $c->getTranslatedAttribute('name');
            }
        }
        return $names;
    }

    public static function parentIDs($category, $ids = [])
    {
        $ids[] = $category->id;
        if ($category->parent && !in_array($category->parent->id, $ids)) {
            $ids = self::parentIDs($category->parent, $ids);
        }
        return $ids;
    }

    /**
     * Get entries filtered by translated value.
     *
     * @param string $field {required} the field your looking to find a value in.
     * @param string $operator {required} value you are looking for or a relation modifier such as LIKE, =, etc.
     * @param string $value {optional} value you are looking for. Only use if you supplied an operator.
     * @param string|array $locales {optional} locale(s) you are looking for the field.
     * @param bool $default {optional} if true checks for $value is in default database before checking translations.
     *
     * @return Builder
     * @example  Class::whereTranslation('title', '=', 'zuhause', ['de', 'iu'])
     * @example  $query->whereTranslation('title', '=', 'zuhause', ['de', 'iu'])
     *
     */
    public static function scopeOrWhereTranslation($query, $field, $operator, $value = null, $locales = null, $default = true)
    {
        if ($locales && !is_array($locales)) {
            $locales = [$locales];
        }
        if (!isset($value)) {
            $value = $operator;
            $operator = '=';
        }

        $self = new static();
        $table = $self->getTable();

        return $query->whereIn($self->getKeyName(), Translation::where('table_name', $table)
            ->where('column_name', $field)
            ->orWhere('value', $operator, $value)
            ->when(!is_null($locales), function ($query) use ($locales) {
                return $query->whereIn('locale', $locales);
            })
            ->pluck('foreign_key')
        )->when($default, function ($query) use ($field, $operator, $value) {
            return $query->orWhere($field, $operator, $value);
        });
    }

    public static function showInPlaces()
    {
        return [
            self::SHOW_IN_NONE => 'Не показывать',
            self::SHOW_IN_EVERYWHERE => 'Везде',
            self::SHOW_IN_MENU => 'В меню',
            self::SHOW_IN_HOME => 'На главной странице',
        ];
    }
}
