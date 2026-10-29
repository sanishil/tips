<x-mail::message>
Hello {{ $name }},

Your account is ready. Use this password to sign in:

<x-mail::panel>
<span style="font-size: 20px; font-weight: 700; letter-spacing: 1px; font-family: Consolas, 'Courier New', monospace;">{{ $password }}</span>
</x-mail::panel>

<x-mail::button :url="route('login')">
Sign in
</x-mail::button>

Once you're in, we'll ask you to choose a password of your own.

{{ config('app.name') }}<br>
Tripura Institute of Paramedical Sciences
</x-mail::message>
