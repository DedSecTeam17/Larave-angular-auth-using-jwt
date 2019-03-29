@component('mail::message')
# Introduction



Hi there
Click link below to reset your password

@component('mail::button', ['url' => 'http://localhost:4200/response_password_reset?token='.$token])
Reset My Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
