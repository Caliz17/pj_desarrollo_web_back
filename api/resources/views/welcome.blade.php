<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a la API</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: #333;
        }

        .container {
            text-align: center;
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
            transition: transform 0.3s;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #007bff;
        }

        p {
            font-size: 1.2em;
            margin-bottom: 30px;
            color: #555;
        }

        .additional-message {
            font-size: 1em;
            margin-bottom: 20px;
            color: #666; /* Color gris más suave */
        }

        .button {
            display: inline-block;
            padding: 15px 30px;
            font-size: 1.2em;
            color: white;
            background-color: #ff6f61;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .button:hover {
            background-color: #ff3b30; /* Color de hover */
            transform: scale(1.05);
        }

        /* Estilo para iconos */
        .fa {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><i class="fas fa-rocket"></i> ¡Bienvenido a nuestra API!</h1>
    <p>Esta API te permite interactuar con nuestros recursos de manera sencilla y efectiva.</p>
    <p class="additional-message">¡Prepara tus cartas y compite en el campo de batalla!</p>
    <a class="button" href="{{ url('/api/documentation') }}">
        <i class="fas fa-book-open"></i> Ver Documentación
    </a>
</div>

</body>
</html>
