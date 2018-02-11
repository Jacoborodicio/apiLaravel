@component('mail::message')
# Hola {{$user->name}}!

Gracias por registrarte con nosotros. Por favor, valida tu cuenta a través del siguiente botón:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Confirmar mi cuenta
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
