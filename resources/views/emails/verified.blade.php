@component('mail::message')
# Hello, {{ $user->name }}

Congratulations! Your account has been verified successfully.

You can now access the full features of our system.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
