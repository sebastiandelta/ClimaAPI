<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicación de Clima</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="container mt-5">
    <h1 class="text-center animate__animated animate__fadeIn">Consulta el Clima</h1>
    <form method="GET" action="/weather" class="mb-4 animate__animated animate__fadeInDown">
        <div class="input-group">
            <input type="text" name="city" placeholder="Ingresa la ciudad" required class="form-control" />
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    @if(isset($weather['cod']) && $weather['cod'] === 200)
        <div class="card animate__animated animate__bounceIn">
            <div class="card-body">
                <h2 class="card-title">Clima en {{ $weather['name'] }}</h2>
                <p class="card-text">Temperatura: <strong>{{ $weather['main']['temp'] }} °C</strong></p>
                <p class="card-text">Condición: <strong>{{ $weather['weather'][0]['description'] }}</strong></p>
                <p class="card-text">Humedad: <strong>{{ $weather['main']['humidity'] }}%</strong></p>
                <p class="card-text">Velocidad del Viento: <strong>{{ $weather['wind']['speed'] }} m/s</strong></p>
            </div>
        </div>
        @if($imageUrl)
            <div class="mt-4 text-center">
                <img src="{{ $imageUrl }}" alt="Imagen de {{ $weather['name'] }}" class="img-fluid">
            </div>
        @endif
    @elseif(isset($error))
        <div class="alert alert-danger mt-3 animate__animated animate__shakeX">{{ $error }}</div>
    @else
        <p class="animate__animated animate__fadeIn">No se encontraron resultados.</p>
    @endif
</body>
</html>
