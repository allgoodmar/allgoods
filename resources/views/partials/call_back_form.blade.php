<!-- Modal -->
<div class="modal fade" id="callBackFormModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">{{ __('main.feedback_form') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" style="background-color: #EFF3F6!important;">
            <form class="form-feedback contact-form" action="{{ route('contacts.send') }}" method="post">
                @csrf
                <div class="form-hide-blocks">
                    <div class="form-group__wrap">
                        <div class="form-group">
                            <label for="a_name" class="sr-only form-label">{{ __('main.form.your_name') }}</label>
                            <input type="text" name="name" id="a_name" class="form-control radius-6" placeholder="{{ __('main.form.your_name') }}" required >
                        </div>
                        <div class="form-group">
                            <label for="a_phone" class="sr-only form-label">{{ __('main.form.your_phone_number') }}</label>
                            <input type="text" name="phone" id="a_phone" class="form-control radius-6"
                                placeholder="{{ __('main.form.your_phone_number') }}" required >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="a_message" class="sr-only form-label">{{ __('main.message') }}</label>
                        <textarea id="a_message" name="message" rows="3" class="form-control radius-6"
                            placeholder="{{ __('main.message') }}"></textarea>
                    </div>
                    <div class="row gutters-5 mb-4">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <div class="form-group">
                                <input type="text" name="captcha" class="form-control"
                                placeholder="{{ __('main.form.security_code') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="captcha-container">
                                    <img src="{{ asset('images/captcha.png') }}" alt="Captcha" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-flex">
                        <button type="button" class="btn btn-secondary radius-6 mr-auto" data-dismiss="modal">{{ __('main.to_close') }}</button>
                        <button type="submit" class="theme-btn radius-6 ml-auto">{{ __('main.to_send') }}</button>
                    </div>
                </div>
                <div class="form-result"></div>
            </form>
        </div>
      </div>
    </div>
</div>