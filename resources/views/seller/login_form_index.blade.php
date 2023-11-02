@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card my-5">
                <div class="card-header">AllGood Seller</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <p>Some text/guideline will be written here!</p>

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

                    <form class="d-inline" method="POST" action="{{ route('seller.login_form_store') }}">
                        @csrf
                        <span>Номер телефона</span>
                        <input type="tel" name="phone_number" id="storage_phone"
                            class="phone-input-mask form-control  @error('phone_number') is-invalid @enderror"
                            value="{{ old('phone_number', optional(auth()->user())->phone_number) ?? '' }}"
                            required pattern="^\+998\d{2}\s\d{3}-\d{2}-\d{2}$">
                        @error('phone_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <br>
                        
                        <span>Пароль</span>
                        <input type="password" name="password" class="form-control" value="{{ old('fathername') }}" required>

                        <button type="submit" class="btn btn-success p-1 mt-3 align-baseline">Вход</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
