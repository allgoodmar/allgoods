<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\SellerCompany;
use App\SellerCompanyOwner;
use App\Sellerphone;
use App\User;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

use function Ramsey\Uuid\v1;

class SellerAuthController extends Controller
{
    public function index()
    {
        return view('seller.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required'
        ]);

        $check = Sellerphone::where('phone_number', $request->phone_number)->first();
        if (empty($check)) {
            Sellerphone::create([
                'phone_number' => $request->phone_number
            ]);

            return redirect()->back()->with('success', "So'rov yuborildi!");
        }else{
            return redirect()->back()->with('danger', "Bu telefon raqam oldin ro'yxatdan o'tgan!");
        }
    }

    public function register_form_index()
    {
        return view('seller.register_form_index');
    }

    public function register_form_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            // 'fathername' => 'required',
            // 'birthday' => 'required',
            'phone_number' => 'required',
            'company_name' => 'required',
            // 'company_inn' => 'required',
            // 'company_oked' => 'required',
            // 'company_identification' => 'required',
            // 'company_official_name' => 'required',
            // 'bank_code_mfo' => 'required',
            // 'company_checking_account' => 'required',
            // 'bank_name' => 'required',
            'password' => 'required',
            'password_again' => 'required'
        ]);

        $check = Sellerphone::where('phone_number', $request->phone_number)->first();
        $check_owner = SellerCompanyOwner::where('phone_number', $request->phone_number)->first();

        $newPhone = preg_replace("/[^0-9]/", "", $request->phone_number);

        if (empty($check) && empty($check_owner)) {
            if ($request->password === $request->password_again) {

                /*
                $seller_owner = SellerCompanyOwner::create([
                    'name' => $request->name,
                    'last_name' => $request->lastname,
                    'father_name' => $request->fathername,
                    'birthday' => $request->birthday,
                    'phone_number' => $request->phone_number
                ]);
                */

                $seller_owner = User::create([
                    'role_id' => "3",
                    'name' => $request->name,
                    'last_name' => $request->lastname,
                    // 'father_name' => $request->fathername,
                    'email' => "sellerallgood@gmail.com",
                    'birthday' => "2021-12-12",
                    'password' => Hash::make($request->password),
                    'phone_number' => $newPhone
                ]);

                if (!empty($request->company_identification)) {
                    $image = Helper::storeImage($request->company_identification, 'avatar', 'sellers');
                    $company_identification = $image;
                }else{
                    $company_identification = '';
                }

                $seller_company = SellerCompany::create([
                    'owner_id' => $seller_owner->id,
                    'company_name' => $request->company_name,
                    // 'company_inn' => $request->company_inn,
                    // 'company_oked' => $request->company_oked,
                    // 'company_identification_file' => $company_identification,
                    // 'company_official_name' => $request->company_official_name,
                    // 'company_checking_account' => $request->company_checking_account,
                    // 'bank_code_mfo' => $request->bank_code_mfo,
                    // 'bank_name' => $request->bank_name,
                    'password' => Hash::make($request->password),
                    'phone_number' => $newPhone
                ]);

                /*
                SellerCompanyOwner::where('id', $seller_owner->id)->update([
                    'company_id' => $seller_company->id
                ]);*/

                $updatedPerson = User::where('phone_number', $newPhone)->first();
                if (!empty($updatedPerson)) {
                    User::where('phone_number', $newPhone)->update([
                        'role_id' => "3",
                        'company_id' => $seller_company->id
                    ]);
                }

                User::where('id', $seller_owner->id)->update([
                    'role_id' => "3",
                    'company_id' => $seller_company->id
                ]);

                Sellerphone::create([
                    'phone_number' => $newPhone
                ]);

                // return redirect()->back()->with('success', "Muvaffaqiyatli qabul qilindi!");
                return redirect()->to('http://merchant.allgood.uz');
            }else{
                return redirect()->back()->with('danger', "Parollar bir-biriga mos emas!");
            }
        }else{
            return redirect()->back()->with('danger', "Bu telefon raqam oldin ro'yxatdan o'tgan!");
        }
    }

    public function login_form_index()
    {
        if (empty(Session::get('owner_id_login'))) {
            return view('seller.login_form_index');
        }else{
            return redirect()->route('seller.account');
        }
    }

    public function login_form_store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required'
        ]);

        $check = SellerCompany::where('phone_number', $request->phone_number)->first();
        if (isset($check)) {
            $seller_owner = SellerCompanyOwner::where('id', $check->owner_id)->first();
        }


        if (!empty($check) && $check->phone_number === $request->phone_number) {
            if (Hash::check($request->password, $check->password)) {
                return redirect()->route('seller.account', ['owner' => $check->owner_id]);
            }else{
                redirect()->back()->with('danger', "Parol xato!");
            }
        }else{
            return redirect()->back()->with('danger', "Telefon raqam noto'g'ri kiritilgan!");
        }
    }

    public function logout(Request $request)
    {
        Session::forget('seller_owner_name');
        Session::forget('seller_owner_lastname');
        Session::forget('seller_owner_fathername');
        Session::forget('seller_owner_phone_number');
        Session::forget('seller_owner_name');

        Session::forget('seller_company_id');
        Session::forget('seller_company_name');
        Session::forget('seller_company_inn');
        Session::forget('seller_company_oked');
        Session::forget('seller_company_official_name');
        Session::forget('seller_company_checking_account');
        Session::forget('seller_company_bank_code_mfo');
        Session::forget('seller_company_bank_name');
        Session::forget('seller_company_phone_number');

        Session::forget('owner_id_login');

        return redirect()->route('home');
    }

    public function update_form_index($id)
    {
        $company = SellerCompany::where('id', $id)->first();

        $company_owner = SellerCompanyOwner::where('company_id', $id)->first();

        return view('seller.settings.update', compact('company', 'company_owner'));
    }

    public function update_form_store(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'fathername' => 'required',
            'birthday' => 'required',
            'phone_number' => 'required',
            'company_name' => 'required',
            'company_inn' => 'required',
            'company_oked' => 'required',
            'company_official_name' => 'required',
            'bank_code_mfo' => 'required',
            'company_checking_account' => 'required',
            'bank_name' => 'required',
        ]);

        $check_owner = SellerCompanyOwner::where('company_id', $id)->first();

        $seller_owner = SellerCompanyOwner::where('company_id', $id)->update([
            'name' => $request->name,
            'last_name' => $request->lastname,
            'father_name' => $request->fathername,
            'birthday' => $request->birthday,
            'phone_number' => $request->phone_number
        ]);

        if (!empty($request->company_identification)) {
            $image = Helper::storeImage($request->company_identification, 'avatar', 'sellers');
            $company_identification = $image;
        }else{
            $company_identification = '';
        }

        if (!empty($request->company_identification)) {
            $image = Helper::storeImage($request->company_identification, 'avatar', 'sellers');
            $company_identification = $image;

            $seller_company = SellerCompany::where('id', $id)->update([
                'company_name' => $request->company_name,
                'company_inn' => $request->company_inn,
                'company_oked' => $request->company_oked,
                'company_identification_file' => $company_identification,
                'company_official_name' => $request->company_official_name,
                'company_checking_account' => $request->company_checking_account,
                'bank_code_mfo' => $request->bank_code_mfo,
                'bank_name' => $request->bank_name,
                'phone_number' => $request->phone_number
            ]);
        }else{
            $seller_company = SellerCompany::where('id', $id)->update([
                'company_name' => $request->company_name,
                'company_inn' => $request->company_inn,
                'company_oked' => $request->company_oked,
                'company_official_name' => $request->company_official_name,
                'company_checking_account' => $request->company_checking_account,
                'bank_code_mfo' => $request->bank_code_mfo,
                'bank_name' => $request->bank_name,
                'phone_number' => $request->phone_number
            ]);
        }

        if (!empty($request->password) && !empty($request->password_again)) {
            if ($request->password === $request->password_again) {
                SellerCompany::where('id', $id)->update([
                    'password' => Hash::make($request->password)
                ]);
            }
        }

        return redirect()->back()->with('success', "Muvaffaqiyatli tahrirlandi!");
    }
}
