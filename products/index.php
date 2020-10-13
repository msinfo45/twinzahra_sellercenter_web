
<?php

require '../include/head.php';
require '../include/slidebar.php';



include "../config/db_connection.php";

include "../config/lazada/LazopSdk.php";

$rows = array();
$rows2 = array();
$rowsLazada = array();

$current_app_version_code = "1"; //App Version Code
$current_app_version_name = "0.1.0"; //App Version Name

$token_header = ""; //Header Token
$version_code_header = ""; //Header Version Code
$version_name_header = ""; //Header Version Name
$version_name_header = ""; //Header Version Name
$userid_header = "";
$modeHeader = 1;


$url='https://api.lazada.co.id/rest';
	

//-------------------------------------- API Function Start Here ----------------------------------------//

//Check request content
$content = isset($_GET['request']);

if (isset($content) && $content != "") {

    //Load Models
    include "../config/model.php";

    $db = new Model_user();



    
				if ($content == "add_product") {
					$modeHeader = 0;
                  echo'  <div class="app-main__inner">
                        <div class="app-page-title">
                     
						<div class="main-card mb-3 card">
                            <div class="card-body">
                                <h5 class="card-title">Tambah Produk</h5>
                                <form class="needs-validation" novalidate action="./api/products.php?request=add_product" method="post"> ';
								
                                   echo' <div class="form-row">';
                                       echo' <div class="col-md-9 mb-3">
                                            <label for="validationCustom01">Nama Produk</label>
                                            <input type="text" class="form-control" id="validationCustom01" placeholder="Masukan nama produk" value="" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>';
										
                                        echo'<div class="col-md-9 mb-3">
                                            <label for="validationCustom02">Deskripsi</label>
                                            <input type="text" class="form-control" id="validationCustom02" placeholder="Deskripsi" value="" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>';
                                        
                                        echo'<div class="col-md-9 mb-3">
                                            <label for="validationCustom04">Kategori</label>
                                            <input type="text" class="form-control" id="validationCustom04" placeholder="Kategori" required>
                                            <div class="invalid-feedback">
                                                Please provide a valid state.
                                            </div>
                                        </div>';
										
                                        echo'<div class="col-md-9 mb-3">
                                            <label for="validationCustom05">Merk</label>
                                            <input type="text" class="form-control" id="validationCustom05" placeholder="Merk" required>
                                            <div class="invalid-feedback">
                                        
                                            </div>
                                        </div>';
									
									    echo'<div class="col-md-9 mb-3">
                                            <label for="validationCustom05">Variant</label>
                                            <input type="text" class="form-control" id="validationCustom05" placeholder="Variant" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>';
                                    
									    echo'<div class="col-md-6 mb-3">
                                            <label for="validationCustom05">Harga Dasar</label>
                                            <input type="text" class="form-control" id="validationCustom05" placeholder="Rp" required>
                                            <div class="invalid-feedback">
                                            
                                            </div>
                                        </div>';
									
									    echo'<div class="col-md-6 mb-3">
                                            <label for="validationCustom05">Harga Jual</label>
                                            <input type="text" class="form-control" id="validationCustom05" placeholder="Rp" required>
                                            <div class="invalid-feedback">
                                           
                                            </div>
                                        </div>';
									
									    echo'<div class="col-md-6 mb-3">
                                            <label for="validationCustom05">Stok</label>
                                            <input type="text" class="form-control" id="validationCustom05" placeholder="0" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>';
									
									    echo'<div class="col-md-6 mb-3">
                                            <label for="validationCustom05">Berat</label>
                                            <input type="text" class="form-control" id="validationCustom05" placeholder="Grm" required>
                                            <div class="invalid-feedback">
                                             
                                            </div>
                                        </div>';
									
									
									
                                    echo'<button class="btn btn-primary" type="submit">Submit form</button>
                                </form>';
          ?>
                               <script>
                                    // Example starter JavaScript for disabling form submissions if there are invalid fields
                                    (function() {
                                        'use strict';
                                        window.addEventListener('load', function() {
                                            // Fetch all the forms we want to apply custom Bootstrap validation styles to
                                            var forms = document.getElementsByClassName('needs-validation');
                                            // Loop over them and prevent submission
                                            var validation = Array.prototype.filter.call(forms, function(form) {
                                                form.addEventListener('submit', function(event) {
                                                    if (form.checkValidity() === false) {
                                                        event.preventDefault();
                                                        event.stopPropagation();
                                                    }
                                                    form.classList.add('was-validated');
                                                }, false);
                                            });
                                        }, false);
                                    })();
                               </script>
							   
							<?php
                            echo'</div>
                        </div>
                        
                    </div>';
                }
				
				
				
				
    // ---------------------------------------- API that need token below ------------------------------------------- //
    if ($modeHeader == 1) {
        //Check header token
        $token_header = $_SERVER['HTTP_TOKEN'];
        $userid_header = $_SERVER['HTTP_USER_ID'];
        $version_code_header = $_SERVER['HTTP_VERSION_CODE'];
        $version_name_header = $_SERVER['HTTP_VERSION_NAME'];
        $version_check = 1;
		
		
		
				
				

        // if($current_app_version_code['Value'] == $version_code_header){
        // 	$version_check = 1;
        // }

        // $data = [
        // 	"token_header" 				=> $token_header,
        // 	"userid_header" 		    => $userid_header,
        // 	"version_code_header" 		=> $version_code_header
        // ];

        // echo json_encode($data);
        $nurse_type_header = isset($_SERVER['HTTP_NURSE_TYPE']) ? $_SERVER['HTTP_NURSE_TYPE'] : 1;

        if (isset($token_header) && isset($userid_header) && $token_header != "" && $userid_header != "" && $version_check == 1) {

            $checkLoginGoogle = $db->getUserByID($userid_header);

            $loginGoogle = 0;
            if ($checkLoginGoogle) {
                $checkLoginGoogle = $checkLoginGoogle->fetch_assoc();
                if (isset($checkLoginGoogle['GoogleUserID'])) {
                    $loginGoogle = 1;
                }
            }

            if ($loginGoogle == 0) {
                $checkToken = $db->checkToken($token_header, $userid_header);
            } elseif ($loginGoogle == 1) {
                $checkToken = $db->checkToken2($token_header, $userid_header);
            }
            if ($checkToken) {
               
		
				
			
		


					if ($content == "update_stock") {
                    $post = json_decode(file_get_contents("php://input"), true);
					
			
				
				
//                    $user_id = $userid_header;
                    $user_id = $post['UserID'];
					$product_variant_name = $post['ProductVariantName'];
					$product_variant_detail_name = $post['ProductVariantDetailName'];
					$product_name = $post['ProductName'];
					$sku_id = $post['SkuID'];
					$barcode = $post['Barcode'];
					$unit = $post['Unit'];
					$stock_system = $post['StockSystem'];
					$stock_fisik = $post['StockFisik'];
					$selisih = $post['Selisih'];
					$product_variant_id = $post['ProductVariantID'];
					$reason = $post['Reason'];

                    if (isset($user_id)&& isset($sku_id) ) {

						$addDataStockOpname = $db->insertStockOpname($user_id, $product_variant_name ,$product_variant_detail_name , $product_name  ,
						$sku_id ,$barcode ,$unit ,$stock_system ,$stock_fisik ,$selisih , $reason);
						
						$updateDataProduct = $db->updateStockProduct($user_id, $sku_id ,$stock_fisik , $product_variant_id);
							
                        if ($updateDataProduct != null) {
							
                      


                            $return = array(
                                "status" => 200,
                                "message" => "Update Data Berhasil",
                                "total_rows" => 1,
                                "data" => []
                            );
                        } else {
                            $return = array(
                                "status" => 200,
								"total_rows" => 0,
                                "message" => "Update data gagal",
								"data" => []
                            );
                        }
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Oops sepertinya ada yang salah!"
                        );
                    }

                    echo json_encode($return);
                }
				
				


            } else {
                //Token not match !!!
                $return = array(
                    "status" => 406,
                    "message" => "Anda sudah login di device lain!"
                );

                echo json_encode($return);
            }
        } elseif ($version_check == 0) {
            $return = array(
                "status" => 407,
                "force_update" => $force_update,
                "message" => "Versi app terbaru sudah ada di playstore, harap update app terbaru !"
            );

            echo json_encode($return);
        } else {
            $return = array(
                "status" => 406,
                "message" => "Oops sesi anda sudah habis!"
            );

            echo json_encode($return);
        }
    }

} else {
	
	
	?>
	
	    <div class="app-main__inner">
                           
						
						     <div class="mb-3 card">
                              <div class="card-body">
											
                              <ul class="tabs-animated-shadow nav-justified tabs-animated nav">
                                             
                            <li class="nav-item">
                                       <a role="tab" class="nav-link active" id="tab-database" data-toggle="tab" href="#database">
                                    <span>Database</span>
                                </a>
                            </li>
							
							  <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-lazada" data-toggle="tab" href="#lazada">
                                    <span>Lazada</span>
                                </a>
                            </li>
							
							  <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-shopee" data-toggle="tab" href="#shopee">
                                    <span>Shopee</span>
                                </a>
                            </li>
							
							  <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-tokopedia" data-toggle="tab" href="#tokopedia">
                                    <span>Tokopedia</span>
                                </a>
                            </li>
							
							
							  <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-bukalapak" data-toggle="tab" href="#bukalapak">
                                    <span>Bukalapak</span>
                                </a>
                            </li>
							
							
							           </ul>
									   
        <div class="tab-content">
                                                 												
        <div class="tab-pane show active" id="database"  role="tabpanel">
							
		
		
		<div class="mb-3">
		 <div class="card-body">
		 
		<div class="panel-left">	
		
		<div class="input-group md-form form-sm form-2 pl-0">
		<input class="form-control my-0 py-1 amber-border SearchProductDatabase" type="text" placeholder="search" aria-label="SearchProductDatabase" name="SearchProductDatabase">
		<div class="input-group-append">
		<span class="input-group-text amber lighten-3" id="basic-text1"><i class="fas fa-search text-grey"
        aria-hidden="true"></i></span>
		</div>
		</div>
		
		</div>
		

		<div class="panel-right">
		
		<div class="col-xs-6">
						<a href="./products.php?request=add_product" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Tambah Produk</span></a>	
						<a data-toggle="modal"  title="Sync Marketplace"  class="SyncMarketplace btn btn-primary" href="#SyncMarketplace">Sync Marketplace</a>		
					
											
		</div>
		
		</div>
		
						
		</div>
		</div>
		
		

	<div class="card-body" id="ResultProductDatabase"></div>
	
													
    </div>
			
      <div class="tab-pane" id="lazada" role="tabpanel">
		<div class="mb-3">
		 <div class="card-body">
		 
		<div class="panel-left">	
		
		<div class="input-group md-form form-sm form-2 pl-0">
		<input class="form-control my-0 py-1 amber-border SearchProductLazada" type="text" placeholder="search" aria-label="SearchProductLazada" name="SearchProductLazada">
		<div class="input-group-append">
		<span class="input-group-text amber lighten-3" id="basic-text1"><i class="fas fa-search text-grey"
        aria-hidden="true"></i></span>
		</div>
		</div>
		
		</div>
		

		<div class="panel-right">
		
		<div class="col-xs-6">
						<a href="./products.php?request=add_product" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Tambah Produk</span></a>	
						<a data-toggle="modal"  title="Sync Marketplace"  class="SyncMarketplace btn btn-primary" href="#SyncMarketplace">Sync Marketplace</a>		
					
											
		</div>
		
		</div>
		
						
		</div>
		</div>
		
		

	<div class="card-body" id="ResultProductLazada"></div>
	
					
													</div>
														

													 <div class="tab-pane" id="shopee" role="tabpanel">
                                                           tab 5
                                                    </div>
													 <div class="tab-pane" id="tokopedia" role="tabpanel">
                                                           tab 6 
                                                    </div>
													 <div class="tab-pane" id="bukalapak" role="tabpanel">
                                                           tab 7 
                                                    </div>
													
													
                                                </div>
                                            </div>
                                        </div>
										
                           
							
					
						
						
						
                       
                
     
   </div>
   
   <?php
   
	

}
 
