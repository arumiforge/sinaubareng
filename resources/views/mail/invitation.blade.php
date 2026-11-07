<x-mail-layout :title="__('mail.invitation.subject', ['school' => $schoolName])">
    <p style="margin:0 0 16px;">{{ __('mail.invitation.intro', ['school' => $schoolName, 'role' => $roleLabel]) }}</p>
    <p style="margin:0 0 16px;">{{ __('mail.invitation.instructions') }}</p>
    @include('mail.button', ['url' => $url, 'label' => __('mail.invitation.action')])
    <p style="margin:0 0 16px;">{{ __('mail.invitation.expires', ['date' => $expiresOn]) }}</p>
    <p style="margin:0;">{{ __('mail.invitation.ignore') }}</p>
</x-mail-layout>
