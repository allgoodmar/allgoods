@csrf

<div class="form-group">
    <label for="form_address_line_1">{{ __('main.address') }} <span
            class="text-danger">*</span></label>
    <input id="form_address_line_1" type="text"
        class="form-control @error('address_line_1') is-invalid @enderror"
        name="address_line_1"
        value="{{ old('address_line_1') ?? $address->address_line_1 ?? '' }}" required
        autofocus>
    @error('address_line_1')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>


<div class="form-group">
    <button type="submit" class="btn btn-primary">
        {{ __('main.form.to_save') }}
    </button>
</div>
