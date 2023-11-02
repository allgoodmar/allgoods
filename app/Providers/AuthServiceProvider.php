<?php

namespace App\Providers;

use App\Address;
use App\Card;
use App\Order;
use App\Policies\AddressPolicy;
use App\Policies\CardPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Product;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Product::class => ProductPolicy::class,
        Order::class => OrderPolicy::class,
        Address::class => AddressPolicy::class,
        Card::class => CardPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
