<?php

namespace App\Http\Controllers\Api\V2;

use App\Address;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Address::class, 'address');
    }

    public function index(Request $request)
    {
        $addresses = $request->user()->addresses->sortByDesc('is_default');
        return AddressResource::collection($addresses);
    }

    public function show(Request $request, Address $address)
    {
        return new AddressResource($address);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address_line_1' => 'required|max:50000',
            'address_line_2' => 'nullable|max:50000',
            'latitude' => 'nullable|max:191',
            'longitude' => 'nullable|max:191',
            'location_accuracy' => 'nullable|max:191',
        ]);

        $request->user()->addresses()->create($data);
        return response()->json([
            'message' => __('main.address_saved'),
        ]);
    }

    public function update(Request $request, Address $address)
    {
        $data = $request->validate([
            'address_line_1' => 'required|max:50000',
            'address_line_2' => 'nullable|max:50000',
            'latitude' => 'nullable|max:191',
            'longitude' => 'nullable|max:191',
            'location_accuracy' => 'nullable|max:191',
        ]);

        $address->update($data);
        return response()->json([
            'message' => __('main.address_saved'),
        ]);
    }

    public function destroy(Request $request, Address $address)
    {
        $address->delete();
        return response()->json([
            'message' => __('main.address_deleted'),
        ]);
    }

    public function setDefault(Request $request, Address $address)
    {
        $this->authorize('update', $address);
        $user = $request->user();
        Address::where('user_id', $user->id)->update(['is_default' => 0]);
        $address->update(['is_default' => 1]);
        return response()->json([
            'message' => __('main.address_saved'),
        ]);
    }
}
