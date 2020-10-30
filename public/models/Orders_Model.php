<?php


class Orders_Model 
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

public function checkProductBySKU($sku_id)
    {
        $query = $this->conn->query("SELECT * FROM product_variant_details WHERE SkuID = '" . $sku_id . "' ");

        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
    }

public function getDataOrders($user_id, $page, $limit , $status_id)
{
                                                                         
    $condition = '';
    if ($page != '' && $limit != '') {
    if ($page == 1) {
                                                                         $p = 0;
                                                                         } else {
                                                                         $p = ($page - 1) * $limit;
                                                                         }
                                                                         
                                                                         $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
                                                                         }
                                                                         
                                                                      
               

                                                                        $query = $this->conn->query("SELECT order_id , order_number ,marketplace,branch_number ,warehouse_code,
                                                                        customer_first_name , customer_last_name ,  price , 
                                                                                                    items_count , payment_method ,voucher ,  voucher_code , voucher_platform , voucher_seller , 
                                                                                                     gift_option ,gift_message , shipping_fee, 
                                                                                                    shipping_fee_discount_seller , shipping_fee_discount_platform, promised_shipping_times  ,
                                                                                                     national_registration_number,  tax_code ,extra_attributes , remarks , delivery_info ,
                                                                                                      statuses , created_at , updated_at
                                                                                                     FROM 
                                                                                                    history_orders
                                                                                                    where user_id = '" . $user_id . "' and statuses = '" . $status_id . "'
                                                                                                     Order by created_at DESC " . $condition);  
                                                                                                     
                                                                                                     if (mysqli_num_rows($query) > 0) {
                                                                                                     return $query;
                                                                                                     } else {
                                                                                                     return null;
                                                                                                     }
                                                                                                     }
                                                                                                     
                                                                                


                                                                public function getDataOrderItems($user_id, $page, $limit , $order_id)
                                                                         {
                                                                         
                                                                         $condition = '';
                                                                         if ($page != '' && $limit != '') {
                                                                         if ($page == 1) {
                                                                         $p = 0;
                                                                         } else {
                                                                         $p = ($page - 1) * $limit;
                                                                         }
                                                                         
                                                                         $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
                                                                         }
                                                                         
                                                                      

                                                                        $query = $this->conn->query("SELECT * FROM 
                                                                                                    history_order_details
                                                                                                    where order_id = '" . $order_id . "'
                                                                                                     Order by created_at DESC " . $condition);                                                                                                  
                                                                                                     if (mysqli_num_rows($query) > 0) {
                                                                                                     return $query;
                                                                                                     } else {
                                                                                                     return null;
                                                                                                     }
                                                                                                     }
                                                                                                                    

                                                                    
    public function checkHistoryOrderByOrder($order_id , $user_id)
    {
        $query = $this->conn->query("SELECT * from history_orders WHERE order_id = '" . $order_id . "' and user_id = '" . $user_id . "'");

    if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return false;
        }
        
     
     
    }


  public function acceptOrders($user_id, $order_id)
    {

        $update = $this->conn->query("UPDATE history_orders SET 
                                        statuses        = 2
                                    WHERE 
                                        order_id = '" . $order_id . "' and user_id = '" . $user_id . "'");


        if ($update) {
            return true;
        } else {
            return false;
        }
    }


    




    }

?>