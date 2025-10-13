<div class="h-auto w-full rounded-xl border border-neutral-200 bg-zinc-50 p-4 col-span-2">
    <div class="flex justify-between items-start mb-4">
        <h1 class="text-3xl font-bold text-red-900">Login Chart</h1>
        <div class="flex gap-2 max-w-sm">
            <flux:select class="mb-4 w-xs" onchange="updateChart(this.value)">
                <flux:select.option value="daily">Daily</flux:select.option>
                <flux:select.option value="weekly">Weekly</flux:select.option>
                <flux:select.option value="yearly">Yearly</flux:select.option>
            </flux:select>
        </div>
    </div>

    <div class="flex-1">
        <div id="noDataMessage" class="text-center text-gray-500 text-lg" style="display: none;">
            No data yet
        </div>
        <canvas id="loginChart" class="w-full h-full" style="display: none;"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const loginData = @json($loginData);
        const ctx = document.getElementById('loginChart').getContext('2d');
        const noDataMessage = document.getElementById('noDataMessage');

        function hasData(period) {
            return loginData[period].data.some(value => value > 0);
        }

        let loginChart = null;

        function renderChart(period = 'daily') {
            if (!hasData(period)) {
                // No data: hide canvas, show message
                if (loginChart) loginChart.destroy();
                document.getElementById('loginChart').style.display = 'none';
                noDataMessage.style.display = 'block';
                return;
            }

            // Has data: show canvas, hide message
            noDataMessage.style.display = 'none';
            const canvas = document.getElementById('loginChart');
            canvas.style.display = 'block';

            if (loginChart) {
                loginChart.data.labels = loginData[period].labels;
                loginChart.data.datasets[0].data = loginData[period].data;
                loginChart.update();
            } else {
                loginChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: loginData[period].labels,
                        datasets: [{
                            label: 'Logins',
                            data: loginData[period].data,
                            borderColor: 'rgba(127, 29, 29, 1)',
                            backgroundColor: 'rgba(127, 29, 29, 0.6)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: 'rgba(127, 29, 29, 1)',
                            pointBorderColor: '#fff',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { 
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        }

        function updateChart(period) {
            renderChart(period);
        }

        // Initial render
        renderChart('daily');
    </script>
</div>
