<?php

namespace App\Http\Controllers;

use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Image;
use App\Notification;
use App\Order;
use App\Page;
use App\Referal;
use App\Rules\CurrentPassword;
use App\Shop;
use App\User;
use App\UserApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show'), LinkItem::STATUS_INACTIVE));

        $cartQuantity = app('cart')->getTotalQuantity();
        $wishlistQuantity = app('wishlist')->getTotalQuantity();
        $compareQuantity = app('compare')->getTotalQuantity();
        $ordersQuantity = Order::where('user_id', $user->id)->count();

        $notifications = $user->notifications()->new()->count();

        $copyUrlOrigin = Referal::where('ref_user_id', $user->id)->first();
        if (!empty($copyUrlOrigin->name_ref)) {
            $copyUrl = "https://allgood.uz/ref/from/" . $copyUrlOrigin->name_ref;
        } else {
            $copyUrl = null;
        }
        $countRefs = User::where('ref_id', $user->id)->count();

        $process = 0;
        if (!empty($user->passport_main_image)) {
            $process = $process + 1;
        }
        if (!empty($user->passport_address_image)) {
            $process = $process + 1;
        }
        if (!empty($user->passport_additional_image)) {
            $process = $process + 1;
        }

        if (!empty($user->plastic_card_image)) {
            $process = $process + 1;
        }

        if (!empty(auth()->user()->id)) {
            if (!empty(Session::get('voucher'))) {
                User::where('id', auth()->user()->id)->update([
                    'is_coupon_used' => 'no',
                    'voucher' => 'true',
                ]);

                $user = User::find(auth()->user()->id);
                if (!empty($user->coupon_sum)) {
                    $user->increment('coupon_sum', Session::get('voucher'));
                }else{
                    $user->coupon_sum = Session::get('voucher');
                }
                $user->save();
                Session::forget('voucher');
            }
        }

        return view('profile.show', compact('breadcrumbs', 'process', 'user', 'cartQuantity', 'wishlistQuantity', 'compareQuantity', 'ordersQuantity', 'notifications', 'copyUrl', 'countRefs'));
    }

    public function edit()
    {
        $user = Auth::user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.edit'), route('profile.edit'), LinkItem::STATUS_INACTIVE));
        return view('profile.edit', compact('breadcrumbs', 'user'));
    }

    public function update(Request $request)
    {
        $data = $this->validate($request, [
            'name' => ['required', 'string', 'max:190'],
            // 'phone_number' => ['max:190'],
            'address' => ['max:5000'],
            'avatar' => ['image', 'max:1024'],
        ]);

        $authUser = auth()->user();
        if (!empty($data['avatar'])) {
            if ($authUser->avatar) {
                Storage::disk('public')->delete($authUser->avatar);
            }
            Helper::storeImage($authUser, 'avatar', 'users');
            unset($data['avatar']);
        }

        $authUser->update($data);

        // Session::flash('message', __('main.profile_saved'));
        // return redirect()->back();

        return redirect()->route('profile.show')->withSuccess(__('main.profile_saved'));
    }

    public function password(Request $request)
    {
        $data = $this->validate($request, [
            'current_password' => ['required', new CurrentPassword],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        Auth::user()->update([
            'password' => Hash::make($data['password']),
        ]);
        Session::flash('pmessage', __('main.password_saved'));
        return redirect()->back();
    }

    public function orders()
    {
        $user = Auth::user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.orders'), route('profile.orders'), LinkItem::STATUS_INACTIVE));
        $orders = $user->orders()->with('orderItems.product')->latest()->paginate(20);
        return view('profile.orders', compact('breadcrumbs', 'user', 'orders'));
    }

    public function requestSellerStatus()
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.become_a_seller'), route('profile.request-seller-status'), LinkItem::STATUS_INACTIVE));

        $user = Auth::user();
        $userApplication = false;
        $errorText = __('main.you_cannot_become_a_seller');
        if ($user->role->name == 'user' || $user->role->name == 'seller') {
            $userApplication = UserApplication::firstOrCreate([
                'type' => UserApplication::TYPE_BECOME_SELLER,
                'user_id' => $user->id,
            ], [
                'status' => UserApplication::STATUS_PENDING,
            ]);
            if ($userApplication->wasRecentlyCreated) {
                Session::flash('message', __('main.request_accepted'));
            }
        }

        Helper::toTelegram('Новая заявка - Стать продавцом');

        return view('profile.request_seller_status', compact('userApplication', 'errorText'));
    }

    public function shopEdit(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) {
            $shop = new Shop();
        }
        return view('profile.shop.edit', compact('shop'));
    }

    public function shopUpdate(Request $request)
    {
        $shop = auth()->user()->shops()->first();
        if (!$shop) {
            abort(404);
        }
        $data = $this->validatedShopData($request);
        $data['status'] = Shop::STATUS_PENDING;
        $shop->update($data);

        Helper::storeImage($shop, 'image', 'shops', Shop::$imgSizes);

        Session::flash('message', __('main.shop_updated') . '. ' . __('main.pending_moderator_review'));
        return redirect()->route('profile.shop.edit');
    }

    protected function validatedShopData(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'image' => ['sometimes', 'image', 'max:1000'],
            'description' => ['max:1000'],
            'phone_number' => ['required'],
            'email' => ['required', 'email', 'max:190'],
            'address' => ['max:1000'],
        ]);
        return $data;
    }

    public function products()
    {
        $user = Auth::user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.products'), route('profile.products'), LinkItem::STATUS_INACTIVE));
        $shop = $user->shops()->first();
        if (!$shop) {
            $shop = new Shop();
        }
        $products = $shop->products()->latest()->paginate(20);
        return view('profile.products', compact('breadcrumbs', 'user', 'products'));
    }

    public function notifications()
    {
        $user = Auth::user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.notifications'), route('profile.notifications.index'), LinkItem::STATUS_INACTIVE));
        $notifications = $user->notifications()->latest()->paginate(20);
        Notification::whereIn('id', $notifications->pluck('id')->toArray())->update(['status' => Notification::STATUS_READ]);
        return view('profile.notification.index', compact('breadcrumbs', 'user', 'notifications'));
    }

    public function documents()
    {
        $user = Auth::user();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.moiDokumenti'), route('profile.documents.index'), LinkItem::STATUS_INACTIVE));

        // dd($user->passport_main_string . " - " . $user->passport_address_string);
        $process = 0;
        if (!empty($user->passport_main_image)) {
            $process = $process + 1;
        }
        if (!empty($user->passport_address_image)) {
            $process = $process + 1;
        }
        if (!empty($user->passport_additional_image)) {
            $process = $process + 1;
        }
        if (!empty($user->plastic_card_image)) {
            $process = $process + 1;
        }
        $checkMainImage = Image::where('id', $user->passport_main_image)->first();
        $checkAddressImage = Image::where('id', $user->passport_address_image)->first();
        $checkAdditionalImage = Image::where('id', $user->passport_additional_image)->first();
        $checkPlasticCardImage = Image::where('id', $user->plastic_card_image)->first();

        return view('profile.documents.index', compact('breadcrumbs', 'user', 'checkMainImage', 'checkAddressImage', 'checkAdditionalImage', 'checkPlasticCardImage', 'process'));
    }

    public function documentsUpdatePassport(Request $request)
    {
        // dd($request->passport_additional_string);
        if (empty($request->passport_main_string) && empty($request->passport_address_string) && empty($request->passport_additional_string) && empty($request->plastic_card_string)) {
            return redirect()->back();
        }
        // dd($request->passport_additional_string);
        $user = $request->user();
        $data = $request->validate([
            'passport_main_string' => 'nullable|max:5120',
            'passport_address_string' => 'nullable|max:5120',
            'passport_additional_string' => 'nullable|max:5120',
            'plastic_card_string' => 'nullable|max:5120',
        ]);

        if (!empty($request->passport_main_string)) {
            $createData = [
                'path' => Storage::disk('public')->putFile('images/' . date('FY'), $data['passport_main_string']),
                'original_name' => $data['passport_main_string']->getClientOriginalName(),
                'mime_type' => $data['passport_main_string']->getClientMimeType(),
                'size' => $data['passport_main_string']->getSize(),
                'user_id' => $user->id,
            ];

            $image = Image::create($createData);
            User::where('id', $user->id)->update([
                'passport_main_image' => $image->id
            ]);
        }

        if (!empty($request->passport_address_string)) {
            $createData = [
                'path' => Storage::disk('public')->putFile('images/' . date('FY'), $data['passport_address_string']),
                'original_name' => $data['passport_address_string']->getClientOriginalName(),
                'mime_type' => $data['passport_address_string']->getClientMimeType(),
                'size' => $data['passport_address_string']->getSize(),
                'user_id' => $user->id,
            ];

            $image = Image::create($createData);
            User::where('id', $user->id)->update([
                'passport_address_image' => $image->id
            ]);
        }

        if (!empty($request->passport_additional_string)) {
            $createData = [
                'path' => Storage::disk('public')->putFile('images/' . date('FY'), $data['passport_additional_string']),
                'original_name' => $data['passport_additional_string']->getClientOriginalName(),
                'mime_type' => $data['passport_additional_string']->getClientMimeType(),
                'size' => $data['passport_additional_string']->getSize(),
                'user_id' => $user->id,
            ];

            $image = Image::create($createData);
            User::where('id', $user->id)->update([
                'passport_additional_image' => $image->id
            ]);
        }

        if (!empty($request->plastic_card_string)) {
            $createData = [
                'path' => Storage::disk('public')->putFile('images/' . date('FY'), $data['plastic_card_string']),
                'original_name' => $data['plastic_card_string']->getClientOriginalName(),
                'mime_type' => $data['plastic_card_string']->getClientMimeType(),
                'size' => $data['plastic_card_string']->getSize(),
                'user_id' => $user->id,
            ];

            $image = Image::create($createData);
            User::where('id', $user->id)->update([
                'plastic_card_image' => $image->id
            ]);
        }
        /*
        if (!empty($request->passport_address_string)) {
            Helper::storeImage(auth()->user(), 'passport_address_string', 'passports');
        }
        */

        return redirect()->back();
    }

    public function documentsUpdateCard(Request $request)
    {
        $request->validate([
            'card_number' => 'required',
            'card_validation_date' => 'required',
        ]);

        if ($request->card_number == "____ ____ ____ ____") {
            return redirect()->back();
        }

        $card_number = preg_replace("/[^0-9]/", "", $request->card_number);
        $card_expiry = preg_replace("/[^0-9]/", "", $request->card_validation_date);

        User::where("id", auth()->user()->id)->update([
            'card_number' => $card_number,
            'card_expiry' => $card_expiry
        ]);

        return redirect()->back();
    }

    public function notificationsShow(Notification $notification)
    {
        $user = Auth::user();
        if ($notification->user_id != $user->id) {
            abort(403);
        }
        $notification->status = Notification::STATUS_READ;
        $notification->save();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.notifications'), route('profile.notifications.index')));
        return view('profile.notification.show', compact('breadcrumbs', 'user', 'notification'));
    }

    public function testfrommerchant()
    {
        return response()->json([
            'success' => "Successfull message"
        ]);
    }
}
