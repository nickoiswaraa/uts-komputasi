$(document).ready(function() {
    let chart;

    $('#newtonRaphsonForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'calculate2.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#result').html(`<p class="alert alert-danger">${response.error}</p>`);
                } else if (response.method === 'raphson') {
                    $('#result').html(`<p class="alert alert-success">Root found: ${response.root.toFixed(6)}</p>`);
                    drawNewtonRaphsonChart(response);
                }
            },
            error: function() {
                $('#result').html('<p class="alert alert-danger">An error occurred. Please try again.</p>');
            }
        });
    });

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