<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Welcome</title>
</head>
<body>
<h>Thanks for registering to our service</h>
<p>You have registered with username <strong>{{$user->username}}</strong> and email <strong>{{$user->email}}</strong>
</p>
<p>You must be logged in before clicking on the confirmation link</p>
<p>Your confirmation link is: <a href="{{ route('confirm.user.email', $user->confirmation_hash) }}">Link</a></p>
<p>Or copy the following link and paste it into the address bar of your browser</p>
<p>{{ route('confirm.user.email', $user->confirmation_hash) }}</p>
</body>
</html>