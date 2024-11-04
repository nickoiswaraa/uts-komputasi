$(document).ready(function() {
    let chart;

    $('#method').change(function() {
        if ($(this).val() === 'forward') {
            $('#forwardInputs').show();
            $('#raphsonInputs').hide();
        } else {
            $('#forwardInputs').hide();
            $('#raphsonInputs').show();
        }
    });

    $('#newtonForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'calculate1.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#result').html(`<p class="alert alert-danger">${response.error}</p>`);
                } else if (response.method === 'forward') {
                    $('#result').html(`<p class="alert alert-success">Nilai f(x) pada x = ${response.xInterpolate} adalah ${response.interpolatedValue.toFixed(4)}</p>`);
                    drawForwardInterpolationChart(response);
                }
            },
            error: function() {
                $('#result').html('<p class="alert alert-danger">Perhitungan Gagal. Silakan coba kembali dan pastikan semua formulir terisi</p>');
            }
        });
    });

    function drawForwardInterpolationChart(data) {
        if (chart) {
            chart.destroy();
        }

        // Combine all points and sort them by x value
        let allPoints = data.xValues.map((x, i) => ({x: x, y: data.yValues[i]}))
            .concat([{x: data.xInterpolate, y: data.interpolatedValue}])
            .sort((a, b) => a.x - b.x);

        const ctx = document.getElementById('newtonChart').getContext('2d');
        chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Given Points',
                    data: data.xValues.map((x, i) => ({x: x, y: data.yValues[i]})),
                    backgroundColor: 'blue',
                    pointRadius: 5
                }, {
                    label: 'Interpolated Point',
                    data: [{x: data.xInterpolate, y: data.interpolatedValue}],
                    backgroundColor: 'red',
                    pointRadius: 5
                }, {
                    label: 'Trend Line',
                    data: allPoints,
                    type: 'line',
                    borderColor: 'green',
                    fill: false,
                    pointRadius: 0
                }]
            },
            options: {
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'X'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Y'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Newton Forward Interpolation'
                    },
                    legend: {
                        display: true
                    }
                }
            }
        });
    }

    function drawNewtonRaphsonChart(data) {
        if (chart) {
            chart.destroy();
        }

        // Sort iterations by x value
        let sortedIterations = [...data.iterations].sort((a, b) => a.x - b.x);

        const ctx = document.getElementById('newtonChart').getContext('2d');
        chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'x value',
                    data: sortedIterations.map(iter => ({x: iter.x, y: iter.fx})),
                    borderColor: 'blue',
                    backgroundColor: 'blue',
                    pointRadius: 5,
                    showLine: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'X'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Y'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Newton-Raphson Method Convergence'
                    },
                    legend: {
                        display: true
                    }
                }
            }
        });
    }
});