<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php

//require '../include/head.php';
//require '../include/slidebar.php';



//include "../config/db_connection.php";

//include "../config/lazada/LazopSdk.php";

  ?>
  <div class="content-wrapper">
                           
              <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>
              Products
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Products</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
                 <div class="content">
              
                     <div class="container-fluid">
 

            <div class="card card-primary card-outline card-outline-tabs">
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-products-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-products-database-tab" data-toggle="pill" href="#custom-tabs-products-database" role="tab" aria-controls="custom-tabs-products-database" aria-selected="true">Database</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-products-lazada-tab" data-toggle="pill" href="#custom-tabs-products-lazada" role="tab" aria-controls="custom-tabs-products-lazada" aria-selected="false">Lazada</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-products-shopee-tab" data-toggle="pill" href="#custom-tabs-products-shopee" role="tab" aria-controls="custom-tabs-products-shopee" aria-selected="false">Shopee</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-products-tokopedia-tab" data-toggle="pill" href="#custom-tabs-products-tokopedia" role="tab" aria-controls="custom-tabs-proucts-tokopedia" aria-selected="false">Tokopedia</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-products-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-products-database" role="tabpanel" aria-labelledby="custom-tabs-products-database-tab">


 <div class="input-group md-form form-sm form-2 pl-0">
    <input class="form-control my-0 py-1 amber-border SearchProductDatabase" type="text" placeholder="search" aria-label="SearchProductDatabase" name="SearchProductDatabase">
    <div class="input-group-append">
    <span class="input-group-text amber lighten-3" id="basic-text1"><i class="fas fa-search text-grey"
        aria-hidden="true"></i></span>
    </div>
    </div>

                    <div id="ResultProductDatabase"></div>

                  </div>
                  <div class="tab-pane fade" id="custom-tabs-products-lazada" role="tabpanel" aria-labelledby="custom-tabs-products-lazada-tab">
                     Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam. 
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-products-shopee" role="tabpanel" aria-labelledby="custom-tabs-products-shopee-tab">
                     Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. 
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-products-tokopedia" role="tabpanel" aria-labelledby="custom-tabs-products-tokopedia-tab">
                     Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis. 
                  </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>

                    
   </div>
   </div>    
   </div>
   
  <script>

  LoadViewProducts();

  $(document).on("click", "#tab-database", function () {
  
  LoadViewProducts();
});

$(document).on("click", "#tab-lazada", function () {
  
  loadClassProductLazada();
});


    
    
 $(document).on("keypress", ".SearchProductDatabase", function(e){

        if(e.which == 13){
  
 
 var SearchProductDatabase = $('.SearchProductDatabase').val();
 
LoadViewProducts(SearchProductDatabase);

   }

    });
  
 function LoadViewProducts(SearchProductDatabase){
  
    var displayProduct = 5;
    var Page = 1;
  $('#ResultProductDatabase').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      loadProducts(displayProduct);
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
  
    function loadProducts(limit){
    
       $.ajax({
     url:'<?= base_url('products/load_products') ?>',
        method:"POST",
    //data: '{"Search":"'+ Search +'","Page": Page}',
        data:{action: '<?= base_url('products/load_products') ?>', limit:limit , "Search":SearchProductDatabase,"Page": Page},
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
      url:'http://localhost/twinzahra/public/include/views/load_products.php',
        method:"POST",
    //data: '{"Search":"'+ Search +'","Page": Page}',
        data:{action: 'load_products', limit:limit , "Search":SearchProductLazada,"Page": Page},
        success:function(data) {
      $('#ResultProductLazada').html(data); 
        }
      });
  

    }
    
}






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
  
//require '../include/footer.php';

//require '../include/modal/index.php';
?>
<?= $this->endSection() ?>