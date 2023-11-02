
<!-- START SECTION SUBSCRIBE NEWSLETTER -->
<div class="section bg_default small_pt small_pb">
    <div class="custom-container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="newsletter_text text_white">
                    <h3>{{ __('main.subscribe_to_site_news') }}</h3>
                    <p></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="newsletter_form2 rounded_input">
                    <form action="{{ route('subscriber.subscribe') }}" class="subscriber-form" method="post">

                        @csrf

                        <input type="email" name="email" class="form-control bg-white" placeholder="{{ __('main.form.your_email') }}" required>

                        <button type="submit" class="btn btn-dark btn-radius" name="submit"
                            value="Submit">{{ __('main.to_subscribe') }}</button>

                        <div class="form-result text-center"></div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- START SECTION SUBSCRIBE NEWSLETTER -->
