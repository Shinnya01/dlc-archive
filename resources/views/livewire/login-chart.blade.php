<div class="h-auto w-full rounded-xl border border-neutral-200 bg-zinc-50 p-4 col-span-2">
                <div class="flex justify-between items-start">
                    <h1 class="text-3xl font-bold text-red-900">Login Chart</h1>
                    <div class="flex gap-2 max-w-sm">
                        <flux:select class="mb-4 w-xs" onchange="updateChart(this.value)">
                            <flux:select.option value="daily">Daily</flux:select.option>
                            <flux:select.option value="weekly">Weekly</flux:select.option>
                            <flux:select.option value="yearly">Yearly</flux:select.option>
                        </flux:select>
                    </div>
                </div>

                <div class="w-full h-80">
                    <canvas id="loginChart"></canvas>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
        const loginData = @json($loginData);

        const ctx = document.getElementById('loginChart').getContext('2d');

        // Initial chart as a line chart
        let loginChart = new Chart(ctx, {
            type: 'line', // <-- changed from 'bar' to 'line'
            data: {
                labels: loginData.daily.labels,
                datasets: [{
                    label: 'Logins',
                    data: loginData.daily.data,
                    borderColor: 'rgba(127, 29, 29, 1)',
                    backgroundColor: 'rgba(127, 29, 29, 0.6)',
                    fill: true,       // fills the area under the line
                    tension: 0.3,     // smooth curve
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
                            precision: 0, // ensures whole numbers
                            stepSize: 1   // forces integer steps
                        }
                    }
                }
            }
        });

        // Function to update chart when period changes
        function updateChart(period) {
            loginChart.data.labels = loginData[period].labels;
            loginChart.data.datasets[0].data = loginData[period].data;
            loginChart.update();
        }
    </script>
</div>