<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            color: #222;
        }

        .container {
            max-width: 460px;
            margin: 60px auto;
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        }

        h1 {
            color: #102B42;
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 14px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            border: none;
            background: #102B42;
            color: white;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .link {
            margin-top: 18px;
            text-align: center;
        }

        .link a {
            color: #102B42;
            font-weight: bold;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Registro de usuario</h1>
    <p>Crear integrante del equipo técnico</p>

    @if($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/register">
        @csrf

        <label>Nombre</label>
        <input type="text" name="name" value="{{ old('name') }}" required>

        <label>Correo electrónico</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <label>Confirmar contraseña</label>
        <input type="password" name="password_confirmation" required>

        <button type="submit">Registrarse</button>
    </form>

    <div class="link">
        <a href="/login">Ya tengo cuenta</a>
    </div>
</div>

</body>
</html>
