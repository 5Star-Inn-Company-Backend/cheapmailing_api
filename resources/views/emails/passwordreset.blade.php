<x-mail::message>
# Hi there,

Your confirmation code is below — enter it in your open browser window and we'll help you change your password.

<x-mail::panel>
     {{$code}}
</x-mail::panel>

    If you didn’t request this email, there’s nothing to worry about — you can safely ignore it.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
