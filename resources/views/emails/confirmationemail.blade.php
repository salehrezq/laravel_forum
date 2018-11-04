@component('mail::message')
# Confirmation Email

Thanks for registering to our forum

<p>You have registered with username <strong>{{$user->username}}</strong> and email <strong>{{$user->email}}</strong>
</p>
<p>Click on the following link to confirm your email. You must be logged in before clicking on the confirmation link:</p>
@component('mail::button', ['url' => route('confirm.user.email', $user->confirmation_hash)])
    confirmation link
@endcomponent
<p>Or copy the following link and paste it into the address bar of your browser:</p>
<p><strong>{{ route('confirm.user.email', $user->confirmation_hash) }}</strong></p>

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
