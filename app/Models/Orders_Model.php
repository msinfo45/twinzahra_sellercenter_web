<?php
namespace App\Models;

use CodeIgniter\Model;

class Orders_Model extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id','order_number','user_id','marketplace','branch_number','warehouse_code','customer_first_name','customer_last_name','price','items_count','payment_method','voucher','voucher_code','voucher_platform','voucher_seller','gift_option','gift_message','shipping_fee','shipping_fee_discount_seller','shipping_fee_discount_platform','promised_shipping_times','national_registration_number','tax_code','extra_attributes','remarks','delivery_info','statuses','created_at','updated_at'
    ];
    protected $returnType = 'App\Entities\Orders';
    protected $useTimestamps = true;
   


 function __construct()
    {
       $db = \Config\Database::connect();
   
    }


    public function findById($id)
    {
        $data = $this->find($id);
        if($data)
        {
            return $data;
        }
        return false;
    }


    public function getDataOrders($user_id, $page, $limit , $status_id)
{
                  
     $db = \Config\Database::connect();                                                       
    $condition = '';
    if ($page != '' && $limit != '') {
    if ($page == 1) {
    $p = 0;
    } else {
     $p = ($page - 1) * $limit;
       }
                                                                         
      $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
      }
                                                                         
                                                                      
               

    $sql = "SELECT order_id , order_number ,marketplace,branch_number ,warehouse_code,
                                        customer_first_name , customer_last_name ,  price , 
                                          items_count , payment_method ,voucher ,  voucher_code , voucher_platform , voucher_seller , 
                                          gift_option ,gift_message , shipping_fee, 
                                           shipping_fee_discount_seller , shipping_fee_discount_platform, promised_shipping_times  ,
                                           national_registration_number,  tax_code ,extra_attributes , remarks , delivery_info ,
                                           statuses , created_at , updated_at
                                            FROM 
                                             history_orders";  


    $result = $db -> query ($sql);
        

return $result;
  //if (mysqli_num_rows($result) > 0) {
   //    return $query;
    //   } else {
    //    return null;
     //}
      }
                                                                                                     
                                     




}