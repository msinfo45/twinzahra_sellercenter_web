<?php

 if(session() ->get('HTTP_TOKEN') == "")
{
    header("Location: ".base_url('/'));

   exit;
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>ApiMart - Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('public/plugins/fontawesome-free/css/all.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url('public/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('public/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?= base_url('public/plugins/jqvmap/jqvmap.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('public/dist/css/adminlte.min.css') ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= base_url('public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= base_url('public/plugins/daterangepicker/daterangepicker.css') ?>">
  <!-- summernote -->
  <link rel="stylesheet" href="<?= base_url('public/plugins/summernote/summernote-bs4.css') ?>">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/placeholder-loading/dist/css/placeholder-loading.min.css">

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= base_url('public/assets/scripts/main.js') ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url('public/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<script src="<?= base_url('public/js/jquery-3.5.1.min.js') ?>"></script>
<script src="<?= base_url('public/js/sprinkle.js') ?>"></script>

<!-- Bootstrap 4 -->
<script src="<?= base_url('public/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- ChartJS -->
<script src="<?= base_url('public/plugins/chart.js/Chart.min.js') ?>"></script>
<!-- Sparkline -->

<!-- JQVMap -->
<script src="<?= base_url('public/plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?= base_url('public/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
<!-- daterangepicker -->
<script src="<?= base_url('public/plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('public/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url('public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<!-- Summernote -->
<script src="<?= base_url('public/plugins/summernote/summernote-bs4.min.js') ?>"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url('public/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('public/dist/js/adminlte.js') ?>"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->


</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>

    </ul>


    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url('marketplace/copy') ?>" class="nav-link">Salin Produk</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url('marketplace/sync') ?>" class="nav-link">Sinkronkan</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= base_url('v1/users?request=logout') ?>" class="nav-link">Logout</a>
        </li>
    </ul>
  </nav>
  <!-- /.navbar -->

<?= $this->include('navbar') ?>

<?= $this->renderSection('content') ?>

<!-- /.content-wrapper -->
<footer class="main-footer">
    <strong>Copyright &copy; 2014-2019 <a href="http://msinfo.co.id">CV. Multisolusi Informatika</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.0.2
    </div>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->


</body>
</html>
