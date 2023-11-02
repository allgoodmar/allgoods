<?php

namespace App\Http\Controllers\Api\V2;

use App\Card;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Services\AtmosService;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Card::class, 'card');
    }

    public function index(Request $request)
    {
        $cards = $request->user()->cards()->where('status', 1)->orderBy('is_default', 'desc')->get();
        return CardResource::collection($cards);
    }

    public function show(Request $request, Card $card)
    {
        return new CardResource($card);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'card_number' => 'required|regex:/^\d{16}$/',
            'expiry' => 'required|regex:/^\d{4}$/',
        ]);

        $atmos = new AtmosService();
        $response = $atmos->bindCardCreate($data);
        if (!$response) {
            return response()->json([
                'message' => __('main.unknown_error'),
            ], 400);
        }
        $json = json_decode($response->getBody()->getContents());
        if (empty($json->result->code)) {
            return response()->json([
                'message' => __('main.unknown_error'),
            ], 400);
        }
        if ($json->result->code != 'OK') {
            return response()->json([
                'message' => $json->result->description,
            ], 400);
        }

        $request->user()->cards()->create([
            'atmos_transaction_id' => $json->transaction_id,
        ]);

        return response()->json([
            'transaction_id' => $json->transaction_id,
        ]);
    }

    public function confirm(Request $request)
    {
        $data = $request->validate([
            'transaction_id' => 'required|exists:cards,atmos_transaction_id',
            'otp' => 'required',
        ]);

        $atmos = new AtmosService();
        $response = $atmos->bindCardApply($data);
        if (!$response) {
            return response()->json([
                'message' => __('main.unknown_error'),
            ], 400);
        }
        $json = json_decode($response->getBody()->getContents());
        if (empty($json->result->code)) {
            return response()->json([
                'message' => __('main.unknown_error'),
            ], 400);
        }
        if ($json->result->code != 'OK') {
            return response()->json([
                'message' => $json->result->description,
            ], 400);
        }

        $card = $request->user()->cards()->where('atmos_transaction_id', $data['transaction_id'])->firstOrFail();
        $card->update([
            'atmos_card_id' => $json->data->card_id ?? '',
            'atmos_card_token' => $json->data->card_token ?? '',
            'pan' => $json->data->pan ?? '',
            'expiry' => $json->data->expiry ?? '',
            'card_holder' => $json->data->card_holder ?? '',
            'balance' => $json->data->balance ?? 0,
            'phone' => $json->data->phone ?? '',
            'status' => 1,
        ]);

        return response()->json([
            'message' => __('main.card_saved'),
            'data' => new CardResource($card),
        ]);
    }

    public function update(Request $request, Card $card)
    {
        //
    }

    public function destroy(Request $request, Card $card)
    {
        $atmos = new AtmosService();
        $atmos->removeCard([
            'id' => $card->atmos_card_id,
            'token' => $card->atmos_card_token,
        ]);
        $card->delete();
        return response()->json([
            'message' => __('main.card_deleted'),
        ]);
    }

    public function setDefault(Request $request, Card $card)
    {
        $this->authorize('update', $card);
        $user = $request->user();
        Card::where('user_id', $user->id)->update(['is_default' => 0]);
        $card->update(['is_default' => 1]);
        return response()->json([
            'message' => __('main.card_saved'),
        ]);
    }
}
