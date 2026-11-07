<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }}</title>
    </head>
    {{-- Mail clients ignore stylesheets, so styles are inline. Colours are the light tokens. --}}
    <body style="margin:0;padding:24px 12px;background:#F7F8F7;font-family:Arial,Helvetica,sans-serif;color:#1C2420;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#FFFFFF;border:1px solid #E3E6E4;border-radius:10px;">
            <tr>
                <td style="padding:24px;font-size:16px;line-height:1.5;">
                    <p style="margin:0 0 16px;font-weight:bold;color:#115E59;">{{ __('common.app_name') }}</p>
                    {{ $slot }}
                </td>
            </tr>
        </table>
        <p style="max-width:560px;margin:16px auto 0;font-size:13px;color:#5B6660;">{{ __('mail.footer') }}</p>
    </body>
</html>
