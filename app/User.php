<?php

namespace App;

use App\Events\UserSaved;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Resizable;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, Notifiable, HasFactory, Resizable;

    const GENDER_NOT_SPECIFIED = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'address', 'first_name', 'last_name', 'coupon_sum', 'ref_id', 'card_number', 'card_expiry', 'passport_main_image', 'passport_address_image', 'installment_data_verified', 'installment_limit',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static $imgSizes = [
        'small' => [100, 100],
        'medium' => [400, 400],
        // 'large' => [900, 900],
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'saved' => UserSaved::class,
    ];

    /**
     * Get url
     */
    public function getURLAttribute()
    {
        return LaravelLocalization::localizeURL('user/' . $this->id);
    }

    /**
     * Get main image
     */
    public function getAvatarImgAttribute()
    {
        return ($this->avatar && $this->avatar != 'users/default.png') ? Voyager::image($this->avatar) : asset('images/avatar.png');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function userApplications()
    {
        return $this->hasMany(UserApplication::class);
    }

    public function otps()
    {
        return $this->morphMany(Otp::class, 'otpable');
    }

    public function passportMainImage()
    {
        return $this->belongsTo(Image::class, 'passport_main_image');
    }

    public function passportAddressImage()
    {
        return $this->belongsTo(Image::class, 'passport_address_image');
    }

    public function isPhoneVerified()
    {
        return $this->phone_number_verified_at;
    }

    public function isSeller()
    {
        return $this->role->name == 'seller';
    }

    public function isAdmin()
    {
        return ($this->role->name == 'admin' || $this->role->name == 'administrator');
    }

    public function getImgAttribute()
    {
        return $this->avatar ? Voyager::image($this->avatar) : asset('images/avatar.png');
    }

    public function getSmallImgAttribute()
    {
        return $this->avatar ? Voyager::image($this->getThumbnail($this->avatar, 'small')) : asset('images/avatar.png');
    }

    public function getMediumImgAttribute()
    {
        return $this->avatar ? Voyager::image($this->getThumbnail($this->avatar, 'medium')) : asset('images/avatar.png');
    }

    public function getPassportMainImgAttribute()
    {
        return ($this->passportMainImage && $this->passportMainImage->path) ? Voyager::image($this->passportMainImage->path) : asset('images/no-image.jpg');
    }

    public function getPassportAddressImgAttribute()
    {
        return ($this->passportAddressImage && $this->passportAddressImage->path) ? Voyager::image($this->passportAddressImage->path) : asset('images/no-image.jpg');
    }

    public static function genders()
    {
        return [
            static::GENDER_NOT_SPECIFIED => __('main.gender_not_specified'),
            static::GENDER_MALE => __('main.gender_male'),
            static::GENDER_FEMALE => __('main.gender_female'),
        ];
    }

    public function getGenderTitleAttribute()
    {
        return static::genders()[$this->gender] ?? '';
    }

    public function isInstallmentDataPendingVerification()
    {
        return $this->installment_data_verified == 2;
    }

    public function isInstallmentDataVerified()
    {
        return $this->installment_data_verified == 1;
    }

    public function getInstallmentDataVerifiedTextAttribute()
    {
        $text = 'Не верифицирован';
        if ($this->isInstallmentDataVerified()) {
            $text = 'Верифицирован';
        } elseif ($this->isInstallmentDataPendingVerification()) {
            $text = 'В ожидании проверки';
        }
        return $text;
    }
}
