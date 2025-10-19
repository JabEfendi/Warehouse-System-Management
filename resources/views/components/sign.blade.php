<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body{
            background-image:  url('/image/bg-wms-sign.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            background-color: #484860;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }
        .glass-container{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 75%;
            height: 85%;
            overflow: hidden;
            box-sizing: border-box;
            background: rgba(44, 38, 56, 1);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            padding: 15px;
        }
        .container-image{
            width: 50%;
            height: 100%;
            border-radius: 15px;
            display: flex;
            overflow: hidden;
        }
        .container-image .carousel,
        .container-image .carousel-inner,
        .container-image .carousel-item{
            height: 100%;
        }
        .container-image img {
            width: 100%;
            width: 100%;
            border-radius: 15px;
        }
        .container-form{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .f-signin{
            background-color: #5a5666ff;
            color: white;
            border: 0;
        }
        .f-signin::placeholder{
            color: #b3b1b1ff;
        }
        .f-signin:focus {
            background-color: #5a5666ff;
            color: white;
            border: 0;
            outline: none;
            box-shadow: none;
        }
    </style>
</head>
<body>
    {{$slot}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>