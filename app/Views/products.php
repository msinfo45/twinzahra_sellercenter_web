

<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php
   #include "../config/db_connection.php";
   #include "../config/lazada/LazopSdk.php";
   ?>
<div class="content-wrapper">
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
                           <a href="./products/add" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Tambah Produk</span></a>   
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
      url:"<?= base_url('public/include/classes/items_product.php') ?>",
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
      url:"<?= base_url('public/include/classes/item_products_lazada.php') ?>",
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
         url:'/api/lazada.php?request=sync_marketplace',
            
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
   #require '../include/modal/index.php';
   ?>
<?= $this->endSection() ?>

