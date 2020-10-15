<?php


echo '	<div class="app-main"> ';
echo '	<div class="app-sidebar sidebar-shadow">';
echo '  <div class="app-header__logo">
             <div class="logo-src"></div>
             <div class="header__pane ml-auto">
             <div>';
echo'	<button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
            </button>';
echo'	</div>
			</div>
			</div>';
echo'	<div class="app-header__mobile-menu">
                        <div>
                            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>';
echo'	<div class="app-header__menu">
                        <span>
                            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                                <span class="btn-icon-wrapper">
                                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                                </span>
                            </button>
                        </span>
			</div>';   

echo'	<div class="scrollbar-sidebar">';
echo'	<div class="app-sidebar__inner">';
echo'	<ul class="vertical-nav-menu">';
echo'	<li class="app-sidebar__heading">Dashboard</li>';
echo'	<li>
                                    <a href="/index.php">
                                        <i class="metismenu-icon pe-7s-rocket"></i>
                                        Dashboard
                                    </a>
        </li>';
		
		echo'	<li class="app-sidebar__heading">Management</li>
                                <li class="mm-active">
							<a href="#">
                                        <i class="metismenu-icon pe-7s-diamond"></i>
                                        Products
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul
                                         class="mm-show"
										>';
echo'									<li>
                                            <a href="/products" class="mm-active">
                                                <i class="metismenu-icon"></i>
                                                Daftar Produk
                                            </a>
                                        </li>
										
										<li>
                                            <a href="/barcode" >
                                                <i class="metismenu-icon"></i>
                                                Cetak Barcode
                                            </a>
                                        </li>
										
                                        <li>';
										
										
                                           
echo'	</ul>
                                </li>';
								

								
echo'	<li>
								
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-car"></i>
                                        Penjualan
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul
        
                                        
                                    >
									
									 <li>
                                            <a href="/orders?request=create_orders">
                                                <i class="metismenu-icon">
                                                </i>Buat Pesanan
                                            </a>
                                        </li>
										
										
                                        <li>
                                            <a href="/orders">
                                                <i class="metismenu-icon">
                                                </i>Pesanan
                                            </a>
                                        </li>
										
										
                                       
									   
									   
                                       
                                    </ul>
                                </li>
                                
                                
                            </ul>
                        </div>
                    </div>
                </div>    <div class="app-main__outer">';



?>