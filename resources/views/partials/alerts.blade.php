@if (session()->has('alert') && session()->has('alertType'))
    <div class="my-4 container">
        <div class="alert alert-{{ session()->get('alertType') }}">
            <h4 class="m-0">{{ session()->get('alert') }}</h4>
        </div>
    </div>
@endif

@if (session()->has('message'))
    <div class="my-4 container">
        <div class="alert alert-success">
            <h4 class="m-0">{{ session()->get('message') }}</h4>
        </div>
    </div>
@endif

@if (session()->has('success'))
    <div class="my-4 container">
        <div class="alert alert-success">
            <h4 class="m-0">{{ session()->get('success') }}</h4>
        </div>
    </div>
@endif

@if (session()->has('status'))
    <div class="my-4 container">
        <div class="alert alert-success">
            <h4 class="m-0">{{ session()->get('status') }}</h4>
        </div>
    </div>
@endif

@if (session()->has('error'))
    <div class="my-4 container">
        <div class="alert alert-danger">
            <h4 class="m-0">{{ session()->get('error') }}</h4>
        </div>
    </div>
@endif
