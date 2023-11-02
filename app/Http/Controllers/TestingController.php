<?php

namespace App\Http\Controllers;

use App\Order;
use App\Services\Zoodpay;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestingController extends Controller
{
    public function index()
    {
        // $dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', '2021-05-18T10:58:30Z');
        // dd($dateTime);
        $this->zoodpay();
    }

    private function zoodpay()
    {
        $order = Order::with('orderItems.product.categories')->findOrFail(28);
        dump($order);
        $zoodpay = new Zoodpay();
        $merchSignature = $zoodpay->generateMerchantSignature($order);
        $zoodSignature1 = $zoodpay->generateZoodpaySignature($order, '60ab88d505718', true);
        $zoodSignature2 = $zoodpay->generateZoodpaySignature($order, '60ab88d505718');
        // dump($merchSignature);
        dump('4cfe3c5ec2a1b90e4c8df7a41a0f2ea7dc9651243889ba480fb3877530d12b62d353414cc87c513490cc1ce5eef77e8d7f444428f04c456663f5f15a32cff2b1');
        dump($zoodSignature1);
        dump($zoodSignature2);
    }

    private function opersmsTest()
    {
        // opersms - di art diart
        $data = [
            ['phone' => '998908081239', 'text' => utf8_encode(iconv('windows-1251', 'utf-8', 'Тестовое сообщение от xplore.uz'))],
            // Если сообщения приходят в неправильной кодировке, используйте iconv:
            //['phone' => 'NUMBER', 'text' => utf8_encode(iconv('windows-1251', 'utf-8', 'TEXT'))],
        ];

        $client = new Client();
        $query = [
            "login" => "xplore",
            "password" => "iwPhBSgI1XBb",
            "data" => json_encode($data)
        ];
        try {
            $client->request('POST', 'http://83.69.139.182:8083/', [
                'form_params' => $query
            ]);
        } catch (RequestException $e) {
            Log::info($e->getMessage());
        }
    }
}
