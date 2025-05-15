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
        // Mapping countries to box IDs arrays
        const countryToBoxIds = {
          'uk': ['62a87ec4b91502001bdbbfc8','5cbb1c3b815d52001aaaef9f'],
          'scotland': ['5c74ecf39e675600190aca7e'],
          'philippines': ['5faf231d9b2df8001bc7da8b'],
          'thailand': ['5ca9ffb8695e3b001acb3189']
        };

        const urlParams = new URLSearchParams(window.location.search);
        const country = urlParams.get('country')?.toLowerCase() || 'england';
        const boxIds = countryToBoxIds[country] || [];

        let map;
        let markers = [];
        let countdown = 15;

        function fetchData() {
            if (boxIds.length === 0) {
                $('#sensorCards').html(`<div class="alert alert-danger">Invalid country selected.</div>`);
                return;
            }

            $('#sensorCards').html('');  // Clear old cards

            let bounds = [];

            // Fetch all boxes one by one
            boxIds.forEach(boxId => {
                $.getJSON(`https://api.opensensemap.org/boxes/${boxId}`, function (data) {
                    const coords = data.currentLocation.coordinates;
                    const longitude = coords[0];
                    const latitude = coords[1];
                    const boxName = data.name;

                    // Add marker to map or create map
                    if (!map) {
                        map = L.map('map').setView([latitude, longitude], 6);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(map);
                    }

                    const marker = L.marker([latitude, longitude]).addTo(map).bindPopup(boxName);
                    markers.push(marker);
                    bounds.push([latitude, longitude]);

                    // Add sensor cards for this box
                    let cardsHtml = `<h4 class="mt-4">${boxName}</h4>`;

                    data.sensors.forEach(sensor => {
                        const sensorId = sensor._id;
                        const sensorName = sensor.title;
                        const value = sensor.lastMeasurement?.value ?? 'No Data';
                        const unit = sensor.unit ?? '';
                        const chartId = `chart-${boxId}-${sensorId}`;

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

                    $('#sensorCards').append(cardsHtml);

                    // Create charts for sensors in this box
                    data.sensors.forEach(sensor => {
                        const chartId = `chart-${boxId}-${sensor._id}`;
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

                    // Fit map to all markers once all boxes are loaded
                    if (bounds.length === boxIds.length) {
                        const leafletBounds = L.latLngBounds(bounds);
                        map.fitBounds(leafletBounds, {padding: [50, 50]});
                    }
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

                    // Clear old markers before fetching new data
                    if (map) {
                        markers.forEach(marker => map.removeLayer(marker));
                        markers = [];
                    }

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
