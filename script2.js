function evaluateExpression(expression, x) {
    return new Function('x', `return ${expression};`)(x);
}

function newtonRaphson(f, df, x0, tolerance, maxIterations) {
    let x = x0;
    let iterations = [];

    for (let i = 0; i < maxIterations; i++) {
        let fx = evaluateExpression(f, x);
        let dfx = evaluateExpression(df, x);

        if (Math.abs(dfx) < tolerance) {
            return { error: 'Turunan mendekati nol. Pilih tebakan awal yang berbeda.', iterations: iterations };
        }

        let xNew = x - fx / dfx;
        iterations.push({ iteration: i + 1, x: xNew, fx: fx });

        if (Math.abs(xNew - x) < tolerance) {
            return { root: xNew, iterations: iterations };
        }

        x = xNew;
    }

    return { error: 'Iterasi maksimum tercapai tanpa konvergensi.', iterations: iterations };
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const f = document.getElementById('function').value;
        const df = document.getElementById('derivative').value;
        const initialGuess = parseFloat(document.getElementById('initialGuess').value);
        const tolerance = parseFloat(document.getElementById('tolerance').value);
        const maxIterations = parseInt(document.getElementById('maxIterations').value);

        const result = newtonRaphson(f, df, initialGuess, tolerance, maxIterations);

        const resultContainer = document.getElementById('resultContainer');
        resultContainer.innerHTML = ''; // Clear previous result

        if (result.root !== undefined) {
            resultContainer.innerHTML += `<div class="alert alert-success">Akar ditemukan: ${result.root}</div>`;
        } else if (result.error !== undefined) {
            resultContainer.innerHTML += `<div class="alert alert-danger">${result.error}</div>`;
        }

        if (result.iterations.length > 0) {
            const canvas = document.createElement('canvas');
            canvas.id = 'iterationChart';
            resultContainer.appendChild(canvas);

            const iterationLabels = result.iterations.map(item => item.iteration);
            const values = result.iterations.map(item => item.x);

            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: iterationLabels,
                    datasets: [{
                        label: 'Nilai x per Iterasi',
                        data: values,
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
        }
    });
});
