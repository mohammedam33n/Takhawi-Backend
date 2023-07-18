<!-- start header -->
@include('backend.layouts.head')
<!-- end header -->

<!-- Navbar -->
@include('backend.layouts.navbar')
<!-- /.navbar -->

<!-- Main Sidebar Container -->
@include('backend.layouts.sidbar')
<!-- End Sidebar Container -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@yield('title')</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    @yield('content')
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Main Footer -->
@include('backend.layouts.footer')
<!-- End Footer -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- Main FOOT -->
@include('backend.layouts.foot')
<!-- End FOOT -->
