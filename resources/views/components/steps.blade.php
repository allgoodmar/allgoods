<!-- START STEPS -->
<div class="">
    <div class="custom-container">
        <div class="shopping_info pt-5 pb-4">
            <h4>{{ __('main.installment_payment_steps') }}</h4>
            <div class="row justify-content-center">
                @foreach ($steps as $key => $step)
                    <div class="col-md-3">
                        <div class="icon_box icon_box_style2">
                            <div class="icon">
                                <img src="{{ $step->img }}" class="img-fluid" alt="{{ $step->name }}">
                            </div>
                            <div class="icon_box_content">
                                <h5>{{ $step->name }}</h5>
                                <p>{{ $step->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- END STEPS -->
