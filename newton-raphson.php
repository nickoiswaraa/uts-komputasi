<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newton-Raphson Method</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="mb-4">
            <h1 class="mb-3 text-center">Perhitungan Newton-Raphson</h1>
        </div>
       
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="function" class="form-label">Fungsi (f(x))</label>
                <input type="text" class="form-control" id="function" name="function" placeholder="Contoh: x^2 - 4" required>
            </div>
            <div class="mb-3">
                <label for="derivative" class="form-label">Turunan Fungsi (f'(x))</label>
                <input type="text" class="form-control" id="derivative" name="derivative" placeholder="Contoh: 2*x" required>
            </div>
            <div class="mb-3">
                <label for="initialGuess" class="form-label">Tebakan Awal</label>
                <input type="number" step="any" class="form-control" id="initialGuess" name="initialGuess" required>
            </div>
            <div class="mb-3">
                <label for="tolerance" class="form-label">Toleransi</label>
                <input type="number" step="any" class="form-control" id="tolerance" name="tolerance" value="0.001" required>
            </div>
            <div class="mb-3">
                <label for="maxIterations" class="form-label">Maksimum Iterasi</label>
                <input type="number" class="form-control" id="maxIterations" name="maxIterations" value="100" required>
            </div>
            <button type="submit" class="btn btn-primary">Hitung</button>
            <a href="index.html" class="btn btn-danger">Beranda</a>
        </form>

        <?php
        function evaluateExpression($expression, $x) {
            $expression = str_replace('x', (string)$x, $expression);
            return eval("return $expression;");
        }

        function newtonRaphson($f, $df, $x0, $tolerance, $max_iterations) {
            $x = $x0;
            $iterations = [];

            for ($i = 0; $i < $max_iterations; $i++) {
                $fx = evaluateExpression($f, $x);
                $dfx = evaluateExpression($df, $x);

                if (abs($dfx) < $tolerance) {
                    return ['error' => 'Turunan mendekati nol. Pilih tebakan awal yang berbeda.', 'iterations' => $iterations];
                }

                $x_new = $x - $fx / $dfx;
                $iterations[] = ['iteration' => $i + 1, 'x' => $x_new, 'fx' => $fx];

                if (abs($x_new - $x) < $tolerance) {
                    return ['root' => $x_new, 'iterations' => $iterations];
                }

                $x = $x_new;
            }

            return ['error' => 'Iterasi maksimum tercapai tanpa konvergensi.', 'iterations' => $iterations];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $function = $_POST['function'];
            $derivative = $_POST['derivative'];
            $initialGuess = floatval($_POST['initialGuess']);
            $tolerance = floatval($_POST['tolerance']);
            $maxIterations = intval($_POST['maxIterations']);

            $result = newtonRaphson($function, $derivative, $initialGuess, $tolerance, $maxIterations);
            if (isset($result['root'])) {
                echo "<div class='alert alert-success'>Akar ditemukan: " . $result['root'] . "</div>";
            } elseif (isset($result['error'])) {
                echo "<div class='alert alert-danger'>" . $result['error'] . "</div>";
            }

            if (!empty($result['iterations'])) {
                echo "<canvas id='iterationChart'></canvas>";
                $iterations = json_encode(array_map(fn($item) => $item['iteration'], $result['iterations']));
                $values = json_encode(array_map(fn($item) => $item['x'], $result['iterations']));
                echo "<script>
                    var ctx = document.getElementById('iterationChart').getContext('2d');
                    var chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: $iterations,
                            datasets: [{
                                label: 'Nilai x per Iterasi',
                                data: $values,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Iterasi'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Nilai x'
                                    }
                                }
                            }
                        }
                    });
                </script>";
            }
        }
        ?>
    </div>
</body>
</html>
