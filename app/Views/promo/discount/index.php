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
                <ul class="nav nav-tabs" id="custom-tabs-discount-tab" role="tablist">

                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-discount-all-tab" 
                    data-toggle="pill" href="#custom-tabs-discount-all-content" role="tab" 
                    aria-controls="custom-tabs-discount-all-content" aria-selected="true" >Semua</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-discount-ongoing-tab"
                     data-toggle="pill" href="#custom-tabs-discount-ongoing-content" role="tab" 
                     aria-controls="custom-tabs-discount-ongoing-content" aria-selected="false" >Akan Datang</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-discount-going-tab"
                     data-toggle="pill" href="#custom-tabs-discount-going-content" role="tab" 
                     aria-controls="custom-tabs-discount-going-content" aria-selected="false" >Berjalan</a>
                  </li>

                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-discount-done-tab"
                     data-toggle="pill" href="#custom-tabs-discount-done-content" role="tab" 
                     aria-controls="custom-tabs-discount-done-content" aria-selected="false" >Selesai</a>
                  </li>
                  
                </ul>
              </div>


            
                <div class="tab-content" id="custom-tabs-discount-tabContent">

                <div class="tab-pane fade show active" id="custom-tabs-discount-all-content"
                 role="tabpanel" aria-labelledby="custom-tabs-discount-all-tab">

 
                <div id="ResultAll"></div> 

                </div>

                  <div class="tab-pane fade" id="custom-tabs-discount-ongoing-content" 
                  role="tabpanel" aria-labelledby="custom-tabs-discount-ongoing-tab">


                  <div id="ResultOnGoing"></div> 

                  </div>

                  <div class="tab-pane fade" id="custom-tabs-discount-going-content"
                   role="tabpanel" aria-labelledby="custom-tabs-discount-going-tab">

                  <div id="ResultGoing"></div> 

                  </div>

                  <div class="tab-pane fade" id="custom-tabs-discount-done-content"
                   role="tabpanel" aria-labelledby="custom-tabs-discount-done-tab">
                 
                    <div id="ResultDone"></div> 

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
function LoadAll(){

var display = 5;
  $('#ResultAll').html(createSkeleton(display));
  
    setTimeout(function(){
      LoadAll(display);
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
  
    function LoadAll(limit){
      $.ajax({
          url:'<?= base_url('promo/discount/load_all') ?>',
        method:"POST",
        data:{action: 'load_all', limit:limit},
        success:function(data) {
          $('#ResultAll').html(data);
        }
      });
    }
}
///

//Funcion Pending Order
function LoadOnGoing(){

var display = 5;
  $('#ResultOnGoing').html(createSkeleton(display));
  
    setTimeout(function(){
        LoadOnGoing(display);
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
  
    function LoadOnGoing(limit){
      $.ajax({
          url:'<?= base_url('promo/discount/load_ongoing') ?>',
        method:"POST",
        data:{action: 'load_ongoing', limit:limit},
        success:function(data) {
          $('#ResultOnGoing').html(data);
        }
      });
    }
}
///

function LoadGoing(){

var display = 5;
  $('#ResultGoing').html(createSkeleton(display));
  
    setTimeout(function(){
      LoadGoing(display);
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
  
    function LoadGoing(limit){
      $.ajax({
          url:'<?= base_url('promo/discount/load_going') ?>',
        method:"POST",
        data:{action: 'load_going', limit:limit},
        success:function(data) {
          $('#ResultGoing').html(data);
        }
      });
    }
}

function LoadDone(){

var display = 5;
  $('#ResultDone').html(createSkeleton(display));
  
    setTimeout(function(){
      LoadDone(display);
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
  
    function LoadDone(limit){
      $.ajax({
          url:'<?= base_url('promo/discount/load_done') ?>',
        method:"POST",
        data:{action: 'load_done', limit:limit},
        success:function(data) {
          $('#ResultDone').html(data);
        }
      });
    }
}



//Default load
//LoadAll();
 //

$(document).on("click", "#custom-tabs-discount-all-tab", function () {
  
    LoadAll();
});

$(document).on("click", "#custom-tabs-discount-ongoing-tab", function () {

 LoadOnGoing();
});

$(document).on("click", "#custom-tabs-discount-going-tab", function () {

LoadGoing();
});

$(document).on("click", "#custom-tabs-discount-done-tab", function () {
 
LoadDone();
});




</script>




<?= $this->endSection() ?>

