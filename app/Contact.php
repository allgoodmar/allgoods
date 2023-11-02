<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    const TYPE_CONTACT = 0;
    const TYPE_CALLBACK = 1;
    const TYPE_ORDER = 2;
    const TYPE_INSTALLMENT_PAYMENT = 3;

    protected $fillable = ['name', 'phone', 'email', 'subject', 'info', 'message'];

    public function save(array $options = [])
    {
        // add ip address and user agent
        if (!$this->ip_address && request()->ip()) {
            $this->ip_address = request()->ip();
        }
        if (!$this->user_agent && request()->header('User-Agent')) {
            $this->user_agent = request()->header('User-Agent');
        }

        parent::save();
    }

    public static function types()
    {
        return [
            static::TYPE_CONTACT => __('main.contact_type_contact'),
            static::TYPE_CALLBACK => __('main.contact_type_callback'),
            static::TYPE_ORDER => __('main.contact_type_order'),
            static::TYPE_INSTALLMENT_PAYMENT => __('main.contact_type_installment_payment'),
        ];
    }

    public function getTypeTitleAttribute()
    {
        return static::types()[$this->type];
    }
}
