<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newton-Raphson Method</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script2.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <div id="resultContainer" class="mt-4"></div>
    </div>
    
    <script src="script2.js"></script>
</body>
</html>