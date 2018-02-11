@component('mail::message')
# Hola {{$user->name}}!

Parece que has modificado tu correo electrónico. Por favor, confirma la nueva dirección a través del siguiente botón:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Confirmar mi cuenta
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
