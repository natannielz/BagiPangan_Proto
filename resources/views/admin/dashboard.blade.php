<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    {{-- Date range picker --}}
                    <div class="flex flex-wrap items-end gap-4 border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Dari Tanggal</label>
                            <input type="date" id="from" class="rounded-md border-gray-300 shadow-sm text-sm" value="{{ now()->subDays(29)->toDateString() }}">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Sampai Tanggal</label>
                            <input type="date" id="to" class="rounded-md border-gray-300 shadow-sm text-sm" value="{{ now()->toDateString() }}">
                        </div>
                        <button onclick="fetchStats()" class="px-4 py-2 text-sm font-semibold bg-brand-600 text-white rounded-md hover:bg-brand-800">
                            Terapkan
                        </button>
                    </div>

                    {{-- KPI cards --}}
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-xs text-gray-500 mb-1">Porsi Diselamatkan</div>
                            <div id="kpi-portions" class="text-2xl font-bold text-brand-700">–</div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-xs text-gray-500 mb-1">Tingkat Penyelesaian</div>
                            <div id="kpi-rate" class="text-2xl font-bold text-green-600">–</div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-xs text-gray-500 mb-1">Total Donasi</div>
                            <div id="kpi-total" class="text-2xl font-bold text-gray-700">–</div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 text-center">
                            <div class="text-xs text-gray-500 mb-1">Rata-rata Jam Klaim</div>
                            <div id="kpi-avg-claim" class="text-2xl font-bold text-indigo-600">–</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="font-semibold text-gray-900 mb-3">Donasi per Hari</div>
                            <canvas id="donationsPerDayChart" height="120"></canvas>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="font-semibold text-gray-900 mb-3">Breakdown Status</div>
                            <canvas id="statusChart" height="120"></canvas>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="font-semibold text-gray-900 mb-3">Top Donors</div>
                        <canvas id="topDonorsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lineChart = null;
        let statusChart = null;

        async function fetchStats() {
            if (!window.axios || !window.Chart) return;

            const from = document.getElementById('from').value;
            const to   = document.getElementById('to').value;

            const [statsRes, topRes] = await Promise.all([
                window.axios.get(`/api/v1/dashboard/stats?from=${from}&to=${to}`, { headers: { Accept: 'application/json' } }),
                window.axios.get('/api/v1/dashboard/top-donors', { headers: { Accept: 'application/json' } }),
            ]);

            const data      = statsRes.data?.data || {};
            const perDay    = data.donations_per_day || { labels: [], counts: [] };
            const breakdown = data.status_breakdown  || [];
            const topDonors = topRes.data?.data       || [];

            // Update KPI cards
            document.getElementById('kpi-portions').textContent   = data.portions_saved ?? '–';
            document.getElementById('kpi-rate').textContent       = data.completion_rate != null ? data.completion_rate + '%' : '–';
            document.getElementById('kpi-total').textContent      = data.totals?.total ?? '–';
            document.getElementById('kpi-avg-claim').textContent  = data.avg_claim_hours != null ? data.avg_claim_hours + 'j' : '–';

            if (lineChart) {
                lineChart.data.labels            = perDay.labels;
                lineChart.data.datasets[0].data  = perDay.counts;
                lineChart.update('active');
            } else {
                const ctxLine = document.getElementById('donationsPerDayChart');
                if (ctxLine) {
                    lineChart = new window.Chart(ctxLine, {
                        type: 'line',
                        data: {
                            labels: perDay.labels,
                            datasets: [{
                                label: 'Donasi',
                                data: perDay.counts,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                                fill: true,
                                tension: 0.25,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                        },
                    });
                }
            }

            if (statusChart) {
                statusChart.data.labels            = breakdown.map(x => x.status);
                statusChart.data.datasets[0].data  = breakdown.map(x => x.total);
                statusChart.update('active');
            } else {
                const ctxStatus = document.getElementById('statusChart');
                if (ctxStatus) {
                    statusChart = new window.Chart(ctxStatus, {
                        type: 'doughnut',
                        data: {
                            labels: breakdown.map(x => x.status),
                            datasets: [{
                                data: breakdown.map(x => x.total),
                                backgroundColor: ['#22c55e', '#eab308', '#3b82f6', '#a855f7', '#ef4444'],
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } },
                        },
                    });
                }
            }

            // Top donors chart (one-time)
            if (!window._topDonorsChartInit) {
                window._topDonorsChartInit = true;
                const ctxTop = document.getElementById('topDonorsChart');
                if (ctxTop) {
                    new window.Chart(ctxTop, {
                        type: 'bar',
                        data: {
                            labels: topDonors.map(x => x.name || `#${x.donor_id}`),
                            datasets: [{
                                label: 'Total',
                                data: topDonors.map(x => x.total),
                                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                        },
                    });
                }
            }
        }

        (async function () {
            await fetchStats();
        })();
    </script>
</x-app-layout>
