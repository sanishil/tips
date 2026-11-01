<x-mail::message>
Here's your code to {{ $action }}:

<x-mail::panel>
<span style="font-size: 26px; font-weight: 700; letter-spacing: 6px;">{{ $code }}</span>
</x-mail::panel>

It works for {{ \App\Support\EmailOtp::EXPIRES_MINUTES }} minutes. If you didn't ask for it, you can ignore this email.

{{ config('app.name') }}<br>
Gomati District Polytechnic<br>
Fulkumari Part, Tripura 799013
</x-mail::message>
