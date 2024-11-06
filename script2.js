// File: script.js
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function (e) {
        e.preventDefault();  // Mencegah submit form secara default

        // Ambil nilai input dari form
        const f = document.getElementById('function').value;
        const df = document.getElementById('derivative').value;
        const initialGuess = parseFloat(document.getElementById('initialGuess').value);
        const tolerance = parseFloat(document.getElementById('tolerance').value);
        const maxIterations = parseInt(document.getElementById('maxIterations').value);

        // Buat object yang berisi data untuk dikirim ke PHP
        const formData = new FormData();
        formData.append('function', f);
        formData.append('derivative', df);
        formData.append('initialGuess', initialGuess);
        formData.append('tolerance', tolerance);
        formData.append('maxIterations', maxIterations);

        // Buat request AJAX ke calculate2.php
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'calculate2.php', true);
        
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Parse hasil dari response PHP (dalam format JSON)
                const result = JSON.parse(xhr.responseText);

                const resultContainer = document.getElementById('resultContainer');
                resultContainer.innerHTML = ''; // Kosongkan hasil sebelumnya

                // Tampilkan hasil (akar atau error)
                if (result.root !== undefined) {
                    resultContainer.innerHTML += `<div class="alert alert-success">Akar ditemukan: ${result.root}</div>`;
                } else if (result.error !== undefined) {
                    resultContainer.innerHTML += `<div class="alert alert-danger">${result.error}</div>`;
                }

                // Jika ada iterasi, tampilkan chart
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
            }
        };

        xhr.send(formData);  // Kirim data ke PHP
    });
});