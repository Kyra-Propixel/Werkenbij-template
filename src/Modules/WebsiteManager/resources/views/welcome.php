<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            right: 25%;
            left: 19%;
        }

        #welcome-page {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            text-align: center;
        }

        #welcome-page img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        #welcome-page p {
            font-weight: bold;
            font-size: 18px;
            color: #333333;
            margin-bottom: 30px;
        }

        .intro-links a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .intro-links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div id="welcome-page">
    <h1 class="text-center">
        <img src="https://dev01.propixel.nl//Logo_Transparante-achtergrond.svg" alt="Logo">
    </h1>
    <p>De website die u nu bekijkt wordt nog aangewerkt door ProPixel. <br>Meer informatie?</p>
    <div class="intro-links">
        <a href="https://propixel.nl">Bezoek Ons</a>
    </div>
</div>

</body>
</html>