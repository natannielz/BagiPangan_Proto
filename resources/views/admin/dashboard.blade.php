<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Hero Banner --}}
        <x-dashboard-banner />

        {{-- Date range picker --}}
        <div class="flex flex-wrap items-end gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" id="from" class="rounded-xl border-gray-200 shadow-sm text-sm focus:border-brand-600 focus:ring-brand-600" value="{{ now()->subDays(29)->toDateString() }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" id="to" class="rounded-xl border-gray-200 shadow-sm text-sm focus:border-brand-600 focus:ring-brand-600" value="{{ now()->toDateString() }}">
            </div>
            <button onclick="fetchStats()" class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 transition-colors">
                Terapkan
            </button>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Porsi Diselamatkan</div>
                <div id="kpi-portions" class="text-2xl font-bold text-brand-700" data-countup="0">-</div>
                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                    <span id="kpi-portions-trend" class="font-semibold text-brand-600"></span>
                    vs bulan lalu
                </div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Tingkat Penyelesaian</div>
                <div id="kpi-rate" class="text-2xl font-bold text-brand-600">-</div>
                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                    <span id="kpi-rate-trend" class="font-semibold text-brand-600"></span>
                    vs bulan lalu
                </div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Total Donasi</div>
                <div id="kpi-total" class="text-2xl font-bold text-gray-700" data-countup="0">-</div>
                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                    <span id="kpi-total-trend" class="font-semibold text-brand-600"></span>
                    vs bulan lalu
                </div>
            </div>
            <div class="card-hover rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="text-xs font-medium text-gray-500 mb-2">Rata-rata Jam Klaim</div>
                <div id="kpi-avg-claim" class="text-2xl font-bold text-brand-600">-</div>
                <div class="mt-1 flex items-center gap-1 text-xs text-gray-400">
                    <span id="kpi-avg-trend" class="font-semibold text-brand-600"></span>
                    vs bulan lalu
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-semibold text-gray-900">Donasi per Hari</div>
                    <div x-data="{ period: 'week' }" class="flex rounded-lg bg-gray-100 p-0.5">
                        <button @click="period='week'; window.setPeriod && window.setPeriod('week')"
                                :class="period === 'week' ? 'bg-white shadow-sm text-brand-700' : 'text-gray-500'"
                                class="rounded-md px-3 py-1 text-xs font-medium transition-all">Minggu</button>
                        <button @click="period='month'; window.setPeriod && window.setPeriod('month')"
                                :class="period === 'month' ? 'bg-white shadow-sm text-brand-700' : 'text-gray-500'"
                                class="rounded-md px-3 py-1 text-xs font-medium transition-all">Bulan</button>
                    </div>
                </div>
                <canvas id="donationsPerDayChart" height="140"></canvas>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="font-semibold text-gray-900 mb-4">Breakdown Status</div>
                <canvas id="statusChart" height="140"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="font-semibold text-gray-900 mb-4">Top Donors</div>
            <canvas id="topDonorsChart" height="120"></canvas>
        </div>
    </div>

    <script>
        // Chart.js global defaults
        Chart.defaults.font.family = 'Plus Jakarta Sans';
        Chart.defaults.color = '#9ca3af';

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
            document.getElementById('kpi-portions').textContent   = data.portions_saved ?? '-';
            document.getElementById('kpi-rate').textContent       = data.completion_rate != null ? data.completion_rate + '%' : '-';
            document.getElementById('kpi-total').textContent      = data.totals?.total ?? '-';
            document.getElementById('kpi-avg-claim').textContent  = data.avg_claim_hours != null ? data.avg_claim_hours + 'j' : '-';

            if (lineChart) {
                lineChart.data.labels            = perDay.labels;
                lineChart.data.datasets[0].data  = perDay.counts;
                lineChart.options.plugins.highlightPoint = { activeIndex: perDay.counts.length - 1 };
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
                                borderColor: '#16a34a',
                                backgroundColor: 'rgba(22,163,74,.06)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 7,
                                pointHoverBackgroundColor: '#16a34a',
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                highlightPoint: { activeIndex: perDay.counts.length - 1 },
                            },
                            scales: {
                                x: { grid: { display: false }, border: { display: false } },
                                y: { grid: { color: 'rgba(0,0,0,.04)' }, border: { display: false } },
                            },
                        },
                        plugins: [window.highlightPlugin],
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
                                backgroundColor: ['#16a34a', '#eab308', '#3b82f6', '#a855f7', '#ef4444'],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            cutout: '65%',
                            plugins: { legend: { position: 'bottom', labels: { padding: 16 } } },
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
                                backgroundColor: 'rgba(22,163,74,0.7)',
                                borderRadius: 8,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { grid: { display: false }, border: { display: false } },
                                y: { grid: { color: 'rgba(0,0,0,.04)' }, border: { display: false } },
                            },
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
