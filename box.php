<!DOCTYPE html>
<html>
<head>
    <title>SenseBox Country Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        .card {
            margin-bottom: 20px;
        }
        .countdown {
            font-weight: bold;
            font-size: 1.2rem;
            color: green;
        }
    </style>
</head>
<body class="container py-4">

    <?php include 'menu.php'; ?>

    <h1 class="mb-4">SenseBox Country-Based Dashboard</h1>

    <!-- Countdown Timer -->
    <div class="mb-3">
        <p class="countdown">Refreshing in <span id="countdown">15</span> seconds...</p>
    </div>

    <!-- Map -->
    <div id="map" class="mb-4"></div>

    <!-- Sensor Cards -->
    <div id="sensorCards" class="row"></div>

    <!-- JS Libraries -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        const countryToBoxIds = {
          'england': ['65314271dac0f10007c67a7a'],
          'scotland': ['5c74ecf39e675600190aca7e'],
          'philippines': ['5faf231d9b2df8001bc7da8b'],
          'thailand': ['5ca9ffb8695e3b001acb3189', '5c68e255a1008400190cd5fa']
      };

        const urlParams = new URLSearchParams(window.location.search);
        const country = urlParams.get('country')?.toLowerCase() || 'england';
        const boxId = countryToBoxId[country];

        let map, marker;
        let countdown = 15;

        function fetchData() {
            if (!boxId) {
                $('#sensorCards').html(`<div class="alert alert-danger">Invalid country selected.</div>`);
                return;
            }

            $.getJSON(`https://api.opensensemap.org/boxes/${boxId}`, function (data) {
                const coords = data.currentLocation.coordinates;
                const longitude = coords[0];
                const latitude = coords[1];
                const boxName = data.name;

                // Initialize map
                if (!map) {
                    map = L.map('map').setView([latitude, longitude], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);
                    marker = L.marker([latitude, longitude]).addTo(map).bindPopup(boxName).openPopup();
                } else {
                    marker.setLatLng([latitude, longitude]);
                }

                const sensors = data.sensors;
                let cardsHtml = `<h4 class="mt-4">${boxName}</h4>`;

                sensors.forEach(sensor => {
                    const sensorId = sensor._id;
                    const sensorName = sensor.title;
                    const value = sensor.lastMeasurement?.value ?? 'No Data';
                    const unit = sensor.unit ?? '';
                    const chartId = `chart-${sensorId}`;

                    cardsHtml += `
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">${sensorName}</div>
                            <div class="card-body">
                                <h5 class="card-title">Latest Value: ${value} ${unit}</h5>
                                <canvas id="${chartId}" height="150"></canvas>
                            </div>
                        </div>
                    </div>`;
                });

                $('#sensorCards').html(cardsHtml);

                // Create charts
                sensors.forEach(sensor => {
                    const chartId = `chart-${sensor._id}`;
                    const ctx = document.getElementById(chartId).getContext('2d');
                    const value = sensor.lastMeasurement?.value ?? 0;

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['t-4', 't-3', 't-2', 't-1', 'Now'],
                            datasets: [{
                                label: sensor.title,
                                data: [
                                    Math.random() * 100,
                                    Math.random() * 100,
                                    Math.random() * 100,
                                    Math.random() * 100,
                                    parseFloat(value) || 0
                                ],
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderWidth: 2,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            });
        }

        function startCountdown() {
            setInterval(() => {
                countdown--;
                $('#countdown').text(countdown);
                if (countdown <= 0) {
                    countdown = 15;
                    $('#countdown').text(countdown);
                    fetchData();
                }
            }, 1000);
        }

        $(document).ready(function () {
            fetchData();
            startCountdown();
        });
    </script>
</body>
</html>
