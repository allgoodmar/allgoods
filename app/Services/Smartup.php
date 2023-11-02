<?php

namespace App\Services;

use App\Warehouse;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Smartup
{
    public $client;

    private $base_url;
    private $login;
    private $password;
    private $filial_id;

    private $warehouses;

    public function __construct()
    {
        $this->base_url = config('services.smartup.base_url');
        $this->filial_id = config('services.smartup.filial_id');
        $this->login = config('services.smartup.login');
        $this->password = config('services.smartup.password');

        $this->warehouses = Warehouse::all();

        $this->client = new Client([
            'base_uri' => $this->base_url,
            'timeout'  => 300.0,
        ]);
    }

    public function saveProductsFiles()
    {
        // delete old files
        File::cleanDirectory(storage_path('app/smartup'));

        // get products
        $response = $this->client->post('/b/es/porting+exp$se_product', [
            'json' => [
                'logon' => [
                    'login' => $this->login,
                    'password' => $this->password,
                    'filial_id' => $this->filial_id,
                ],
            ],
        ]);
        Storage::disk('local')->put('smartup/products.json', $response->getBody());

        // get prices
        $response = $this->client->post('/b/es/porting+exp$se_product_price', [
            'json' => [
                'logon' => [
                    'login' => $this->login,
                    'password' => $this->password,
                    'filial_id' => $this->filial_id,
                ],
            ],
        ]);
        Storage::disk('local')->put('smartup/products-price.json', $response->getBody());

        // get stock
        $warehouses = $this->getWarehouses();
        foreach($warehouses as $warehouse) {
            $response = $this->client->post('/b/es/porting+exp$se_product_balance', [
                'json' => [
                    'logon' => [
                        'login' => $this->login,
                        'password' => $this->password,
                        'filial_id' => $this->filial_id,
                        'warehouse_code' => $warehouse->code,
                    ],
                ],
            ]);
            Storage::disk('local')->put('smartup/warehouses/stock-' . $warehouse->code . '.json', $response->getBody());
        }

    }

    public function setFilialId($id)
    {
        $this->filial_id = $id;
    }

    public function getWarehouses()
    {
        return $this->warehouses;
    }

    public function getCategoriesSynchroNames()
    {
        return [
            'Сенсорный телефон' => 'Смартфоны',
            'Мобильный телефон' => 'Кнопочные телефоны',
            'Планшет' => 'Планшеты',
            'Аксессуары' => 'Гаджеты и аксессуары',
            'Карта памяти' => 'Карты памяти',
            'Кабель' => 'Кабели',
            'Наушники' => 'Проводные наушники',
            // '' => 'Беспроводные наушники',
            'Внешний аккумулятор' => 'Аккумуляторы',
            'Беспроводная зарядка' => 'Зарядные устройства',
            'Сетевая зарядка' => 'Зарядные устройства',
            'Аудиокабель/Адаптер' => 'Кабели',
            // 'Промо продукция' => '',
            'Рюкзаки' => 'Гаджеты и аксессуары',
            'Колонки' => 'Гаджеты и аксессуары',
            'АЗУ держатель' => 'Автодержатели',
            'сим карта' => 'Гаджеты и аксессуары',
            'Защитное стекло' => 'Гаджеты и аксессуары',
            'Чехол' => 'Гаджеты и аксессуары',
            'Чехол книжка' => 'Гаджеты и аксессуары',
            'Чехол-флип' => 'Гаджеты и аксессуары',
            // '' => '',
            // '' => '',
            // '' => '',
        ];
    }
}
