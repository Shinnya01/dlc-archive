<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <h1 class="text-5xl font-bold text-red-900">Welcome, {{ $name }}</h1>

        <!-- Stats Cards -->
        <livewire:dashboard-data/>

        <!-- Login Chart with buttons -->
  
        <div class="grid grid-cols-3 gap-4">
            <livewire:login-chart/>
            <livewire:recent-logins />

        </div>
       
        
        <!-- Background Pattern Placeholder -->
        <livewire:history/>
    </div>

    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const loginData = @json($loginData);

        const ctx = document.getElementById('loginChart').getContext('2d');

        // Initial chart
        let loginChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: loginData.daily.labels,
                datasets: [{
                    label: 'Logins',
                    data: loginData.daily.data,
                    backgroundColor: 'rgba(127, 29, 29, 0.8)',
                    borderColor: 'rgba(127, 29, 29, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true,
                        ticks: {
                            precision: 0, // ensures whole numbers
                            stepSize: 1   // forces integer steps
                        }
                        }
                    }
            }
        });

        // Function to update chart
        function updateChart(period) {
            loginChart.data.labels = loginData[period].labels;
            loginChart.data.datasets[0].data = loginData[period].data;
            loginChart.update();
        }
    </script> --}}
</x-layouts.app>
