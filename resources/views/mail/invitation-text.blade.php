{{ __('mail.invitation.intro', ['school' => $schoolName, 'role' => $roleLabel]) }}

{{ __('mail.invitation.instructions') }}

{!! $url !!}

{{ __('mail.invitation.expires', ['date' => $expiresOn]) }}

{{ __('mail.invitation.ignore') }}
