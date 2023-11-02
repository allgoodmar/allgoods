<?php

namespace App;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Order extends Model
{
    const STATUS_CANCELLED_AFTER_PAYMENT = -2;
    const STATUS_CANCELLED = -1;
    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_PAID = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_PENDING_PAYMENT = 4;
    const STATUS_REFUND = 5;
    const STATUS_REFUND_REQUESTED = 6;
    const STATUS_ACCEPTED = 7;
    const STATUS_SENT_FOR_DELIVERY = 8;
    const STATUS_COURIER = 9;
    const STATUS_DELIVERED = 10;

    const COMMUNICATION_METHOD_PHONE = 0;
    const COMMUNICATION_METHOD_SMS = 1;
    const COMMUNICATION_METHOD_TELEGRAM = 2;

    const PAYMENT_METHOD_CASH = 1;
    const PAYMENT_METHOD_UZCARD = 2;
    const PAYMENT_METHOD_HUMO = 3;
    const PAYMENT_METHOD_PAYME = 4;
    const PAYMENT_METHOD_CLICK = 5;
    const PAYMENT_METHOD_APELSIN = 6;
    const PAYMENT_METHOD_INTERNATIONAL_CARD = 7;
    const PAYMENT_METHOD_CARD = 8;
    const PAYMENT_METHOD_ZOODPAY_INSTALLMENTS = 9;
    const PAYMENT_METHOD_INSTALLMENTS = 10;
    const PAYMENT_METHOD_INTEND = 11;
    const PAYMENT_METHOD_ALIFSHOP = 12;
    const PAYMENT_METHOD_ALLGOOD = 13;
    const PAYMENT_METHOD_HUSBAND = 14;
    const PAYMENT_METHOD_ANORBANK = 15;
    const PAYMENT_METHOD_UZUM = 16;

    const TYPE_BUY_IMMEDIATELY = 0;
    const TYPE_INSTALLMENT = 1;

    const SOURCE_SITE = 0;
    const SOURCE_API = 1;
    const SOURCE_TELEGRAM = 2;

    protected $guarded = [];

    protected $casts = [
        'delivered_at' => 'datetime:Y-m-d',
    ];

    protected static function boot()
    {
        parent::boot();
        self::updating(function ($model) {

        });
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function atmosTransactions()
    {
        return $this->morphMany(AtmosTransaction::class, 'payable');
    }

    /**
     * Get url
     */
    public function getURLAttribute()
    {
        return LaravelLocalization::localizeURL('order/' . $this->id . '-' . md5($this->created_at));
    }

    /**
     * Get status title
     */
    public function getStatusTitleAttribute()
    {
        return static::statuses()[$this->status];
    }

    /**
     * Get communication method title
     */
    public function getCommunicationMethodTitleAttribute()
    {
        return static::communicationMethods()[$this->communication_method];
    }

    /**
     * Get payment method title
     */
    public function getPaymentMethodIdReadAttribute()
    {
        return $this->payment_method_title;
    }

    public function getPaymentMethodTitleAttribute()
    {
        return Helper::paymentMethodTitle($this->payment_method_id);
    }

    public function getSourceTitleAttribute()
    {
        return static::sources()[$this->source_id] ?? '';
    }

    public static function statuses() {
        return [
            static::STATUS_CANCELLED_AFTER_PAYMENT => __('main.order_status_cancelled_after_payment'),
            static::STATUS_CANCELLED => __('main.order_status_cancelled'),

            static::STATUS_PENDING => __('main.order_status_accepted'),
            //static::STATUS_PENDING => __('main.order_status_pending'),

            // static::STATUS_PROCESSING => __('main.order_status_processing'),
            static::STATUS_PAID => __('main.order_status_paid'),
            // static::STATUS_COMPLETED => __('main.order_status_completed'),
            // static::STATUS_PENDING_PAYMENT => __('main.order_status_pending_payment'),
            static::STATUS_REFUND => __('main.order_status_refund'),
            static::STATUS_REFUND_REQUESTED => __('main.order_status_refund_requested'),
            static::STATUS_ACCEPTED => __('main.order_status_accepted'),
            static::STATUS_SENT_FOR_DELIVERY => __('main.order_status_sent_for_delivery'),
            static::STATUS_COURIER => __('main.order_status_courier'),
            static::STATUS_DELIVERED => __('main.order_status_delivered'),
        ];
    }

    public static function statusDescriptions() {
        return [
            static::STATUS_ACCEPTED => __('main.order_status_accepted_description'),
            static::STATUS_SENT_FOR_DELIVERY => __('main.order_status_sent_for_delivery_description'),
            static::STATUS_COURIER => __('main.order_status_courier_description'),
            static::STATUS_DELIVERED => __('main.order_status_delivered_description'),
        ];
    }

    public static function communicationMethods() {
        return [
            static::COMMUNICATION_METHOD_PHONE => __('main.communication_method_phone'),
            // static::COMMUNICATION_METHOD_SMS => __('main.communication_method_sms'),
            static::COMMUNICATION_METHOD_TELEGRAM => __('main.communication_method_telegram'),
        ];
    }

    public static function types()
    {
        return [
            static::TYPE_BUY_IMMEDIATELY => __('main.buy_immediately'),
            // static::TYPE_INSTALLMENT => __('main.place_installment_order_through'),
        ];
    }

    public static function sources()
    {
        return [
            static::SOURCE_SITE => 'Site',
            static::SOURCE_API => 'API',
            static::SOURCE_TELEGRAM => 'Telegram',
        ];
    }

    public function isInstallmentOrder()
    {
        return $this->type == static::TYPE_INSTALLMENT;
    }

    public function isPending()
    {
        return $this->status == static::STATUS_PENDING;
    }

    public function isPendingPayment()
    {
        return $this->status == static::STATUS_PENDING_PAYMENT;
    }

    public function isPaid()
    {
        return $this->status == static::STATUS_PAID;
    }

    public function setPaid()
    {
        $this->status = static::STATUS_PAID;
        $this->save();
    }

    public function isCancelled()
    {
        return $this->status == static::STATUS_CANCELLED;
    }

    public function setCancelled()
    {
        $this->status = static::STATUS_CANCELLED;
        $this->save();
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getTotalReadAttribute()
    {
        return Helper::formatPrice($this->total);
    }

    public function getTotalTiyinAttribute()
    {
        return round($this->total * 100);
    }

    public function paycomTransactions()
    {
        return $this->hasMany(PaycomTransaction::class, 'order_id');
    }

    public function zoodpayTransactions()
    {
        return $this->hasMany(ZoodpayTransaction::class, 'order_id');
    }
}
