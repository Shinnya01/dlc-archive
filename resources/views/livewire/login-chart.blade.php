<div x-data x-init="
    const ctx = $refs.canvas.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Logins',
                data: @json($data),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
">
    <canvas x-ref="canvas" width="400" height="200"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function renderLoginChart(labels, data) {
        const ctx = document.getElementById('loginChart').getContext('2d');

        // Destroy existing chart if exists (important for Livewire re-renders)
        if (window.loginChartInstance) {
            window.loginChartInstance.destroy();
        }

        window.loginChartInstance = new Chart(ctx, {
            type: 'bar', // change type if you want
            data: {
                labels: labels,
                datasets: [{
                    label: 'Logins',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Initial render on page load
    document.addEventListener('livewire:load', function () {
        renderLoginChart(@json($labels), @json($data));
    });

    // Re-render chart whenever Livewire updates the component
    Livewire.hook('message.processed', (message, component) => {
        if (component.fingerprint.name === 'login-chart') {
            renderLoginChart(@json($labels), @json($data));
        }
    });
</script>
