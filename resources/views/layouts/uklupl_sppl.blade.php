<script type="text/javascript">
    $(document).ready(function() {
        $(function() {
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieData = {
                labels: [
                'Kab/Kota',
                'Pusat',
                'Provinsi',
                ],
                datasets: [
                {
                    data: [700, 500, 400],
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12']
                }
                ]
            }
            var pieOptions = {
                legend: {
                display: false
                }
            }
            var pieChart = new Chart(pieChartCanvas, {
                type: 'doughnut',
                data: pieData,
                options: pieOptions
            });
        });
    });
</script>
