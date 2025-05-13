<?php
// Get selected country from URL
$country = isset($_GET['country']) ? strtolower($_GET['country']) : null;

// Define display names
$countryNames = [
    'england' => 'England',
    'scotland' => 'Scotland',
    'philippines' => 'Philippines',
    'thailand' => 'Thailand',
];

// Determine label to show in the menu
$selectedCountryName = $countryNames[$country] ?? 'Select Country';
?>

<!-- Bootstrap 5 Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">SenseBox Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <!-- Country Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="countryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= htmlspecialchars($selectedCountryName) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="countryDropdown">
                        <li><a class="dropdown-item" href="box.php?country=england">England</a></li>
                        <li><a class="dropdown-item" href="box.php?country=scotland">Scotland</a></li>
                        <li><a class="dropdown-item" href="box.php?country=philippines">Philippines</a></li>
                        <li><a class="dropdown-item" href="box.php?country=thailand">Thailand</a></li>

                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap 5 JS Bundle (MUST be included in the page using this menu) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
