@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card my-5">
                <div class="card-header">AllGood Seller</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <p> <b> @lang('main.detail_personal') </b> </p>

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

                    <form class="d-inline" method="POST" action="{{ route('seller.register_form_store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                    <span>@lang('main.detail_fname')</span>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-4">
                                <span>@lang('main.detail_lname')</span>
                                <input type="text" name="lastname" class="form-control" value="{{ old('lastname') }}" required>
                            </div>
                            <div class="col-md-4">
                                <span>@lang('main.detail_phone')</span>
                                <input type="tel" name="phone_number" id="storage_phone"
                                class="phone-input-mask form-control  @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', optional(auth()->user())->phone_number) ?? '' }}"
                                required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                            </div>
                            {{-- 
                            <div class="col-md-4">
                                <span>Sharif</span>
                                <input type="text" name="fathername" class="form-control" value="{{ old('fathername') }}" required>
                            </div> --}}
                        </div>
                        
                        {{-- 
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <span>  Telefon raqam</span>
                                <input type="tel" name="phone_number" id="storage_phone"
                                class="phone-input-mask form-control  @error('phone_number') is-invalid @enderror"
                                value="{{ old('phone_number', optional(auth()->user())->phone_number) ?? '' }}"
                                required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                            </div>
                            {{-- 
                            <div class="col-md-4">
                                <span>Tug'ilgan sana</span>
                                <input type="date" name="birthday" class="form-control" value="{{ old('birthday') }}" required>
                            </div>
                        </div>
                        --}}

                        <hr>

                        <p> <b> @lang('main.detail_company') </b> </p>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <span>@lang('main.detail_company_name')</span>
                                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required>
                            </div>
                        </div>
                        {{-- 
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <span>INN</span>
                                <input type="text" name="company_inn" class="form-control" value="{{ old('company_inn') }}" required>
                            </div>
                            <div class="col-md-6">
                                <span>OKED</span>
                                <input type="text" name="company_oked" class="form-control" value="{{ old('company_oked') }}" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="custom-file">
                                    <label class="custom-file-label" for="customFile">Kompaniya guvohnomasi</label>
                                    <input type="file" name="company_identification" class="custom-file-input" id="customFile" required>
                                </div>
                            </div>
                        </div> 
                        

                        <hr>

                        <p> <b> Hisob raqam </b> </p>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <span>Kompaniya rasmiy nomi</span>
                                <input type="text" name="company_official_name" value="{{ old('company_official_name') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-2">
                                <span>Bank kodi (MFO)</span>
                                <input type="text" name="bank_code_mfo" value="{{ old('bank_code_mfo') }}" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <span>Hisob raqam</span>
                                <input type="text" name="company_checking_account" value="{{ old('company_checking_account') }}" class="form-control" required>
                            </div>
                            <div class="col-md-5">
                                <span>Hisob raqam nomi</span>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}" required>
                            </div>
                        </div>
                        
                        --}}
                        <hr>

                        <p class="text-center"> <b> @lang('main.detail_safety') </b> </p>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <span>@lang('main.detail_password')</span>
                                <input type="text" name="password" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <span>@lang('main.detail_password_again')</span>
                                <input type="text" name="password_again" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mx-auto text-center">
                                <button type="submit" class="btn btn-success p-1 mt-3 align-baseline mx-auto text-center">@lang('main.detail_register')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
