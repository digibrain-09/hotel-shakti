<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scantable</title>
    <link rel="shortcut icon" href="{{asset('assets/img/final_png.png')}}">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .message-container {
            text-align: center;
            padding: 20px;
        }

        .message-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .message-container p {
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h1>Oops! Something went wrong</h1>
        <p>{{ $msg }}</p>
    </div>
</body>
</html>
