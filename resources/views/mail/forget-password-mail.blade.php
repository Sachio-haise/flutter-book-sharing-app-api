<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PKT Education Center</title>
    <style>
        body {
            background-color: #bebec0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <div style="background-color: #FAFAFA;margin-top: 80px;margin-left:auto;margin-right:auto;width: 450px; height: auto; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); padding:2rem;font-family: sans-serif;border-radius: 5px">
        <img src="{{ url('/logo_.png') }}" alt="PKT Education Center" style="width: 80px;margin: 0 auto;display: block;">
        <div style="text-align:center;margin-bottom: 10px">
            <span style="color:#26DD37;font-size: 18px;font-weight: bold;width: 100%;">
                Book Sharing App
            </span>
        </div>
        <p style="font-size: 15px;color:gray">
            Hi {{ $data['name'] }}, let's reset your password. Here is your password reset code.
        </p>
        <div style="text-decoration: none;color:white;background-color: #26DD37;padding: 10px 20px;border-radius: 5px;margin: 0 auto;display: block;width:fit-content;font-weight: bold">
            {{ $data['code'] }}
        </div>
        <div style="font-size: 12px;color:gray;text-decoration:underline;margin-top: 15px ;text-align: center;font-weight:bold">
            Please Do Not Share The Code To Others.
        </div><br />

        <span style="color: #26DD37;font-weight: bold">Book Sharing App</span>
    </div>

</body>

</html>
