{{ __('mail.password_reset.intro', ['name' => $name]) }}

{!! $url !!}

{{ __('mail.password_reset.expires', ['minutes' => $minutes]) }}

{{ __('mail.password_reset.ignore') }}
