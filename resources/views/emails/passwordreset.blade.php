<x-mail::message>
# Hi there,

Someone recently requested to reset your account password

<x-mail::panel>
    Temporary Password: {{$code}}
</x-mail::panel>

<x-mail::button :url="$url" color="success">
LOGIN
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
