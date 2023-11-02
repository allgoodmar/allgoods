<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Order;
use App\Warehouse;
use App\ZoodpayRefund;
use App\ZoodpayTransaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class Zoodpay
{
    public $client;

    private $env;
    private $base_url;
    private $merchant_key;
    private $merchant_secret;
    private $salt;

    public function __construct()
    {
        $this->env = config('services.zoodpay.env');
        $this->base_url = ($this->env == 'production') ? config('services.zoodpay.production_url') : config('services.zoodpay.sandbox_url');
        $this->merchant_key = config('services.zoodpay.merchant_key');
        $this->merchant_secret = config('services.zoodpay.merchant_secret');
        $this->salt = config('services.zoodpay.salt');

        $this->client = new Client([
            'base_uri' => $this->base_url,
            'timeout'  => 10.0,
        ]);
    }

    public function generateMerchantSignature(Order $order, $decimals = false)
    {
        // string = merchant_key|merchant_reference_no|amount|currency|market_code|salt
        // signature = sha512(string)
        $total = ($decimals) ? number_format($order->total, 2, '.', '') : number_format($order->total, 0, '.', '');
        return hash('sha512', $this->merchant_key . '|' . 'order' . $order->id . '|' . $total . '|UZS|UZ|' . $this->salt);
    }

    public function generateZoodpaySignature(Order $order, $zoodpayTransactionId, $decimals = false)
    {
        // string = market_code|currency|amount|merchant_reference_no|merchant_key|transaction_id|salt
        // signature = sha512(string)
        $total = ($decimals) ? number_format($order->total, 2, '.', '') : number_format($order->total, 0, '.', '');
        return hash('sha512', 'UZ|UZS|' . $total . '|' . 'order' . $order->id . '|' . $this->merchant_key . '|' . $zoodpayTransactionId . '|' . $this->salt);
    }

    public function transactionGet($zoodpayTransactionId)
    {

        try {
            $response = $this->client->get('transactions/' . $zoodpayTransactionId, [
                'auth' => $this->authData(),
            ]);
            $body = $response->getBody()->getContents();
            $successData = json_decode($body);
            return [
                'error' => 0,
                'data' => $successData,
            ];
        } catch(ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            Log::error($errorBody);
            return [
                'error' => 1,
                'data' => [
                    'message' => $errorBody,
                ],
            ];
            // dd($errorBody);
        } catch(Throwable $e) {
            return [
                'error' => 2,
                'data' => [
                    'message' => $e->getMessage(),
                ],
            ];
            Log::error($e->getMessage());
        }
    }

    public function refundGet($zoodpayRefundId)
    {

        try {
            $response = $this->client->get('refunds/' . $zoodpayRefundId, [
                'auth' => $this->authData(),
            ]);
            $body = $response->getBody()->getContents();
            $successData = json_decode($body);
            return [
                'error' => 0,
                'data' => $successData,
            ];
        } catch(ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            Log::error($errorBody);
            return [
                'error' => 1,
                'data' => [
                    'message' => $errorBody,
                ],
            ];
            // dd($errorBody);
        } catch(Throwable $e) {
            return [
                'error' => 2,
                'data' => [
                    'message' => $e->getMessage(),
                ],
            ];
            Log::error($e->getMessage());
        }
    }

    public function setDeliveryDate($zoodpayTransactionId, $date)
    {
        $data = [
            'delivered_at' => $date->format('Y-m-d H:i:s'),
            'final_capture_amount' => 0.00
        ];

        try {
            $response = $this->client->put('transactions/' . $zoodpayTransactionId . '/delivery', [
                'auth' => $this->authData(),
                'json' => $data,
            ]);
            $body = $response->getBody()->getContents();
            $successData = json_decode($body);

            return [
                'error' => 0,
                'data' => $successData,
            ];
        } catch(ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            Log::error($errorBody);
            return [
                'error' => 1,
                'data' => [
                    'message' => $errorBody,
                ],
            ];
            // dd($errorBody);
        } catch(Throwable $e) {
            return [
                'error' => 2,
                'data' => [
                    'message' => $e->getMessage(),
                ],
            ];
            Log::error($e->getMessage());
        }
    }

    public function transactionCreate(Order $order)
    {
        // Log::info('transaction create start');
        $locale = app()->getLocale();
        $order->load('orderItems.product.categories');
        $orderItems = $order->orderItems;
        $signature = $this->generateMerchantSignature($order);
        $data = [
            'customer' => [
                'customer_email' => Str::limit($order->email, 128, ''),
                'customer_phone' => Str::limit(Helper::phone($order->phone_number), 32, ''),
                'first_name' => Str::limit($order->name, 50, ''),
            ],
            'items' => [],
            'order' => [
                'amount' => (float)$order->total,
                'currency' => 'UZS',
                'lang' => $locale,
                'market_code' => 'UZ',
                'merchant_reference_no' => 'order' . $order->id,
                'service_code' => 'ZPI',
                'signature' => $signature,
            ],
            'shipping' => [
                'address_line1' => Str::limit($order->address_line_1, 128, ''),
                'country_code' => 'UZ',
                'name' => $order->name,
                'zipcode' => '100000',
            ],
        ];

        foreach ($orderItems as $item) {
            $data['items'][] = [
                'categories' => [
                    [
                        $item->product->categories->first()->name,
                    ],
                ],
                'currency_code' => 'UZS',
                'name' => Str::limit($item->name, 100, ''),
                'price' => (float)$item->price,
                'quantity' => (int)$item->quantity,
            ];
        }

        Log::info($data);

        try {
            $response = $this->client->post('transactions', [
                'auth' => $this->authData(),
                'json' => $data,
            ]);
            $body = $response->getBody()->getContents();
            $successData = json_decode($body);

            return [
                'error' => 0,
                'data' => $successData,
            ];
        } catch(ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            Log::error($errorBody);
            return [
                'error' => 1,
                'data' => [
                    'message' => $errorBody,
                ],
            ];
            // dd($errorBody);
        } catch(Throwable $e) {
            Log::error($e->getMessage());
            return [
                'error' => 2,
                'data' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }
    }

    public function refundCreate(ZoodpayTransaction $zoodpayTransaction, $amount, $reason)
    {
        // Log::info('transaction create start');
        $locale = app()->getLocale();

        $requestID = $zoodpayTransaction->id . 'refund' . Str::random(32);

        $zoodpayRefund = ZoodpayRefund::create([
            'zoodpay_transaction_id' => $zoodpayTransaction->id,
            'zoodpay_refund_amount' => $amount,
            'zoodpay_reason' => $reason,
            'zoodpay_request_id' => $requestID,
        ]);

        $data = [
            'merchant_refund_reference' => 'refundorder' . $zoodpayTransaction->order->id,
            'reason' => $reason,
            'refund_amount' => $amount,
            'request_id' => $requestID,
            'transaction_id' => $zoodpayTransaction->zoodpay_transaction_id,
        ];

        // Log::info(print_r($data, 1));

        try {
            $response = $this->client->post('refunds', [
                'auth' => $this->authData(),
                'json' => $data,
            ]);
            $body = $response->getBody()->getContents();
            $successData = json_decode($body);

            return [
                'error' => 0,
                'data' => $successData,
            ];
        } catch(ClientException $e) {
            $errorBody = $e->getResponse()->getBody()->getContents();
            Log::error($errorBody);
            return [
                'error' => 1,
                'data' => [
                    'message' => $errorBody,
                ],
            ];
            // dd($errorBody);
        } catch(Throwable $e) {
            Log::error($e->getMessage());
            return [
                'error' => 2,
                'data' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }
    }

    private function authData()
    {
        return [$this->merchant_key, $this->merchant_secret];
    }
}