?> 
  <script>

  
  $(document).on("click", "#tab-database", function () {
	
	loadClassProductDatabase();
});

$(document).on("click", "#tab-lazada", function () {
	
	loadClassProductLazada();
});


	  
	  
 $(document).on("keypress", ".SearchProductDatabase", function(e){

        if(e.which == 13){
  
 
 var SearchProductDatabase = $('.SearchProductDatabase').val();
 
loadClassProductDatabase(SearchProductDatabase);

   }

    });
	
 function loadClassProductDatabase(SearchProductDatabase){
	
		var displayProduct = 5;
		var Page = 1;
	$('#ResultProductDatabase').html(createSkeleton(displayProduct));
	
    setTimeout(function(){
      loadProductsDatabase(displayProduct);
    }, 100);

    function createSkeleton(limit){
      var skeletonHTML = '';
      for(var i = 0; i < limit; i++){
        skeletonHTML += '<div class="ph-item">';
        skeletonHTML += '<div class="ph-col-4">';
        skeletonHTML += '<div class="ph-picture"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '<div>';
        skeletonHTML += '<div class="ph-row">';
        skeletonHTML += '<div class="ph-col-12 big"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
      }
      return skeletonHTML;
    }
	
    function loadProductsDatabase(limit){
		
		   $.ajax({
     url:'https://sellercenter.twinzahra.com/class/items_product.php',
        method:"POST",
		//data: '{"Search":"'+ Search +'","Page": Page}',
        data:{action: 'load_products', limit:limit , "Search":SearchProductDatabase,"Page": Page},
        success:function(data) {
      $('#ResultProductDatabase').html(data); 
        }
      });
	

    }
		
}

