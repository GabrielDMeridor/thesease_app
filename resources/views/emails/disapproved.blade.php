@component('mail::message')
# Hello, {{ $user->name }}

We regret to inform you that your account has been disapproved.

If you think this was a mistake, please contact our support team.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
