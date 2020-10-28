<?php


class Products_Model 
{

    private $conn;

    // constructor
    function __construct()
    {
    	include "public/config/db_connection.php";
    	include "public/config/config_type.php";
 
        $this->conn = $conn;
        $this->uploaddir = $UPLOAD_DIR_2;
        $this->smsuserkey = $SMS_USERKEY;
        $this->smspasskey = $SMS_PASSKEY;
    }

    // destructor
    function __destruct()
    {

    }


    				 public function getConfigLazada($user_id)
    {
		
		   
          $query = $this->conn->query(" SELECT * from lazada
										where UserID = '" . $user_id . "' and active=1
                                       ");
																		 
												 
																		 
			if (mysqli_num_rows($query) > 0) {
            return $query;
			} else {
            return null;
			}
			
			}
			

	
	public function getDataProduct($user_id ,$status, $page, $limit , $search ,$search_size, $search_color)
    {
		
		     $condition = '';
			 $where = '';
		
        if ($page != '' && $limit != '') {
            if ($page == 1) {
                $p = 0;
            } else {
                $p = ($page - 1) * $limit;
            }

          $condition .= "Order by pvd.Stock and pvd.Stock DESC LIMIT " . $limit . " OFFSET " . $p . " ";
			
		
			
        }
		

if ($search != null) {
	
	   $query = $this->conn->query("SELECT * FROM 
	                                products AS tp
	                                LEFT JOIN image_products AS ip
	                                ON tp.ProductID = ip.ProductID
									
										where (tp.UserID =" . $user_id . "	and tp.Status =" . $status . ") and (ip.isDefault = 1) and tp.ProductName LIKE CONCAT('%','" . $search . "','%')
										
										Order by tp.ProductID ASC");
	


} else if ($search_size != null) {



  $query = $this->conn->query("
  	SELECT * FROM 
	                                products AS tp
	                                LEFT JOIN image_products AS ip
	                                ON tp.ProductID = ip.ProductID
					LEFT JOIN product_variants AS pv
					ON tp.ProductID = pv.ProductID	
					LEFT JOIN product_variant_details AS pvd
					ON pv.ProductVariantID = pvd.ProductVariantID			
					WHERE (tp.UserID =" . $user_id . " AND  pvd.Stock > 0 ) AND (ip.isDefault = 1 AND tp.Status =" . $status . ") AND (pvd.ProductVariantDetailName LIKE CONCAT('%','" . $search_size . "','%'))
										
										Order by tp.ProductID ASC");


}else{

		   $query = $this->conn->query("SELECT * FROM 
	                                products AS tp
	                                LEFT JOIN image_products AS ip
	                                ON tp.ProductID = ip.ProductID
                                    where (tp.UserID =" . $user_id . "	and tp.Status =" . $status . ") and (ip.isDefault = 1)
                                    Order by tp.ProductID ASC LIMIT " . $limit . " OFFSET " . $p . " ");

	
	  
	
}
			
				
      											 
												 
																 
			if (mysqli_num_rows($query) > 0) {
            return $query;
			} else {
            return null;
			}
			
			}
			

				 public function getProducts($user_id)
    		{
		
		   
		  

			   
			   $query = $this->conn->query("SELECT * from products	where UserID = '" . $user_id . "'	and Status = 1
                                        Order by ProductName ");
			   
			   
		   
          
																		 
												 
																		 
			if (mysqli_num_rows($query) > 0) {
            return $query;
			} else {
            return null;
			}
			
			}



   public function getProductItem($product_id)
{
                                                                         
$query = $this->conn->query("SELECT * FROM 
products AS tp
LEFT JOIN product_variants AS pv 
ON tp.ProductID = pv.ProductID 
LEFT JOIN product_variant_details AS pvd
ON pv.ProductVariantID = pvd.ProductVariantID
LEFT JOIN image_product_variants AS ipv
ON pv.ProductVariantID = ipv.ProductVariantID
WHERE (pv.ProductID = '" . $product_id . "' and ipv.isDefault = 1)
order by pvd.SkuID ASC
");

if (mysqli_num_rows($query) > 0) {
   return $query;
    } else {
     return null;
    }
    }


				 
 public function getProductVariant($product_id)
{
		$query = $this->conn->query("SELECT 
										*
									FROM 
									product_variants AS pv
									LEFT JOIN products AS tp
									ON pv.ProductID = tp.ProductID
									WHERE pv.ProductID = '" . $product_id . "'
									order by pv.isDefault = 1 DESC
									");
                                                                                                     
if (mysqli_num_rows($query) > 0) {
return $query;
} else {
return null;
}
}


public function getImageVariant($product_id)
{
   $query = $this->conn->query("SELECT 
								ipv.ProductVariantID ,
								ipv.ImageProductVariantID,
								ipv.ImageProductVariantName,
								ipv.isDefault
								FROM 
								image_product_variants AS ipv
								LEFT JOIN product_variants AS pv
								ON ipv.ProductVariantID = pv.ProductVariantID
								LEFT JOIN products AS tp
								ON pv.ProductID = tp.ProductID
								WHERE pv.ProductID = '" . $product_id . "'
								order by ipv.isDefault = 1 DESC
								 ");
                                                                                                     
if (mysqli_num_rows($query) > 0) {
return $query;
} else {
return null;
}
}

public function getProductVariantDetail($product_id)
{

$query = $this->conn->query("SELECT 
                             pvd.ProductVariantDetailID,
                            pvd.ProductVariantID,
                            pvd.SkuID,
                            pvd.ProductVariantDetailName,
                            pvd.PriceRetail,
                            pvd.PriceReseller,
                            pvd.Stock,
                            pvd.Barcode,
                            pvd.isDefault          
							FROM 
							product_variant_details AS pvd
							LEFT JOIN product_variants AS pv
							ON pvd.ProductVariantID = pv.ProductVariantID
							LEFT JOIN products AS tp
							ON pv.ProductID = tp.ProductID
							WHERE pv.ProductID = '" . $product_id . "'
							order by pvd.ProductVariantDetailName
							");

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
			
			
			 public function getDataProductVariants2($user_id, $item)
    {





        $query = $this->conn->query("SELECT p.ProductID , p.CategoryID , p.ProductName , p.Description , pv.ProductVariantName , 
											   pvd.SkuID ,pvd.ProductVariantDetailName , pvd.Stock , 
											   pvd.PriceRetail , pvd.PriceReseller ,mb.BrandName AS brand 
											   FROM
											products AS p
											LEFT JOIN product_variants AS pv 
											ON pv.ProductID = p.ProductID
											LEFT JOIN product_variant_details AS pvd
											ON pvd.ProductVariantID = pv.ProductVariantID
											LEFT JOIN master_brand AS mb
											ON p.BrandID = mb.BrandID

										where (p.UserID =" . $user_id . " and p.Status =1) and (p.ProductID = " . $item . ")
									order by pvd.SkuID
										");







        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }

    }

	
		 public function getSkus($user_id)
		{
		

          $query = $this->conn->query("Select SkuID from product_variant_details
										where UserID = '" . $user_id . "'");
										
					 
																 
			if (mysqli_num_rows($query) > 0) {
            return $query;
			} else {
            return null;
			}
			
			}
			
			
		

																		 
												 
		
			
			
	
	
	
	
	
	}

?>