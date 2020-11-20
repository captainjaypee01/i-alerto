<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification</title>
</head>
<body>
    <span>Hi {{$user->name}}! your verification code is <b>{{$user->verification_code}}</b> to activate your account.</span>
</body>
</html>