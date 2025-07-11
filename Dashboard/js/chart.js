// Inisialisasi Area Chart
const orderTrendCtx = document.getElementById('orderTrendChart');
new Chart(orderTrendCtx, {
    type: 'line',
    data: {
        labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
        datasets: [{
            label: 'Order Logo',
            data: [8, 12, 6, 10],
            backgroundColor: 'rgba(13, 110, 253, 0.2)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 2
        }]
    }
});

// Inisialisasi Bar Chart (jika ada)
// ...