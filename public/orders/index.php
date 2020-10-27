<?php

require '../include/head.php';
require '../include/slidebar.php';
require_once "../config/config_type.php";
//Check request content
$content = $_GET['request'];

if (isset($content) && $content != "") {
  
 if ($content == "create_orders") {

echo '<div class="card-body" id="ResultNewOrders"></div> ';

?>
<script>
var displayProduct = 5;
  $('#ResultNewOrders').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      loadOrderPending(displayProduct);
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
  
    function loadOrderPending(limit){
      $.ajax({
         url:"http://localhost/include/classes/create_order.php",
        method:"POST",
        data:{action: 'load_products', limit:limit},
        success:function(data) {
          $('#ResultNewOrders').html(data);
        }
      });
    }
  
  </script>
  
<?php

} 

} else {
    


?>
        
                    <div class="app-main__inner">
                           
            
                 <div class="mb-3 card">
                              <div class="card-body">
                      
                              <ul class="tabs-animated-shadow nav-justified tabs-animated nav">
                                             
                            <li class="nav-item">
                                       <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                                    <span>Pesanan Baru</span>
                                </a>
                            </li>
              
                <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
                                    <span>Siap Kirim</span>
                                </a>
                            </li>
              
                <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2">
                                    <span>Dalam Pengiriman</span>
                                </a>
                            </li>
              
                <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3">
                                    <span>Selesai</span>
                                </a>
                            </li>
              
              
                <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4">
                                    <span>Gagal</span>
                                </a>
                            </li>
              
              
                <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5">
                                    <span>Dibatalkan</span>
                                </a>
                
                            </li>
                                                </ul>
                                                <div class="tab-content">
                                                 
                          

                                                   <div class="tab-pane show active" id="tab-content-0"  role="tabpanel">
              
                          <div class="card-body" id="ResultPending"></div> 
                          
                                                    </div>
      
                                                   <div class="tab-pane" id="tab-content-1" role="tabpanel">
                            <div class="col-md-3">
                           <label >Kirim Masal</label>
                            <input type="text" class="form-control" name="OrderID" id="OrderID" placeholder="Masukan No Pesanan " required>
                          </div>
                          <div class="card-body" id="ResultRts"></div> 
          
                          </div>
                            

                           <div class="tab-pane" id="tab-content-2" role="tabpanel">
                                                           tab 5
                                                    </div>
                           <div class="tab-pane" id="tab-content-3" role="tabpanel">
                                                           tab 6 
                                                    </div>
                           <div class="tab-pane" id="tab-content-4" role="tabpanel">
                                                           tab 7 
                                                    </div>
                           <div class="tab-pane" id="tab-content-5" role="tabpanel">
                                                           tab 8
                                                    </div>
                          
                                                </div>
                                            </div>
                                        </div>
                    
                           
              
          
            
            
            
                       
                
     
   </div>
   
   
  </div></div></div>';
  
  

  
<script>

function loadRTS(){
  
    var displayProduct = 5;
  $('#ResultRts').html(createSkeleton(displayProduct));
  
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
         url:"http://localhost/include/classes/order_rts.php",
        method:"POST",
        data:{action: 'load_products', limit:limit},
        success:function(data) {
          $('#ResultRts').html(data);
        }
      });
    }
  
  
}



var displayProduct = 5;
  $('#ResultPending').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      loadOrderPending(displayProduct);
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
  
    function loadOrderPending(limit){
      $.ajax({
         url:"http://localhost/include/classes/order_pending.php",
        method:"POST",
        data:{action: 'load_products', limit:limit},
        success:function(data) {
          $('#ResultPending').html(data);
        }
      });
    }