$(document).on("keypress", ".SearchProductLazada", function(e){

        if(e.which == 13){
  
 
 var SearchProductLazada = $('.SearchProductLazada').val();
 
loadClassProductLazada(SearchProductLazada);

   }

    });
	
 function loadClassProductLazada(SearchProductLazada){
	
		var displayProduct = 5;
		var Page = 1;
	$('#ResultProductLazada').html(createSkeleton(displayProduct));
	
    setTimeout(function(){
      loadProductsLazada(displayProduct);
    }, 100);

    function createSkeleton(limit){
      var skeletonHTML = '';
      for(var i = 0; i < limit; i++){
        skeletonHTML += '<div class="ph-item">';
        skeletonHTML += '<div class="ph-col-4">';
        skeletonHTML += '<div class="ph-picture"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '<div>';
        skeletonHTML += '<div class="ph-row">';
        skeletonHTML += '<div class="ph-col-12 big"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '<div class="ph-col-12"></div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
        skeletonHTML += '</div>';
      }
      return skeletonHTML;
    }
	
    function loadProductsLazada(limit){
		
		   $.ajax({
     url:'http://sellercenter.twinzahra.com/class/item_products_lazada.php',
        method:"POST",
		//data: '{"Search":"'+ Search +'","Page": Page}',
        data:{action: 'load_products', limit:limit , "Search":SearchProductLazada,"Page": Page},
        success:function(data) {
      $('#ResultProductLazada').html(data); 
        }
      });
	

    }
		
}



