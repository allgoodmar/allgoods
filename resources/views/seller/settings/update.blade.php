@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <a href="{{ route('seller.account') }}" class="btn btn-primary">Назад</a>
            <div class="card mt-1 mb-5">
                <div class="card-header">AllGood Seller - ma'lumotlarni tahrirlash</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <p> <b> Shaxsiy ma'lumotlar </b> </p>

                    @if(session()->has('success') || session()->has('danger'))
                        <div class="alert
                        @if (session()->has('success'))
                        alert-success
                        @endif

                        @if (session()->has('danger'))
                        alert-danger
                        @endif
                        ">
                            {{ session()->get('danger') }}
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    <form class="d-inline" method="POST" action="{{ route('seller.update_form_store', ['id'=>$company->id]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                    <span>Ism</span>
                                    <input type="text" name="name" class="form-control" value="{{ $company_owner->name }}" required>
                            </div>
                            <div class="col-md-4">
                                <span>Familiya</span>
                                <input type="text" name="lastname" class="form-control" value="{{ $company_owner->last_name }}" required>
                            </div>
                            <div class="col-md-4">
                                <span>Sharif</span>
                                <input type="text" name="fathername" class="form-control" value="{{ $company_owner->father_name }}" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <span>  Telefon raqam</span>
                                <input type="tel" name="phone_number" id="storage_phone"
                                class="phone-input-mask form-control  @error('phone_number') is-invalid @enderror"
                                value="{{ $company->phone_number }}"
                                required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                            </div>

                            <div class="col-md-4">
                                <span>Tug'ilgan sana</span>
                                <input type="date" name="birthday" class="form-control" value="{{ $company_owner->birthday }}" required>
                            </div>
                        </div>

                        <hr>

                        <p> <b> Kompaniya haqida ma'lumotlar </b> </p>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <span>Kompaniya rasmiy nomi</span>
                                <input type="text" name="company_name" class="form-control" value="{{ $company->company_name }}" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <span>INN</span>
                                <input type="text" name="company_inn" class="form-control" value="{{ $company->company_inn }}" required>
                            </div>
                            <div class="col-md-6">
                                <span>OKED</span>
                                <input type="text" name="company_oked" class="form-control" value="{{ $company->company_oked }}" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="custom-file">
                                    <label class="custom-file-label" for="customFile">Kompaniya guvohnomasi</label>
                                    <input type="file" name="company_identification" class="custom-file-input" id="customFile">
                                </div>

                                @if (!empty($company->company_identification_file))
                                    <span class="text-success">Yuklangan</span>
                                @else
                                    <span class="text-danger">Yuklanmagan</span>
                                @endif

                            </div>
                        </div>

                        <hr>

                        <p> <b> Hisob raqam </b> </p>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <span>Kompaniya rasmiy nomi</span>
                                <input type="text" name="company_official_name" value="{{ $company->company_official_name }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-2">
                                <span>Bank kodi (MFO)</span>
                                <input type="text" name="bank_code_mfo" value="{{ $company->bank_code_mfo }}" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <span>Hisob raqam</span>
                                <input type="text" name="company_checking_account" value="{{ $company->company_checking_account }}" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <span>Hisob raqam nomi</span>
                                <input type="text" name="bank_name" class="form-control" value="{{ $company->bank_name }}" required>
                            </div>
                        </div>

                        <hr>

                        <p class="text-center"> <b> Xavfsizlik </b> </p>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <span>Parol</span>
                                <input type="text" name="password" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <span>Parolni qayta kiriting</span>
                                <input type="text" name="password_again" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mx-auto text-center">
                                <button type="submit" class="btn btn-success p-1 mt-3 align-baseline mx-auto text-center">Tahrirlash</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
