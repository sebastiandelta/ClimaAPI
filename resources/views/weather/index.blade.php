<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicación de Clima</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body class="container mt-5">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <h1 class="text-center animate__animated animate__fadeIn">Consulta el Clima</h1>
    <button id="toggle-mode" class="mode-toggle" aria-label="Cambiar modo">
        <i class="fas fa-sun"></i>
    </button>

    <form method="GET" action="/weather" class="mb-4 animate__animated animate__fadeInDown">
        <div class="input-group">
            <input type="text" name="city" placeholder="Ingresa la ciudad" required class="form-control" />
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    @if(isset($weatherCurrent['cod']) && $weatherCurrent['cod'] === 200)
    <div class="card animate__animated animate__bounceIn">
        <div class="card-body">
            <h2 class="card-title">Clima en {{ $weatherCurrent['name'] }}</h2>
            <p class="card-text">Temperatura: <strong>{{ $weatherCurrent['main']['temp'] }} °C</strong></p>
            <p class="card-text">Condición: <strong>{{ $weatherCurrent['weather'][0]['description'] }}</strong></p>
            <p class="card-text">Humedad: <strong>{{ $weatherCurrent['main']['humidity'] }}%</strong></p>
            <p class="card-text">Velocidad del Viento: <strong>{{ $weatherCurrent['wind']['speed'] }} m/s</strong></p>
        </div>
    </div>

    <!-- Pronóstico extendido -->
    <h3 class="mt-4 mb-4">Pronóstico del Clima</h3>
    <div class="row">
        @foreach($weatherForecast['list'] as $forecast)
            @if($loop->index % 8 == 0) <!-- Mostrar solo 1 vez cada 8 horas (cada día) -->
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ \Carbon\Carbon::createFromTimestamp($forecast['dt'])->format('d M, H:i') }}</h5>
                            <p class="card-text">Temperatura: <strong>{{ $forecast['main']['temp'] }} °C</strong></p>
                            <p class="card-text">Condición: <strong>{{ $forecast['weather'][0]['description'] }}</strong></p>
                            <p class="card-text">Humedad: <strong>{{ $forecast['main']['humidity'] }}%</strong></p>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <h3 class="mt-4">Tendencias Climáticas</h3>
    <canvas id="weatherChart" style="max-width: 600px; margin: auto;"></canvas>
    

    @if($imageUrl)
        <div class="mt-4 d-flex justify-content-center">
            <img src="{{ $imageUrl }}" alt="Imagen de {{ $weatherCurrent['name'] }}" class="img-fluid img-weather" style="max-width: 600px; height: auto;">
        </div>
    @endif

@else
    <p class="animate__animated animate__fadeIn">No se encontraron resultados.</p>
@endif
</body>
</html>
<script>
    const ctx = document.getElementById('weatherChart').getContext('2d');
    const weatherChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($dates),
            datasets: [
                {
                    label: 'Temperatura (°C)',
                    data: @json($temperatures),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                },
                {
                    label: 'Humedad (%)',
                    data: @json($humidities),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Valores'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Fecha y Hora'
                    }
                }
            }
        }
    });

// Comprobar si el navegador soporta geolocalización
if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Hacer una solicitud a la API de OpenWeather para obtener el nombre de la ciudad
            fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid={{ env('OPENWEATHER_API_KEY') }}&units=metric`)
                .then(response => response.json())
                .then(data => {
                    // Redirigir a la ruta con la ciudad
                    window.location.href = `/weather?city=${data.name}`;
                })
                .catch(error => {
                    console.error('Error al obtener la ubicación:', error);
                });
        }, function() {
            alert("No se pudo obtener la ubicación.");
        });
    } else {
        alert("Geolocalización no soportada en este navegador.");
    }
    const toggleButton = document.getElementById('toggle-mode');
    const body = document.body;

    // Comprobar el modo almacenado en localStorage
    if (localStorage.getItem('dark-mode') === 'enabled') {
        body.classList.add('dark-mode');
        toggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Icono de luna en modo oscuro
    }

    toggleButton.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('dark-mode', 'enabled');
            toggleButton.innerHTML = '<i class="fas fa-sun"></i>'; // Icono de sol en modo oscuro
        } else {
            localStorage.setItem('dark-mode', 'disabled');
            toggleButton.innerHTML = '<i class="fas fa-moon"></i>'; // Icono de luna en modo claro
        }
    });

</script>
