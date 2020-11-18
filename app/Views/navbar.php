
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url('home') ?>" class="brand-link">
        <img src="<?= base_url('public/dist/img/AdminLTELogo.png') ?>" alt="Twinzahrashop" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">Twinzahrashop</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url('public/dist/img/user2-160x160.jpg') ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= session() ->get('FirstName') . " " . session() ->get('LastName') ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->

                <li class="nav-item has-treeview menu-open">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Pembelian
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">


                    <li class="nav-item">
                            <a href="./index2.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tambah Produk</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= site_url('products') ?>" class="nav-link active">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Produk</p>
                        
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="./index3.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stock Opname</p>
                            </a>
                        </li>

                    </ul>
                </li>


                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>
                          Penjualan
                            <i class="fas fa-angle-left right"></i>
                            <span class="badge badge-info right">100</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= site_url('kasir') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kasir</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('orders') ?>" class="nav-link">                        
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pesanan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/layout/boxed.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cek Produk</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="pages/layout/fixed-sidebar.html" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cek Ongkir</p>
                            </a>
                        </li>                    
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>
                            Promosi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                        <a href="<?= site_url('promo/discount') ?>" class="nav-link">      
                                <i class="far fa-circle nav-icon"></i>
                                <p>Promo Toko</p>
                            </a>
                        </li>
                        <li class="nav-item">
                        <a href="<?= site_url('promo/combo') ?>" class="nav-link">      
                                <i class="far fa-circle nav-icon"></i>
                                <p>Flexi Combo</p>
                            </a>
                        </li>
                    
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>
                            Marketplace
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= site_url('marketplace/add') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tambah Toko</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('marketplace') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>List Toko</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= site_url('marketplace/settings') ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengaturan</p>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
