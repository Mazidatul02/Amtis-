<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #fde5e5; 
            color: #333; 
            font-family: 'Arial', sans-serif;
        }

        .container {
            background-color: #fff; 
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(255, 77, 106, 0.3);
            margin-top: 20px;
            position: relative; 
        }

        .btn-primary {
            background-color: #ff4d6a; 
            border-color: #ff4d6a; 
            display: block; 
            margin: 0 auto; 
        }

        .btn-primary:hover {
            background-color: #ff2d49; 
            border-color: #ff2d49;
        }

        .card {
            background-color: #f8e0e0; 
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); 
            margin-top: 20px;
            margin-bottom: 20px; 
            box-shadow: 0 0 20px rgba(255, 77, 106, 0.2);
        }

        .text-danger {
            color: #ff2d49; 
        }

        h2, h3 {
            color: #ff4d6a; 
        }

        table {
            background-color: #fff;
            margin-top: 20px; 
            margin-bottom: 20px; 
        }

        th, td {
            text-align: center;
        }

        th {
            background-color: #ff4d6a; 
            color: #fff; 
        }

        .datetime {
            position: absolute;
            top: 0;
            left: 0;
            padding: 10px;
        }
        
    </style>
</head>
<body>

<section class="datetime">
    <?php
    // Display current date and time
    date_default_timezone_set('Asia/Kuala_Lumpur');
    echo date("j/n/Y, g:i A");
    ?>
</section>

<section class="container mt-5">
    <h2 class="mb-4">Calculator</h2>
    <form method="post" action="">
        <div class="form-group">
            <b><label for="voltage">Voltage</label></b>
            <input type="number" class="form-control" name="voltage" step="0.01" required>
            <label for="voltage">Voltage (V):</label>
        </div>

        <div class="form-group">
            <b><label for="current">Current</label></b>
            <input type="number" class="form-control" name="current" step="0.01" required>
            <label for="current">Ampere (A):</label>
        </div>

        <div class="form-group">
            <b><label for="rate">Current Rate</label></b>
            <input type="number" class="form-control" name="rate" step="0.01" required>
            <label for="rate">(sen/kWh)</label>
        </div>

        <button type="submit" class="btn btn-primary">Calculate</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve user input
        $voltage = $_POST["voltage"];
        $current = $_POST["current"];
        $rate = $_POST["rate"];

        // Validate inputs 
        if (!empty($voltage) && !empty($current) && !empty($rate)) {
            // Calculate power and energy
            $power = $voltage * $current;
            $energy = $power / 1000; // Assuming 1 hour for simplicity

            // Calculate total charge based on tariff
            $totalCharge = calculateElectricityCharge($energy, $rate/100);

            // Display results 
            echo "<div class='card mt-4'>";
            echo "<div class='card-body'>";

            echo "<p class='card-text'>POWER: $power kw</p>";
            echo "<p class='card-text'>RATE: RM $totalCharge</p>";

            echo "</div>";
            echo "</div>";
            

            // Display the reference table
            echo "<table class='table'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th scope='col'>Hour</th>";
            echo "<th scope='col'>Energy (kWh)</th>";
            echo "<th scope='col'>Total (RM)</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            // Display hourly data 
            for ($hour = 1; $hour <= 24; $hour++) {
                $hourlyEnergy = $energy * $hour;
                $hourlyTotalCharge = calculateElectricityCharge($hourlyEnergy, $rate/100);

                echo "<tr>";
                echo "<td>$hour</td>";
                echo "<td>" . number_format($hourlyEnergy, 5) . "</td>"; 
                echo "<td>" . number_format($hourlyTotalCharge, 2) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p class='text-danger mt-4'>Please fill in all the fields.</p>";
        }
    }

    function calculateElectricityCharge($energy, $rate) {
        // Tariff rates based on TNB residential tariff
        $rates = [
            ['limit' => 200, 'rate' => 21.80],
            ['limit' => 300, 'rate' => 33.40],
            ['limit' => 600, 'rate' => 51.60],
            ['limit' => 900, 'rate' => 54.60],
            ['limit' => PHP_INT_MAX, 'rate' => 57.10],
        ];

      
        $totalCharge = 0;

        // Calculate total charge based on tariff
        foreach ($rates as $tier) {
            if ($energy > 0) {
                $consumed = min($energy, $tier['limit']);
                $totalCharge += ($consumed * $rate);
                $energy -= $consumed;
            } else {
                break;
            }
        }
        return number_format($totalCharge, 2);
        
    }
    ?>
</section>

<!-- Bootstrap 4 JS and Popper.js (for dropdowns) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
