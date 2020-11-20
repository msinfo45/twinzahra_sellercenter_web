
<?php

function getData(){


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, base_url('public/api/marketplace/get_marketplace'));
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


$result = getData();

//echo json_encode($result);die;


if (count($result['data']) > 0) {

    foreach($result['data'] as $DataProduct)
    {

        //Set Variable History Orders
        $marketplace = $DataProduct['marketplace_name'] ;
        $active = $DataProduct['active'] ;
        $app_key = $DataProduct['app_key'] ;
        $app_secret = $DataProduct['app_secret'] ;

        if ($marketplace == "LAZADA") {

         $url = "https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_url=" . base_url('/marketplace/create_token') . "&client_id=". $app_key ;

        }else if ($marketplace == "SHOPEE") {

            $redirect = base_url('/marketplace/create_token_shopee');
            $base_string = $app_secret . $redirect;
            $token = hash('sha256', $base_string);
            $url = "https://partner.shopeemobile.com/api/v1/shop/auth_partner?id=".$app_key."&redirect=" . $redirect . "&token=".$token;

        }


        if ($active == "1") {

            $active = "AKTIF";

        }else{

            $active = "TIDAK AKTIF";
        }

           
            echo '<a href="' . $url . '" target = "_blank"><div class="card" >';

            echo'<div class="card-header">
					<div class="row">';
            if ($marketplace == "SHOPEE"){
                echo '<div class="col-auto justify-content-center align-self-center"><img class="img-product" width="40px" height="40px" src="https://twinzahra.masuk.id/public/images/shopee.png"></div>';
            }else if ($marketplace == "LAZADA"){
                echo '<div class="col-auto justify-content-center align-self-center"><img class="img-product" width="40px" height="40px" src="https://twinzahra.masuk.id/public/images/lazada.png"></div>';
            }else if ($marketplace == "OFFLINE"){
                echo '<div class="col-auto justify-content-center align-self-center">OFFLINE</div>';
            }
            echo'<div class="col font-weight-bold justify-content-center align-self-center"> '.$marketplace.	'	</div>';

            echo'</div></div>';




            echo'</div> </a>';



    }

}else{

    echo '<div class="card-body text-center" >'.$result['message'] .'</div>';


}



?>



<script>

    $(document).on("click", ".EditOrder", function () {
        var order_id = $(this).data('id');
        var name = $(this).data('name');
        var marketplace = $(this).data('marketplace');


        $(".modal-body #order_id").val(order_id );
        $(".modal-body #name").val(name);
        $(".modal-body #marketplace").val(marketplace);

        // As pointed out in comments,
        // it is unnecessary to have to manually call the modal.
        $('.modal-body #marketplace').attr("disabled","disabled");
        $('.modal-body #order_id').attr("disabled","disabled");
        $('.modal-body #name' ).attr("disabled","disabled");
        $('#EditOrder').modal('show');

    });



    $(document).on("click", ".AcceptOrder", function () {
        var order_id = $(this).data('id');
        var merchant_name = $(this).data('merchant_name');
        var marketplace = $(this).data('marketplace');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            processData: false,
            data: '{"order_id": "'+ order_id +'", "merchant_name": "'+ merchant_name +'", "marketplace": "'+ marketplace +'","shipping_provider": "dropship", "delivery_type": "dropship"}',

            url:'<?= base_url('public/api/orders/accept_order') ?>',

            beforeSend: function () {
                $('.btn').attr("disabled","disabled");
                $('#AcceptOrder .modal-body').css('opacity', '.5');
            },
            success:function(data){

                console.log(data.message);
                console.log(data.status);

                if(data.status == '200'){
                    $('#AcceptOrder #order_id').val('');
                    $('#AcceptOrder #shipping_provider').val('');
                    $('#AcceptOrder #delivery_type').val('');

                    $('.statusMsg').html('<span style="color:green;"></p>' +data.message );
                    alert(data.message);
                    window.location.href = '<?= base_url('orders') ?>';
                }else{

                    $('.statusMsg').html('<span style="color:red;"></p>'+data.message);
                    alert(data.message);
                    window.location.href = '<?= base_url('orders') ?>';
                }
                $('.btn').removeAttr("disabled");
                $('#AcceptOrder .modal-body').css('opacity', '');



            },
            error: function(){
                alert("Cannot get data");
            }

        });
    });

    $(document).on("click", ".AcceptOrderShopee", function () {
        var data_order = $(this).data('data_order');
        var merchant_name = $(this).data('merchant_name');
        var marketplace = $(this).data('marketplace');

        $.ajax({
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            processData: false,
            data: '{"data_order": "'+ data_order +'", "merchant_name": "'+ merchant_name +'", "marketplace": "'+ marketplace +'"}',

            url:'<?= base_url('public/api/orders/created_order2') ?>',

            beforeSend: function () {
                $('.btn').attr("disabled","disabled");
                $('#AcceptOrder .modal-body').css('opacity', '.5');
            },
            success:function(data){

                console.log(data.message);
                console.log(data.status);

                if(data.status == '200'){
                    $('#AcceptOrder #order_id').val('');
                    $('#AcceptOrder #shipping_provider').val('');
                    $('#AcceptOrder #delivery_type').val('');

                    $('.statusMsg').html('<span style="color:green;"></p>' +data.message );
                    alert(data.message);
                    window.location.href = '<?= base_url('orders') ?>';
                }else{

                    $('.statusMsg').html('<span style="color:red;"></p>'+data.message);
                    alert(data.message);
                    window.location.href = '<?= base_url('orders') ?>';
                }
                $('.btn').removeAttr("disabled");
                $('#AcceptOrder .modal-body').css('opacity', '');



            },
            error: function(){
                alert("Cannot get data");
            }

        });
    });








</script>

<?= $this->include('orders/modal/edit_order') ?>