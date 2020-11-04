<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

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
              <li class="breadcrumb-item"><a href="<?= base_url('home') ?>">Home</a></li>
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
          <a class="nav-link active" id="custom-tabs-products-database-tab" data-toggle="pill" href="#custom-tabs-products-database-content" role="tab" aria-controls="custom-tabs-products-database" aria-selected="true">Database</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="custom-tabs-products-lazada-tab" data-toggle="pill" href="#custom-tabs-products-lazada-content" role="tab" aria-controls="custom-tabs-products-lazada" aria-selected="false">Lazada</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="custom-tabs-products-shopee-tab" data-toggle="pill" href="#custom-tabs-products-shopee-content" role="tab" aria-controls="custom-tabs-products-shopee" aria-selected="false">Shopee</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="custom-tabs-products-tokopedia-tab" data-toggle="pill" href="#custom-tabs-products-tokopedia-content" role="tab" aria-controls="custom-tabs-proucts-tokopedia" aria-selected="false">Tokopedia</a>
        </li>
      </ul>
</div>

<div class="card-body">
  <div class="tab-content" id="custom-tabs-products-tabContent">
  <div class="tab-pane fade show active" id="custom-tabs-products-database-content" role="tabpanel" aria-labelledby="custom-tabs-products-database-tab">


<div class="d-flex">
      <div>
        
 
 <div class="input-group">
    <input class="form-control my-0 py-1 amber-border SearchProductDatabase" type="text" placeholder="search" aria-label="SearchProductDatabase" name="SearchProductDatabase">
    <div class="input-group-append">
    <span class="input-group-text amber lighten-3" id="basic-text1"><i class="fas fa-search text-grey"
        aria-hidden="true"></i></span>
    </div>

    

       <div class="col-auto">
                            <select class="form-control" name="search_size" id="search_size">
                            <option value="" disabled selected>Pilih Ukuran</option>
                            <option value="36">36</option>
                            <option value="37">37</option>
                            <option value="38">38</option>
                            <option value="39">39</option>
                            <option value="40">40</option>
                            <option value="41">41</option>
                            <option value="42">42</option>
                            <option value="43">43</option>
                            <option value="44">44</option>
                        </select>

                         </div>
    </div>

      

      </div>

  <div class="ml-auto">
     
  <div class="input-group">

<div class="col-auto">


<a data-toggle="modal"  title="Tambah Produk"  class="btn btn-primary" href="<?= base_url('products/add_product') ?>">Tambah Produk</a>    

</div>

<a   target="_blank" title="Sync Marketplace"  class="btn btn-primary" href="<?= base_url('v1/products?request=sync_marketplace') ?>">Sync Marketplace</a>    


</div>

      </div>
 </div>
</br>

                    <div id="ResultProductDatabase"></div>

                  </div>
                  <div class="tab-pane fade" id="custom-tabs-products-lazada-content" role="tabpanel" aria-labelledby="custom-tabs-products-lazada-tab">
                  <div id="ResultLazada"></div>

                  </div>
                  <div class="tab-pane fade" id="custom-tabs-products-shopee-content" role="tabpanel" aria-labelledby="custom-tabs-products-shopee-tab">
                     Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. 
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-products-tokopedia-content" role="tabpanel" aria-labelledby="custom-tabs-products-tokopedia-tab">
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
   



 <?= $this->include('products/modal/modal_sync_marketplace') ?>

  <script>

LoadProducts();

$(document).on("click", "#custom-tabs-products-database-tab", function () {
  //alert("database");
  LoadProducts();
 });
 
 $(document).on("click", "#custom-tabs-products-lazada-tab", function () {
 //alert("lazada");
  LoadProductLazada();
 });


    
    
 $(document).on("keypress", ".SearchProductDatabase", function(e){

        if(e.which == 13){
  
 
 var SearchProductDatabase = $('.SearchProductDatabase').val();
 
LoadProducts(SearchProductDatabase, "");

   }

});
  
 function LoadProducts(SearchProductDatabase , SearchSize){
  
    var displayProduct = 10;
    var Page = 1;
  $('#ResultProductDatabase').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      LoadProducts(displayProduct);
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
  
    function LoadProducts(limit){
    
       $.ajax({
     url:'<?= base_url('products/load_products') ?>',
        method:"POST",
    //data: '{"Search":"'+ Search +'","Page": Page}',
        data:{action: '<?= base_url('products/load_products') ?>', limit:limit , "Search":SearchProductDatabase,"SearchSize":SearchSize,"Page": Page},
        success:function(data) {
      $('#ResultProductDatabase').html(data); 
        }
      });
  

    }
    
}


  

//Funcion Pending Order
function LoadProductLazada(){

var displayProduct = 5;
  $('#ResultLazada').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      LoadProductLazada(displayProduct);
    }, 0);

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
  
    function LoadProductLazada(limit){
      $.ajax({
          url:'<?= base_url('products/load_products_lazada') ?>',
        method:"POST",
        data:{action: 'load_products_lazada', limit:limit},
        success:function(data) {
          $('#ResultLazada').html(data);
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

         url:'<?= base_url('v1/products?request=sync_marketplace') ?>',
            beforeSend: function () {
           $('.btn').attr("disabled","disabled");
              // $('.modal-body').css('opacity', '.5');
            },
            success:function(data){
        
        console.log(data.message);
        console.log(data.status);
        console.log(data.data);
        
                if(data.status == '200'){
           alert(data.message);
        $('#result_message').html('<span style="color:green;"></p>'+data.data);
          $('.btn').removeAttr("disabled");
          //$('.modal-body').css('opacity', '');
                }else{
           alert(data.message);
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
      alert("error");
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


 $(document).ready(function(){
 
            $('#search_size').change(function(){ 
                var id=$(this).val();
      
        LoadProducts("", id);
            }); 
             
        });


</script>




<?= $this->endSection() ?>

