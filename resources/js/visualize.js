export default (points) => ({
    chart: null,
    points,
    init() {
        if (!window.Chart) return;

        const ctx = this.$refs.canvas.getContext('2d');

        const data = this.points.map((p) => ({
            x: p.x,
            y: p.y,
            label: p.id,
            metadata: p.metadata,
        }));

        this.chart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Records',
                        data,
                        pointBackgroundColor: '#2563eb',
                        pointRadius: 4,
                    },
                ],
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const d = ctx.raw;
                                return d.metadata
                                    ? `${d.label} • meta: ${JSON.stringify(d.metadata).slice(0, 60)}`
                                    : d.label;
                            },
                        },
                    },
                    legend: { display: false },
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: 0,
                        max: 1,
                        title: { display: false },
                    },
                    y: {
                        type: 'linear',
                        min: 0,
                        max: 1,
                        title: { display: false },
                    },
                },
                responsive: true,
                maintainAspectRatio: false,
            },
        });
    },
    destroy() {
        if (this.chart) {
            this.chart.destroy();
        }
    },
});