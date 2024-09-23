<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>


    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>

<body>

    <div class="col col-3" style="width: 100%;">
        <!-- <div id="daterange" class="float-start" style="background: #fff; cursor:pointer; padding: 5px 10px; border:1px solid #ccc; width:100%; text-align:center ">
            <span></span>
        </div> -->
        <input type="text" placeholder="Select Date" id="dateRange" name="datetimes" style="background: #fff; cursor: pointer; border: 1px solid #ccc; width: 100%;padding: 12px 15px;
    font-size: 1rem;
    border-radius: 10px;">


    </div>
    @section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        $(function() {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                document.getElementById('dateRange').value = start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY');
                document.getElementById('dateRange').placeholder = "Select Date";

            }

            $('input[name="datetimes"]').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });
    </script>
    @endsection

</body>

</html>