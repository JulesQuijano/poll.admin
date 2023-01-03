<x-mail::message>
# Hello {{ $user }}

Your new password is {{ $password }}.

Thanks,<br>
Super Admin<br>
{{ config('app.name') }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
