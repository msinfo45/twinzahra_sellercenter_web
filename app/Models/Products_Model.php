<?php


class Products_Model 
{

    private $conn;

    // constructor
    function __construct()
    {
        include "db_connection.php";
       include "config_type.php";
        $this->conn = $conn;
        $this->uploaddir = $UPLOAD_DIR_2;
        $this->smsuserkey = $SMS_USERKEY;
        $this->smspasskey = $SMS_PASSKEY;
    }

    // destructor
    function __destruct()
    {

    }
	
	 public function getDataProduct($user_id, $status, $page, $limit , $search ,$search_size , $search_color)
    {
		
		     $condition = '';
			 $where = '';
        if ($page != '' && $limit != '') {
            if ($page == 1) {
                $p = 0;
            } else {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
			
			
			
        }
		
		if ($search != null) {
				
				    $query = $this->conn->query(" SELECT tp.ProductID,
										pv.ProductVariantID,
										pvd.SkuID,
										pvd.Barcode,
										tp.ProductName,									
										pv.ProductVariantName,
										pvd.ProductVariantDetailName,
										pvd.PriceRetail,
										pvd.Stock as Stock,
										ipv.ImageProductVariantName as ImageVariantProduct
										FROM products AS tp
										LEFT JOIN product_variant_details AS pvd
										ON tp.ProductID = pvd.ProductID
										LEFT JOIN product_variants AS pv
										ON pvd.ProductVariantID = pv.ProductVariantID
										LEFT JOIN image_product_variants as ipv
										ON pv.ProductVariantID = ipv.ProductVariantID		
										where  tp.UserID = '" . $user_id . "' AND (tp.ProductName LIKE CONCAT('%','" . $search . "','%') OR pvd.ProductVariantDetailName LIKE CONCAT('%','" . $search . "','%'))
                                        Order by tp.ProductID and pvd.Stock DESC " . $condition);
										

         
										
			
			}else if ($search_size != null) {

   				 $query = $this->conn->query("SELECT tp.ProductID,
										pv.ProductVariantID,
										pvd.SkuID,
										pvd.Barcode,
										tp.ProductName,									
										pv.ProductVariantName,
										pvd.ProductVariantDetailName,
										pvd.PriceRetail,
										pvd.Stock as Stock,
										ipv.ImageProductVariantName as ImageVariantProduct
										FROM products AS tp
										LEFT JOIN product_variant_details AS pvd
										ON tp.ProductID = pvd.ProductID
										LEFT JOIN product_variants AS pv
										ON pvd.ProductVariantID = pv.ProductVariantID
										LEFT JOIN image_product_variants as ipv
										ON pv.ProductVariantID = ipv.ProductVariantID		

										WHERE  (tp.UserID = '" . $user_id . "' AND pvd.Stock > 0) AND (pvd.ProductVariantDetailName LIKE CONCAT('%','39','%'))
                                        Order by tp.ProductID and pvd.Stock DESC " . $condition);
										

			}else{
				
				
				 $query = $this->conn->query(" SELECT tp.ProductID,
										pv.ProductVariantID,
										pvd.SkuID,
										pvd.Barcode,
										tp.ProductName,									
										pv.ProductVariantName,
										pvd.ProductVariantDetailName,
										pvd.PriceRetail,
										pvd.Stock as Stock,
										ipv.ImageProductVariantName as ImageVariantProduct
										FROM products AS tp
										LEFT JOIN product_variant_details AS pvd
										ON tp.ProductID = pvd.ProductID
										LEFT JOIN product_variants AS pv
										ON pvd.ProductVariantID = pv.ProductVariantID
										LEFT JOIN image_product_variants as ipv
										ON pv.ProductVariantID = ipv.ProductVariantID		
										where  tp.UserID = '" . $user_id . "' 										
                                        Order by tp.ProductID and pvd.ProductVariantDetailName DESC " . $condition);
			}
			
													 
																 
			if (mysqli_num_rows($query) > 0) {
            return $query;
			} else {
            return null;
			}
			
			}
			
			 public function getDataSync($user_id, $product_id)
    {
		
		   
          $query = $this->conn->query(" SELECT tp.ProductID,
										pv.ProductVariantID,
										pvd.SkuID,
										pvd.Barcode,
										tp.ProductName,									
										pv.ProductVariantName,
										pvd.ProductVariantDetailName,
										pvd.PriceRetail,
										pvd.Stock as Stock,
										ipv.ImageProductVariantName as ImageVariantProduct
										FROM products AS tp
										LEFT JOIN product_variant_details AS pvd
										ON tp.ProductID = pvd.ProductID
										LEFT JOIN product_variants AS pv
										ON pvd.ProductVariantID = pv.ProductVariantID
										LEFT JOIN image_product_variants as ipv
										ON pv.ProductVariantID = ipv.ProductVariantID
										where tp.ProductId = '" . $product_id . "'
                                        Order by tp.ProductID and pvd.ProductVariantDetailName DESC");
																		 
												 
																		 
			if (mysqli_num_rows($query) > 0) {
            return $query;
			} else {
            return null;
			}
			
			}
			
			
			
			
			
		

																		 
												 
		
			
			
	
	
	
	
	
	}

?>