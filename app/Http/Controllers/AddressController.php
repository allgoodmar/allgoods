<?php

namespace App\Http\Controllers;

use App\Address;
use App\Helpers\Breadcrumbs;
use App\Helpers\LinkItem;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));

        $addresses = $user->addresses()->latest()->get();

        return view('addresses.index', compact('breadcrumbs', 'user', 'addresses'));
    }

    public function create()
    {
        $user = auth()->user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.addresses'), route('addresses.index')));

        $address = new Address();

        return view('addresses.create', compact('breadcrumbs', 'user', 'address'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $this->validatedData($request);
        $user->addresses()->create($data);
        return redirect()->route('addresses.index')->withSuccess(__('main.address_saved'));
    }


    public function edit(Address $address)
    {
        $user = auth()->user();
        if ($user->id != $address->user_id) {
            abort(403);
        }
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.addresses'), route('addresses.index')));

        return view('addresses.edit', compact('breadcrumbs', 'user', 'address'));
    }

    public function update(Request $request, Address $address)
    {
        $user = auth()->user();
        if ($user->id != $address->user_id) {
            abort(403);
        }
        $data = $this->validatedData($request);
        $address->update($data);
        return redirect()->route('addresses.index')->withSuccess(__('main.address_saved'));
    }

    protected function validatedData(Request $request)
    {
        $data = $request->validate([
            'address_line_1' => 'required|max:5000',
        ]);
        return $data;
    }

    public function destroy(Request $request, Address $address)
    {
        $user = auth()->user();
        if ($user->id != $address->user_id) {
            abort(403);
        }
        $address->delete();
        return redirect()->route('addresses.index')->withSuccess(__('main.address_deleted'));
    }

    public function statusUpdate(Address $address, $status) {
        $user = auth()->user();
        if ($user->id != $address->user_id) {
            abort(403);
        }
        if (!in_array($status, [Address::STATUS_ACTIVE, Address::STATUS_INACTIVE])) {
            $status = Address::STATUS_ACTIVE;
        }
        if ($status == Address::STATUS_ACTIVE) {
            $user->addresses()->update(['status' => Address::STATUS_INACTIVE]);
        }
        $address->update(['status' => $status]);
        return $address;
    }
}
