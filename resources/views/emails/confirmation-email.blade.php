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
            Thank you for submitting your ticket
            @if($data->user)
                {{$data->user->first_name}} {{$data->user->last_name}}
            @else
                {{$data->guest_first_name}} {{$data->guest_last_name}}
            @endif
        </h1>
        <label>Ticket Number: </label>
        <p>{{$data->ticket_number}}</p>
        <label>Reported Date: </label>
        <p>{{$data->reported_date}}</p>
        <label>Type: </label>
        <p>{{$data->type}}</p>
        <label>Status: </label>
        <p>{{$data->status}}</p>
        <label>Description: </label>
        <p>{{$data->description}}</p>

        <h3>The team will review this and get back to your as soon as possible.</h3>
        <h4>Best regards, Laravel</h4>
    </div>
    <!-- Embed the image -->
    @if(isset($data->file_path_url))
    <div>
        <h2>Attached Image</h2>
        <img src="{{ $message->embed(public_path("/storage/$data->file_path_url")) }}" alt="Image Attachment">
    </div> 
    @endif
</body>
</html>
