<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>
        <h1>
            {{$password == null ? "Your account has been created " : "Your account has been updated "}} {{$data->full_name}}
        </h1>

        <h3>Your email is: {{$data->email}}</h3>
        <h3>Your password is: {{$password !== null ? $password : "password"}}</h3>
    </div>

</body>
</html>
