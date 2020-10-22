<?php

include "../../config/db_connection.php";

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
$content = $_GET['request'];

if (isset($content) && $content != "") {

    //Load Models
    include "../../model/Model_Discount.php";

    $db = new Model_Discount();


    if ($content == "get_discount") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);


        $user_id = 5;

        $page = 1;

        $search = null;

        $limit = 10;

        $status =1;




        //if (isset($post['Page'])) {
        // $page = $post['Page'];
        // }

        if (isset($post['Search'])) {
            $search = $post['Search'];
        }
        if (isset($post['Status'])) {
            $status = $post['Status'];
        }


        if (isset($user_id) && isset($status)) {

            $getData = $db->getDataDiscount($user_id, $status, $page, $limit , $search);
            if ($getData != null) {

                while ($row = $getData->fetch_assoc()) {

                    $rows[] = $row;


                }



                $total = mysqli_num_rows($getData);


                $return = array(
                    "status" => 200,
                    "message" => "ok",
                    "total_rows" => $total,
                    "data" => $rows
                );
            } else {
                $return = array(
                    "status" => 404,
                    "total_rows" => 0,
                    "message" => "Belum ada Data",
                    "data" => []
                );
            }

        } else {
            $return = array(
                "status" => 404,
                "message" => "ERROR",
                "data" => []
            );
        }
        echo json_encode($return);
    }


    if ($content == "get_discount_items") {
        $modeHeader = 0;
        $post = json_decode(file_get_contents("php://input"), true);
//                    $user_id = $userid_header;
        $user_id = null;
        $discount_id= $post['DiscountID'];
        //$product_id= 1;

        $rowProductVariant =  array();
        $rowImageProductVariant =  array();
        $rowProductVariantDetail =  array();

        $getData = $db->getProductItem($discount_id);



        $getProductVariant = $db->getProductVariant($product_id);
        $getImageProductVariant = $db->getImageVariant($product_id);
        $getProductVariantDetail = $db->getProductVariantDetail($product_id);


        if ($getData != null  ) {


            $productArr = [];
            $productVariantArr = [];
            $imageVariantArr = [];
            $productVariantDetailArr = [];


            while ($rowImageProductVariant = $getImageProductVariant->fetch_assoc()) {

                $rowImageProductVariants[] = $rowImageProductVariant;


                $imageVariantArr[$rowImageProductVariant['ImageProductVariantID']]['ProductVariantID'] = $rowImageProductVariant['ProductVariantID'];
                $imageVariantArr[$rowImageProductVariant['ImageProductVariantID']]['ImageProductVariantID'] = $rowImageProductVariant['ImageProductVariantID'];
                $imageVariantArr[$rowImageProductVariant['ImageProductVariantID']]['ImageProductVariantName'] = $rowImageProductVariant['ImageProductVariantName'];
                $imageVariantArr[$rowImageProductVariant['ImageProductVariantID']]['isDefault'] = $rowImageProductVariant['isDefault'];
                $resultImageVariant = array_values($imageVariantArr);


            }


            while ($rowProductVariantDetail = $getProductVariantDetail->fetch_assoc()) {

                $rowProductVariantDetails[] = $rowProductVariantDetail;


                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['ProductVariantDetailID'] = $rowProductVariantDetail['ProductVariantDetailID'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['ProductVariantID'] = $rowProductVariantDetail['ProductVariantID'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['SkuID'] = $rowProductVariantDetail['SkuID'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['ProductVariantDetailName'] = $rowProductVariantDetail['ProductVariantDetailName'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['PriceRetail'] = $rowProductVariantDetail['PriceRetail'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['PriceReseller'] = $rowProductVariantDetail['PriceReseller'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['Stock'] = $rowProductVariantDetail['Stock'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['Barcode'] = $rowProductVariantDetail['Barcode'];
                $productVariantDetailArr[$rowProductVariantDetail['ProductVariantDetailID']]['isDefault'] = $rowProductVariantDetail['isDefault'];
                $resultProductDetails = array_values($productVariantDetailArr);


            }





            while ($rowProductVariant = $getProductVariant->fetch_assoc()) {

                $rowProductVariants[] = $rowProductVariant;





                $productVariantArr[$rowProductVariant['ProductVariantID']]['ProductVariantID'] = $rowProductVariant['ProductVariantID'];
                $productVariantArr[$rowProductVariant['ProductVariantID']]['ProductID'] = $rowProductVariant['ProductID'];
                $productVariantArr[$rowProductVariant['ProductVariantID']]['ProductVariantName'] = $rowProductVariant['ProductVariantName'];
                $productVariantArr[$rowProductVariant['ProductVariantID']]['isDefault'] = $rowProductVariant['isDefault'];


                $productVariantArr[$rowProductVariant['ProductVariantID']]['image_product_variant'] = $resultImageVariant;
                $productVariantArr[$rowProductVariant['ProductVariantID']]['product_variant_details'] = $resultProductDetails;

            }



            $result1 = array_values($productVariantArr);


            while ($row = $getData->fetch_assoc()) {

                $rows[] = $row;


                $productArr[$row['ProductID']]['ProductID'] = $row['ProductID'];
                $productArr[$row['ProductID']]['ProductName'] = $row['ProductName'];
                $productArr[$row['ProductID']]['Description'] = $row['Description'];
                $productArr[$row['ProductID']]['Weight'] = $row['Weight'];

                $productArr[$row['ProductID']]['product_variants'] = $result1;


            }




            $result = array_values($productArr);




            $total = mysqli_num_rows($getData);


            $return = array(
                "status" => 200,
                "message" => "ok",
                "total_rows" => $total,
                "data" => $result
            );
        } else {
            $return = array(
                "status" => 200,
                "total_rows" => 0,
                "message" => "Belum ada Produk",
                "data" => []
            );
        }


        echo json_encode($return);
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








                if ($content == "add_product") {
                    $modeHeader = 0;
                    $post = json_decode(file_get_contents("php://input"), true);
                    $user_id = $post['UserID'];
                    $product_name = $post['ProductName'];
                    $deskripsi= $post['Deskripsi'];
                    $price = $post['Price'];
                    $price_sell = $post['PriceSell'];
                    $category_id = $post['CategoryID'];
                    $brand_id = $post['BrandID'];
                    $stock= $post['Stock'];
                    $weight = $post['Weight'];
                    $product_variants= $post['ProductVariants'];
                    $product_variant_details= $post['ProductVariantDetails'];
                    $image_products= $post['ImageProducts'];
                    $image_product_variants= $post['ImageProductVariants'];
                    if (isset($user_id) && isset($product_name) && isset($price) && isset($stock)) {

                        //cek user id
                        $checkData = $db->checkUserByUserIDRegister($user_id);
                        if ($checkData) {

                            $create = $db->createProduct($user_id, $product_name ,$deskripsi , $price , $stock , $weight,$price_sell  ,$category_id, $brand_id);
                            //jika produk berhasil
                            if ($create) {

                                $product_id = $db->getProductID($user_id);
                                //jika produk id ada
                                if ($product_id != null) {
                                    $createImagePorducts= $db->processImageProducts($image_products, $product_id);
                                    $createProductVariants = $db->processVariant($product_variants, $product_id);

                                    //jika proses variant berhasil
                                    if ($createProductVariants) {

                                        $variant_id = $db->getVariantID($user_id ,$product_id );

                                        if ($variant_id != null) {
                                            $createImagePorductVariants= $db->processImageProductVariants($image_product_variants, $variant_id);
                                            $createDetailVariant = $db->processVariantDetail($product_id, $variant_id , $product_variant_details);

                                            if ($createDetailVariant) {

                                                $return = array(
                                                    "status" => 200,
                                                    "message" => "Produk berhasil ditambahkan",
                                                    "ProductID" => $product_id
                                                );


                                            } else {
                                                $return = array(
                                                    "status" => 404,
                                                    "message" => "Gagal menambahkan detail variant"
                                                );
                                            }

                                        }

                                        //jika variant gagal
                                    } else {
                                        $return = array(
                                            "status" => 404,
                                            "message" => "Gagal menambahkan variant"
                                        );
                                    }


                                    //Tutup cek produk id

                                }
                                //jika produk gagal
                            } else {
                                $return = array(
                                    "status" => 404,
                                    "message" => "Gagal menambahkan produk"
                                );
                            }

                        } else {
                            $return = array(
                                "status" => 404,
                                "message" => "Anda tidak memiliki akses"
                            );
                        }

                        //Jika user id tidak ada//
                    } else {
                        $return = array(
                            "status" => 404,
                            "message" => "Method not found"
                        );
                    }
                    echo json_encode($return);
                }


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
    //Aha, what you're looking for !!!
    $return = array(
        "status" => 404,
        "message" => "Method Not Found!"
    );

    echo json_encode($return);
}

?>
