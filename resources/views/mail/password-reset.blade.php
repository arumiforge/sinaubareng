<x-mail-layout :title="__('mail.password_reset.subject')">
    <p style="margin:0 0 16px;">{{ __('mail.password_reset.intro', ['name' => $name]) }}</p>
    @include('mail.button', ['url' => $url, 'label' => __('mail.password_reset.action')])
    <p style="margin:0 0 16px;">{{ __('mail.password_reset.expires', ['minutes' => $minutes]) }}</p>
    <p style="margin:0;">{{ __('mail.password_reset.ignore') }}</p>
</x-mail-layout>
