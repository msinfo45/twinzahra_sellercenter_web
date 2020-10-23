<?php

class Model_Discount
{

    private $conn;

    // constructor
    function __construct()
    {
        include "../../config/db_connection.php";
        include "../../config/config_type.php";
        $this->conn = $conn;

    }

    // destructor
    function __destruct()
    {

    }

    public function getDataDiscount($user_id ,$status, $page, $limit , $search)
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


if ($search == null) {
    $query = $this->conn->query("SELECT
    dsc.DiscountID,
    dsc.DiscountName,
    dsc.StartDate,
    dsc.EndDate,
    dsc.UserID,
    ds.DiscountStatusName,
    COUNT(dd.SkuID) AS CountSku
    FROM
    discount AS dsc
    LEFT JOIN
   discount_details AS dd
   ON
   dsc.DiscountID = dd.DiscountID
    LEFT JOIN
    discount_status AS ds
    ON dsc.Status = ds.ID
    WHERE dsc.UserID = " . $user_id . "
    ORDER BY CreateDate DESC LIMIT " . $limit . " OFFSET " . $p . " ");


        }else{

            $query = $this->conn->query("SELECT * FROM 
	                                products AS tp
	                                LEFT JOIN image_products AS ip
	                                ON tp.ProductID = ip.ProductID
									
										where (tp.UserID =" . $user_id . "	and tp.Status =" . $status . ") and (ip.isDefault = 1) and tp.ProductName LIKE CONCAT('%','" . $search . "','%')
										
										Order by tp.ProductID ASC");

        }





        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }

    }


}

?>