$(document).on("click", "#tab-0", function () {
  
  var displayProduct = 5;
  $('#ResultPending').html(createSkeleton(displayProduct));
  
    setTimeout(function(){
      loadOrderPending(displayProduct);
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
  
    function loadOrderPending(limit){
      $.ajax({
         url:"http://localhost/include/classes/order_pending.php",
        method:"POST",
        data:{action: 'load_products', limit:limit},
        success:function(data) {
          $('#ResultPending').html(data);
        }
      });
    }
});

$(document).on("click", "#tab-1", function () {
  
 loadRTS();
});


function sound_error() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', '/assets/music/beep_error.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
    }
  
     function sound_add() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', '/assets/music/beep_add.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
    }
  
   $(document).on("keypress", "#OrderID", function(e){

        if(e.which == 13){
  
 
 var OrderID = $('#OrderID').val();
 $.ajax({
        type: 'POST',
    dataType: 'json',
    contentType: 'application/json',
    processData: false,
    data: '{"UserID": "5", "order_id": "'+OrderID+'"}',
        url: 'http://localhost/api/orders.php?request=set_ship',
           
            beforeSend: function () {
               // $('.submitBtn').attr("disabled","disabled");
               // $('.modal-body').css('opacity', '.5');
            },
            success:function(data){
        
        console.log(data.message);
        console.log(data.status);
        
                if(data.status == '200'){
           $('#OrderID').val('');
           
                    //$('#shipping_provider').val('');
                    //$('#delivery_type').val('');
          
                    //$('.statusMsg').html('<span style="color:green;"></p>' +data.message );
          //alert(data.message);
          // window.location.href = '/orders'; 
          sound_add();
           loadRTS();
      
          
                }else{
          sound_error();
          //$('.statusMsg').html('<span style="color:red;"></p>'+data.message);
          alert(data.message);
          // window.location.href = '/orders'; 
                }
                //$('.submitBtn').removeAttr("disabled");
                //$('.modal-body').css('opacity', '');
        
        
                
            },
      error: function(){
      alert("Cannot get data");
      }
      
        });

   }

    });
  

</script>
  
</body>
</html>

