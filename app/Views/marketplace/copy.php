<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php

function getToko(){


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, base_url('public/api/marketplace/get_toko'));
    $payload = json_encode( array( "user_id"=> "5"
    ) );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);

    //mengubah data json menjadi data array asosiatif
    $result=json_decode($content,true);

    return $result;


}

?>
<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        Salin Produk
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Salin Produk</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <div class="content">
        <div class="card">

        <div class="card-header">
        <div class="row ">
        <div class="col-md-4 justify-content-center align-self-center">
        <div class="input-group p-1">
            <label class="form-control-plaintext">Dari</label>
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <select class="form-control col-md-8" name="from" id="from">
                <option value="DATABASE">Database</option>
            </select>
        </div>
        </div>

            <div class="col-md-4 justify-content-center align-self-center">
            <div class="input-group p-1">
            <label class="form-control-plaintext">Ke</label>
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <select class="form-control col-md-8" name="to" id="to">
             <?php
             $result = getToko();
                if (count($result['data']) > 0) {

                    foreach ($result['data'] as $DataProduct) {
                        //Set Variable History Orders
                        $marketplace = $DataProduct['marketplace_name'];
                        $merchant_name = $DataProduct['merchant_name'];
                        $seller_id = $DataProduct['seller_id'];
                        echo '<option value="'.$seller_id.'">';

           echo'<div class="font-weight-bold justify-content-center align-self-center"> '.$merchant_name.	' ('.$marketplace.')	</div>';

            echo'</option>';
                    }
                }
              ?>
            </select>
        </div>
        </div>

      <div class="col-auto justify-content-center align-self-center">
        <button title="Prosess"  id="prosess" class="prosess btn btn-primary">Proses</button>
      </div>

        </div>
        </div>


     <div id="ResultData"></div>


    </div>

    </div>
</div>
</div>




<script>

    $(document).on("click", ".prosess", function () {

        var eaFrom = document.getElementById("from");
        var from = eaFrom.options[eaFrom.selectedIndex].value;
        var eaTo = document.getElementById("to");
        var to = eaTo.options[eaTo.selectedIndex].value;

        var displayProduct = 5;
        $('#ResultData').html(createSkeleton(displayProduct));

        setTimeout(function(){
            loadData(displayProduct);
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

        function loadData(limit){

            $.ajax({
                type: 'POST',
                data: '{"seller_id": "'+ to +'" , "action": "load_data_copy", "limit":"'+ limit +'"}',

                url:'<?= base_url('marketplace/load_data_copy') ?>',

                beforeSend: function () {
                    $('.prosess').attr("disabled","disabled");
                    $('.card-header').css('opacity', '.5');
                },
                success:function(data){
                    $('.prosess').removeAttr("disabled");
                    $('.card-header').css('opacity', '');
                    $('#ResultData').html(data);

                },
                error: function(){

                    alert("Cannot get data");
                }


            });

        }

    });



</script>




<?= $this->endSection() ?>

