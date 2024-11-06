<?php
// File: calculate2.php
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
    // Mendapatkan data dari AJAX
    $function = $_POST['function'];
    $derivative = $_POST['derivative'];
    $initialGuess = floatval($_POST['initialGuess']);
    $tolerance = floatval($_POST['tolerance']);
    $maxIterations = intval($_POST['maxIterations']);

    // Memanggil metode Newton-Raphson
    $result = newtonRaphson($function, $derivative, $initialGuess, $tolerance, $maxIterations);

    // Mengembalikan hasil dalam format JSON
    header('Content-Type: application/json');
    echo json_encode($result);
}