<!-- Modal -->
<div class="modal fade" id="AcceptOrder" tabindex="-1" role="dialog" aria-labelledby="AcceptOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AcceptOrder">Atur Pengiriman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
          $chAC = curl_init();
          curl_setopt($chAC, CURLOPT_URL, 'http://localhost/api/lazada.php?request=get_shipment_providers');
          curl_setopt( $chAC, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($chAC, CURLOPT_RETURNTRANSFER, 1);
          $contentAC = curl_exec($chAC);
          curl_close($chAC);

          //mengubah data json menjadi data array asosiatif
          $resultShipment = json_decode($contentAC,true);
          
           
                      
              echo'<p class="statusMsg"></p><input type="hidden"  name="order_id" id="order_id">
              <input type="hidden"  name="merchant_name" id="merchant_name">';
              
              echo' <div class="col-md-9 mb-3">';
            
            
                      echo '<select class="form-control" name="shipping_providers" id="shipping_providers">
                      
                      <option value="">Pilih Jasa Pengiriman</option>';
                      
                        //foreach($resultShipment as $dataShipment)
                        //{
                    
                          
                          
                      //echo '<option value='.$dataShipment['name'].'>'.$dataShipment['name'].'</option>';
                      echo '<option value="JNE MP">JNE MP</option>  ';
                      echo '<option value="LEX ID">LEX ID</option>  ';
                      echo '<option value="Ninja Van MP">Ninja Van MP</option>  ';
                        //}                     
                      echo'</select>';
                  
                                   
                                        echo'</div>';
                    
                    
                    echo'<div class="col-md-9 mb-3">';
 
                      echo '<select class="form-control" name="delivery_type" id="delivery_type">
                      
                      <option value="">Metode Pengiriman</option>';

                      echo '<option value="dropship">dropship</option>  ';
                                          
                      echo'</select>';
                  
                                   
                                        echo'</div>';
                    
                       ?> 
                    
                


                
            </div>
            <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary "  onclick="SendAcceptOrders()">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="EditOrder" tabindex="-1" role="dialog" aria-labelledby="EditOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditOrder">Ubah Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" >
      

                        
                    <div class="col-md-10">
                        <label >Market Place</label>
                      </div>
                      <div class="col-md-10 mb-3" align="center">
                                            <select class="form-control" name="marketplace" id="marketplace">
                      <option value="">Market Place</option>
                      <option value="LAZADA">LAZADA</option>
                      <option value="TOKOPEDIA">TOKOPEDIA</option>
                      <option value="SHOPEE">SHOPEE</option>
                      <option value="BUKALAPAK">BUKALAPAK</option>
                      <option value="OFFLINE">OFFLINE</option>
                      </select>
   
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>
                     
                    
                    

                      <div class="col-md-10">
                      <label >Order ID</label>
                       </div>
                      
                        <div class="col-md-10 mb-3" >
                                            
                                            <input type="text" class="form-control" name="order_id" id="order_id" placeholder="Order ID" name="order_id" required>
                                            <div class="invalid-feedback">
                                              
                                            </div>
                                        </div>

                    
                                        <div class="col-md-10">
                                            <label >Nama Pelanggan</label>
                       </div>
                        <div class="col-md-10 mb-3" >
                                            <input type="text" class="form-control" id="name" placeholder="Masukan nama pelanggan" value="" name="name" required>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>

                    <?php
                  
        
        echo '<div class="col-md-10">
                 <label >SKU</label>
                  <input type="text" class="form-control" name=SkuID id="SkuID" onkeydown="search(this)" placeholder="Masukan SKU Produk " required>
        </div>';
        
        echo '<div class="table-responsive">';
      
        $chItems = curl_init();
          curl_setopt($chItems, CURLOPT_URL, 'http://localhost/api/orders.php?request=get_order_items');
          $payloadItem = json_encode( array( "order_id"=> "null" ) );
          curl_setopt( $chItems, CURLOPT_POSTFIELDS, $payloadItem );
          curl_setopt( $chItems, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($chItems, CURLOPT_RETURNTRANSFER, 1);
          $contentItem = curl_exec($chItems);
          curl_close($chItems);

          //mengubah data json menjadi data array asosiatif
          $resultItem=json_decode($contentItem,true);

        
      echo '<div class="card">
      <table class="table table-striped table-hover">
      <tbody>';
          if ($resultItem['total_rows'] > 0) {
          
           foreach($resultItem['data'] as $DataProduct)

          {
      echo '<tr>';
              
     echo json_encode($resultItem['data']);
              echo '<td> <div class="css-1cagh9d">
              
              <img class="img-product" src='.$DataProduct['product_main_image'].'
              alt='.$DataProduct['name'].' width="80" height="80">';
              
              echo '<div class="css-gjyepm">';
              
              echo '<div class="styPLCProductNameInfo"><h6>';
              echo $DataProduct['name'];  
              echo '</h6></div>';
              
              echo '<div class="css-11v3zrg">';
              echo $DataProduct['sku'];
              echo '</div>';
              echo '<div class="css-11v3zrg">';
              echo $DataProduct['name'] . " " . $DataProduct['ProductVariantDetailName'];
              echo '</div>';
              echo '<div class="css-11v3zrg">';
              echo $DataProduct['paid_price'];
              echo '</div>';
              echo '</div></div>';
              echo'</td>';
              
          
            echo'</tr>';

          echo'</tbody>
        </table>';
        
    echo'<a data-toggle="modal" data-id="'.$DataProduct['order_item_id'].'" title="Delete Item"  class="DeleteVariantProduct btn btn-primary" href="#">Hapus</a>';      
    
    echo'</div> ';
    
    }
    
      }else{
    
  echo json_encode($result['message']);
  
  }         
      ?>  
        

<script>
  $(document).on("click", ".DeleteVariantProduct", function () {
     var order_id = $(this).data('id');
  //unset($resultItem['data'][0]);
   alert(order_id);
  
    for (var i = 0; i < $resultItem['data'].length; i++) {
  if ($resultItem['data'][i].order_item_id === order_id) {
   $resultItem['data'][i].sku = "Thomas";
    break;
  }
  alert($resultItem['data']);
}

});                  
  
</script>     
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Atur Pengiriman</button>
            </div>
        </div>
    </div>
</div>




<?php
}
?>

