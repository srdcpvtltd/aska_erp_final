<script>
    function getDayWiseAttendanceFilterParam() {
        let params = {
            datetimes: $('#dateRange').val(),
        }
        return params;
    }

    $('#download-excel-report').on('click', function(e) {
        e.preventDefault();
        console.log('comming to js ');
        let route = $(this).data('href');
        let filtered_params = getDayWiseAttendanceFilterParam();
        filtered_params.download_excel = true;
        let queryString = $.param(filtered_params)
        let url = route + '?' + queryString;
        window.open(url, '_blank');
    })
</script>