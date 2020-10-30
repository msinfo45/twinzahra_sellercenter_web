<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

  <div class="content-wrapper">
                           
              <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>
              Pesanan
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url('home') ?>">Home</a></li>
              <li class="breadcrumb-item active">Pesanan</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
                 <div class="content">
              
                     <div class="container-fluid">
 

            <div class="card card-primary card-outline card-outline-tabs">
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-orders-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-orders-pending-tab" data-toggle="pill" href="#custom-tabs-orders-pending-content" role="tab" aria-controls="custom-tabs-orders-pending-content" aria-selected="true" >Pesanan Baru</a>
                  </li>
                  <li class="nav-item">

                    <a class="nav-link" id="custom-tabs-orders-readytoship-tab" data-toggle="pill" href="#custom-tabs-orders-readytoship-content" role="tab" aria-controls="custom-tabs-orders-readytoship-content" aria-selected="false" >Siap Kirim</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-orders-ship-tab" data-toggle="pill" href="#custom-tabs-orders-ship-content" role="tab" aria-controls="custom-tabs-orders-ship-content" aria-selected="false" >Dalam Pengiriman</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-orders-done-tab" data-toggle="pill" href="#custom-tabs-orders-done-content" role="tab" aria-controls="custom-tabs-orders-done-content" aria-selected="false" >Selesai</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-orders-filed-tab" data-toggle="pill" href="#custom-tabs-orders-filed-content" role="tab" aria-controls="custom-tabs-orders-filed-content" aria-selected="false" >Gagal</a>
                  </li>
                </ul>
              </div>


            
                <div class="tab-content" id="custom-tabs-orders-tabContent">

                  <div class="tab-pane fade show active" id="custom-tabs-orders-pending-content" role="tabpanel" aria-labelledby="custom-tabs-orders-pending-tab">

 
                <div id="ResultPending"></div> 

                  </div>

                  <div class="tab-pane fade" id="custom-tabs-orders-readytoship-content" role="tabpanel" aria-labelledby="custom-tabs-orders-readytoship-tab">


                  <div id="ResultReadToShip"></div> 

                  </div>

                  <div class="tab-pane fade" id="custom-tabs-orders-ship-content" role="tabpanel" aria-labelledby="custom-tabs-orders-ship-tab">

             ship

                  </div>

                  <div class="tab-pane fade" id="custom-tabs-orders-done-content" role="tabpanel" aria-labelledby="custom-tabs-orders-done-tab">
                 
                    <div id="ResultDone"></div> 

                  </div>

                     <div class="tab-pane fade" id="custom-tabs-orders-filed-content" role="tabpanel" aria-labelledby="custom-tabs-orders-filed-tab">
                    
                    <div id="ResultFiled"></div> 


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

//Funcion Pending Order
function loadPending(){

var displayProduct = 5;
  $('#ResultPending').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      loadPending(displayProduct);
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
  
    function loadPending(limit){
      $.ajax({
          url:'<?= base_url('orders/load_pending') ?>',
        method:"POST",
        data:{action: 'load_pending', limit:limit},
        success:function(data) {
          $('#ResultPending').html(data);
        }
      });
    }
}
///

//Funcion Pending Order
function loadRTS(){

var displayProduct = 5;
  $('#ResultReadToShip').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      loadRTS(displayProduct);
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
  
    function loadRTS(limit){
      $.ajax({
          url:'<?= base_url('orders/load_rts') ?>',
        method:"POST",
        data:{action: 'load_rts', limit:limit},
        success:function(data) {
          $('#ResultReadToShip').html(data);
        }
      });
    }
}
///

//Default load
loadPending();
 //

$(document).on("click", "#custom-tabs-orders-pending-tab", function () {
  
 loadPending();
});

$(document).on("click", "#custom-tabs-orders-readytoship-tab", function () {

 loadRTS();
});

$(document).on("click", "#custom-tabs-orders-ship-tab", function () {

 //loadRTS();
});

$(document).on("click", "#custom-tabs-orders-done-tab", function () {
 
 //loadRTS();
});

$(document).on("click", "#custom-tabs-orders-filed-tab", function () {
  
 //loadRTS();
});



</script>




<?= $this->endSection() ?>

