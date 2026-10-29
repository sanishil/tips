{{-- Google reCAPTCHA v2 checkbox. app.js stops the form if it isn't ticked; App\Rules\Recaptcha checks it on the server. --}}
<div data-recaptcha {{ $attributes }}>
    <div class="recaptcha-row">
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" data-callback="recaptchaChecked"></div>
        <button type="button" class="btn btn-icon fs-5" data-recaptcha-reset aria-label="Load a new “I'm not a robot” check" title="New check">
            <i class="fa-solid fa-rotate" aria-hidden="true"></i>
        </button>
    </div>
    <x-field-error for="g-recaptcha-response">Please tick the box first.</x-field-error>
</div>

<link rel="preconnect" href="https://www.google.com">
<link rel="preconnect" href="https://www.gstatic.com" crossorigin>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
