<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Active Account</title>
</head>
<body>
    <h1>{{$details['title']}}</h1>
    <form method="POST" action={{route('active_account')}}>
        {{ csrf_field() }}
        <input type="hidden" value={{ $details['email'] }} name="email" id="email"/>
        <input type="submit" name="active" value="Kích hoạt tài khoản" />
    </form>
</body>
</html>