loadClassProductDatabase();


$(document).on("click", ".SyncMarketplace", function () {
     var order_id = $(this).data('id');
     //$(".modal-body #order_id").val( order_id );

     // As pointed out in comments, 
     // it is unnecessary to have to manually call the modal.
      $('#SyncMarketplace').modal('show');
	  
	event.preventDefault();
  
	
	   $.ajax({
        type: 'POST',
		dataType: 'json',
		contentType: 'application/json',
		processData: false,
		data: '{"user_id": "5"}',
        url:'http://sellercenter.twinzahra.com/api/lazada.php?request=sync_marketplace',
           
            beforeSend: function () {
           $('.btn').attr("disabled","disabled");
              // $('.modal-body').css('opacity', '.5');
            },
            success:function(data){
				
				console.log(data.message);
				console.log(data.status);
				console.log(data.data);
				
                if(data.status == '200'){
					
				$('#result_message').html('<span style="color:green;"></p>'+data.data);
					$('.btn').removeAttr("disabled");
					//$('.modal-body').css('opacity', '');
                }else{
					
					$('#result_message').html('<span style="color:red;"></p>'+data.data);
					$('.btn').removeAttr("disabled");
					//$('.modal-body').css('opacity', '');
                }
				
               
				
	  var percentage = 0;
      var timer = setInterval(function(){
       percentage = percentage + 20;
       progress_bar_process(percentage, timer);
      }, 1000);
                
            },
			error: function(){
			alert("Cannot get data");
			  $('.btn').removeAttr("disabled");
				//$('.modal-body').css('opacity', '');
			}
			
        });
		
   
  
				
});



  
  
  function progress_bar_process(percentage, timer)
  {
   $('.progress-bar').css('width', percentage + '%');
   if(percentage > 100)
   {
    clearInterval(timer);
   // $('.SyncMarketplace')[0].reset();
    $('.modal-body2 #process').css('display', 'none');
    $('.modal-body2 .progress-bar').css('width', '0%');
    //$('#save').attr('disabled', false);
    $('.modal-body2 #success_message').html("<div class='alert alert-success'>Berhasil</div>");
    setTimeout(function(){
     $('.modal-body2 #success_message').html('');
    }, 5000);
   }
  }



	 

</script>

<?php
  
require '../include/footer.php';

require '../include/modal/index.php';
?>
