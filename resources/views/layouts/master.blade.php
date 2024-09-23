<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Digital HR Complete HR Attendance System">
    <meta name="author" content="Digital HR">
    <meta name="keywords" content="Digital HR">

    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    @include('admin.section.head_links')
    @yield('styles')
</head>

<body>
    <div id="preloader">
        @include('admin.section.preloader')
    </div>

    <div class="main-wrapper">
        @include('admin.section.sidebar')
        <div class="page-wrapper">
            @include('admin.section.nav')

            <div class="page-content">
                @include('admin.section.page_header')
                @yield('main-content')
            </div>
            <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body"></div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="commonModalOver" tabindex="-1" role="dialog"
                aria-labelledby="commonModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="commonModalLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        </div>
                    </div>
                </div>
            </div>
            <!-- partial -->
            @include('admin.section.footer')
        </div>
    </div>

    @include('admin.section.body_links')
    @include('layouts.nav_notification_scripts')
    @include('layouts.nav_search_scripts')
    @include('layouts.theme_scripts')
    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.3/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
    @yield('scripts')
    <script>
        $(document).ready(function() {
            $('.deleteBtn').click(function(event) {
                event.preventDefault();
                let href = $(this).data('href');
                console.log(href);

                Swal.fire({
                    title: 'Are you sure you want to Delete ?',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    padding: '10px 50px 10px 50px',
                    // width:'1000px',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                })
            });
        });
    </script>
    @yield('attendanceScripts')
</body>
</html>
