
<?php

class Model_user
{

    private $conn;

    // constructor
    function __construct()
    {
        include "db_connection.php";
        include "config_type.php";
        // Load Composer's autoloader
        require '../include/PHPMailer/PHPMailerAutoload.php';
        $this->conn = $conn;
        $this->uploaddir = $UPLOAD_DIR_2;
        $this->smsuserkey = $SMS_USERKEY;
        $this->smspasskey = $SMS_PASSKEY;

        // Import PHPMailer classes into the global namespace




    }

    // destructor
    function __destruct()
    {

    }

    /**
     * Create New User
     */
    public function createUser($firstname, $lastname, $email, $password, $firebase_id, $firebase_time, $device_brand, $device_model, $device_serial, $device_os, $referral_by)
    {

        //Generate Encrypt Password
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"];
        $salt_password = $hash["salt"];
        $code = $this->generatePIN();

        //Generate Token
        $token = $this->generateToken();

        $ref = "";
        if ($referral_by != "")
        {
            $ref = $referral_by;
        }

        $insert = $this
            ->conn
            ->query("INSERT INTO users 
									(FirstName, 
									LastName, 
									Email,
									Password,
									PasswordSalt,
									Token,
									FirebaseID,
									FirebaseTime,
									DeviceBrand,
									DeviceModel,
									DeviceSerial,
									DeviceOS,
									CreatedDate,
									ActivationCode,
									ReferralBy,
									Active
									) 
								VALUES 
									('" . $firstname . "', 
									'" . $lastname . "',
									'" . $email . "',
									'" . $encrypted_password . "',
									'" . $salt_password . "',
									'" . $token . "',
									'" . $firebase_id . "',
									'" . $firebase_time . "',
									'" . $device_brand . "',
									'" . $device_model . "',
									'" . $device_serial . "',
									'" . $device_os . "',
									'" . $this->get_current_time() . "',
									'" . $code . "',
									'" . $ref . "',
									'1'
									) ");

        if ($insert)
        {
            // $name = $firstname;
            //$this->send_sms($email, $code, $name);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function saveCodeLazada($user_id, $code)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO lazada 
									(UserID,
									Code,
									CreatedDate
									) 
								VALUES 
									('" . $user_id . "', 
									'" . $code . "', 
									'" . $this->get_current_time() . "'
									) ");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function updateCodeLazada($user_id, $code)
    {

        $update = $this
            ->conn
            ->query("UPDATE lazada SET Code = '" . $code . "' , UpdateDate =  '" . $this->get_current_time() . "' 
										where UserID = '" . $user_id . "'");

        if ($update)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function cekUserIDLazada($user_id)
    {

        $insert = $this
            ->conn
            ->query("Select * from lazada where UserID = '" . $user_id . "'");

        if (mysqli_num_rows($insert) > 0)
        {

            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     * Create New User
     */
    public function createToko($user_id, $toko_name, $address, $phone, $category_toko_id, $email)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO master_toko 
									(TokoName, 
									Address, 
									Phone,
									CategoryTokoID,							
									Active,
									CreatedDate
									) 
								VALUES 
									('" . $toko_name . "', 
									'" . $address . "',
									'" . $phone . "',
									'" . $category_toko_id . "',							
									'1',
									'" . $this->get_current_time() . "'
									) ");

        if ($insert)
        {
            // $name = $firstname;
            //$this->send_sms($email, $code, $name);
            $dt = $this->getDataTokoByPhone($phone);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                $toko_id = $dt['TokoID'];
                $update = $this
                    ->conn
                    ->query("UPDATE users SET 
										TokoID = '" . $toko_id . "'
									WHERE 
										Email = '" . $email . "'");

            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create New User
     */
    public function insertStockOpname($user_id, $product_variant_name, $product_variant_detail_name, $product_name, $sku_id, $barcode, $unit, $stock_system, $stock_fisik, $selisih, $reason)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO stock_opname 
									(ProductVariantName, 
									ProductVariantDetailName, 
									ProductName,
									SkuID,							
									Barcode,
									Unit,
									StockSystem,
									StockFisik,
									Selisih,
									Reason,
									CreatedDate
									) 
								VALUES 
									('" . $product_variant_name . "', 
									'" . $product_variant_detail_name . "',
									'" . $product_name . "',
									'" . $sku_id . "',							
								'" . $barcode . "',	
								'" . $unit . "',	
								'" . $stock_system . "',	
								'" . $stock_fisik . "',	
								'" . $selisih . "',	
								'" . $reason . "',	
									'" . $this->get_current_time() . "'
									) ");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create New User
     */
    public function createProduct($user_id, $product_name, $deskripsi, $price, $stock, $weight, $price_sell, $category_id, $brand_id)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO products
									(UserID,
                                    SupplierID,
									ProductName, 
									Description, 
									Price,
									PriceSell,
									CategoryID,
									BrandID,
									Stock,	
									Weight,										
									Active,
                                     StatusID,
									CreatedDate,
                                     UpdateDate
									) 
								VALUES 
									('" . $user_id . "',
                                     '0',
									'" . $product_name . "', 
									'" . $deskripsi . "',
									'" . $price . "',
									'" . $price_sell . "',
									'" . $category_id . "',
									'" . $brand_id . "',
									'" . $stock . "',
									'" . $weight . "',
									'1',
                                    '1',
									'" . $this->get_current_time() . "',
                                     '" . $this->get_current_time() . "'
									)");

        if ($insert)
        {

            // $name = $firstname;
            //$this->send_sms($email, $code, $name);
            //$dt = $this->getDataTokoByPhone($phone);
            //if ($dt != null) {
            //$dt = $dt->fetch_assoc();
            // $toko_id = $dt['TokoID'];
            //$update = $this->conn->query(
            //"UPDATE users SET
            //			TokoID = '" . $toko_id . "'
            //		WHERE
            //		Email = '" . $email . "'");
            

            //}
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkCartDetails($sku_id, $user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM cart_details WHERE SKU = '" . $sku_id . "' AND UserID = '" . $user_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function createCartDetail($cart_id, $user_id, $sku_id, $product_id, $price, $quantity, $product_variant_id, $product_variant_detail_id)

    {
        //Lets process
        $exist = $this->checkCartDetails($sku_id, $user_id);
        if ($exist)
        {

            $update = $this
                ->conn
                ->query("UPDATE cart_details SET 
											Quantity = Quantity + 1,
											SubTotal = Price *  Quantity
										WHERE 
											UserID = '" . $user_id . "' AND SKU = '" . $sku_id . "' ");
            if ($update)
            {

                return true;
            }
            else
            {
                return false;
            }

        }
        else
        {

            $insert = $this
                ->conn
                ->query("INSERT INTO cart_details(CartID,

																		  SKU,
																		  ProductID,
																		  Price,
																		  Quantity,
																		  ProductVariantID,
																		  ProductVariantDetailID,
																		  UserID,
																		  SubTotal,
																		  CreatedDate
                                                                          
                                                                           )
                                                                          VALUES
                                                                          ('" . $cart_id . "' ,
																		  '" . $sku_id . "' ,
																		  '" . $product_id . "' ,
																		  '" . $price . "' ,
																		  '" . $quantity . "' ,
																		  '" . $product_variant_id . "' ,
																		  '" . $product_variant_detail_id . "' ,
																		    '" . $user_id . "' ,
																		    '" . $price * $quantity . "' ,
																		  '" . $this->get_current_time() . "'
                                                                           )");

            if ($insert)
            {

                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public function createHistoryOrders($order_id, $order_number, $user_id, $marketplace, $merchant_name ,$branch_number, $warehouse_code, $customer_first_name, $customer_last_name, $price, $items_count, $payment_method, $voucher, $voucher_code, $voucher_platform, $voucher_seller, $gift_option, $gift_message, $shipping_fee, $shipping_fee_discount_seller, $shipping_fee_discount_platform, $promised_shipping_times, $national_registration_number, $tax_code, $extra_attributes, $remarks, $delivery_info, $statuses, $created_at, $updated_at)
    {

        if ($created_at == "")
        {

            $created_at = $this->get_current_time();

        }

        $insert = $this
            ->conn
            ->query("INSERT INTO history_orders
                    (
					order_id,
					order_number,
					user_id,
					marketplace,
          merchant_name,
					branch_number,
					warehouse_code,
					customer_first_name,
					customer_last_name,
					price,
					items_count,
					payment_method,
					voucher,
					voucher_code,
					voucher_platform,
					voucher_seller,
					gift_option,
					gift_message,
					shipping_fee,
					shipping_fee_discount_seller,
					shipping_fee_discount_platform,
					promised_shipping_times,
					national_registration_number,
					tax_code,
					extra_attributes,
					remarks,
					delivery_info,
					statuses,
					created_at,
					updated_at
									)
                                    VALUES
                                    ( 
									'" . $order_id . "' ,
									'" . $order_number . "' ,
									'" . $user_id . "' ,
									'" . $marketplace . "' ,
                                    '" . $merchant_name . "' ,
									'" . $branch_number . "' ,
									'" . $warehouse_code . "' ,
									'" . $customer_first_name . "' ,
									'" . $customer_last_name . "' ,
									'" . $price . "' ,
									'" . $items_count . "' ,
									'" . $payment_method . "' ,
									'" . $voucher . "' ,
									'" . $voucher_code . "' ,
									'" . $voucher_platform . "' ,
									'" . $voucher_seller . "' ,
									'" . $gift_option . "' ,
									'" . $gift_message . "' ,
									'" . $shipping_fee . "' ,
									'" . $shipping_fee_discount_seller . "' ,
									'" . $shipping_fee_discount_platform . "' ,
									'" . $promised_shipping_times . "' ,
									'" . $national_registration_number . "' ,
									'" . $tax_code . "' ,
									'" . $extra_attributes . "',
									'" . $remarks . "',
									'" . $delivery_info . "',
									'" . $statuses . "',
									'" . $created_at . "',
									'" . $updated_at . "'
                                   )");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function createHistoryOrderDetails($order_id, $history_order_details)
    {

        //process insert batch
        //$sql = array();
        

        //$obj1 = json_encode($history_order_details, true);
        $obj = json_decode($history_order_details, true);

        foreach ($obj as $item)
        {

            $insert = $this->conn->query("INSERT INTO history_order_details
			(	
			order_item_id,
			order_id,
			purchase_order_id, 
			purchase_order_number, 
			invoice_number,
			sla_time_stamp, 
			package_id, 
			shop_id,
			order_type,
			shop_sku, 
			sku,
			name, 
			variation, 
			item_price, 
			paid_price,
			qty,
			currency, 
			tax_amount, 
			product_main_image, 
			product_detail_url, 
			shipment_provider, 
			tracking_code_pre, 
			tracking_code, 
			shipping_type, 
			shipping_provider_type, 
            shipping_fee_original, 
			shipping_service_cost , 
            shipping_fee_discount_seller, 
            shipping_amount, 
			is_digital, 
			voucher_amount, 
			voucher_seller, 
            voucher_code_seller, 
            voucher_code, 
			voucher_code_platform, 
			voucher_platform, 
			order_flag, 
			promised_shipping_time, 
			digital_delivery_info, 
            extra_attributes, 
			cancel_return_initiator, 
            reason, 
			reason_detail, 
            stage_pay_status, 
            warehouse_code, 
            return_status, 
            status, 
            created_at, 
            updated_at
                                                                          
        )
       VALUES (
			'" . $item['order_item_id'] . "', 
	   		'" . $item['order_id'] . "', 
			'" . $item['purchase_order_id'] . "', 
			'" . $item['purchase_order_number'] . "', 
			'" . $item['invoice_number'] . "',
			'" . $item['sla_time_stamp'] . "', 
			'" . $item['package_id'] . "', 
			'" . $item['shop_id'] . "',
			'" . $item['order_type'] . "',
			'" . $item['shop_sku'] . "', 
			'" . $item['sku'] . "', 
			'" . $item['name'] . "', 
			'" . $item['variation'] . "', 
			'" . $item['item_price'] . "', 
			'" . $item['paid_price'] . "',
			'" . $item['qty'] . "',
			'" . $item['currency'] . "', 
			'" . $item['tax_amount'] . "', 
			'" . $item['product_main_image'] . "', 
			'" . $item['product_detail_url'] . "', 
			'" . $item['shipment_provider'] . "', 
			'" . $item['tracking_code_pre'] . "', 
			'" . $item['tracking_code'] . "', 
			'" . $item['shipping_type'] . "', 
			'" . $item['shipping_provider_type'] . "', 
            '" . $item['shipping_fee_original'] . "', 
			'" . $item['shipping_service_cost'] . "' , 
            '" . $item['shipping_fee_discount_seller'] . "', 
            '" . $item['shipping_amount'] . "', 
			'" . $item['is_digital'] . "', 
			'" . $item['voucher_amount'] . "', 
			'" . $item['voucher_seller'] . "', 
            '" . $item['voucher_code_seller'] . "', 
            '" . $item['voucher_code'] . "', 
			'" . $item['voucher_code_platform'] . "', 
			'" . $item['voucher_platform'] . "', 
			'" . $item['order_flag'] . "', 
			'" . $item['promised_shipping_time'] . "', 
			'" . $item['digital_delivery_info'] . "', 
            '" . $item['extra_attributes'] . "', 
			'" . $item['cancel_return_initiator'] . "', 
            '" . $item['reason'] . "', 
			'" . $item['reason_detail'] . "', 
            '" . $item['stage_pay_status'] . "', 
            '" . $item['warehouse_code'] . "', 
            '" . $item['return_status'] . "', 
             '" . $item['status'] . "', 
            '" . $item['created_at'] . "', 
            '" . $item['updated_at'] . "'

	   
	   )");

        }

        if ($insert)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

  public function createHistoryAddressShipping($order_id, $address_shipping)
  {

    //$obj = json_decode($address_shipping, true);


      $insert = $this->conn->query("INSERT INTO history_order_address_shipping
			(	
			order_id,
			first_name, 
			last_name, 
			country,
			phone, 
			phone2, 
			address1,
			address2,
			address3, 
			address4,
			address5, 
			city, 
			post_code
                                                                          
        )
       VALUES (
			'" . $address_shipping['order_id'] . "', 
	    '" . $address_shipping['first_name'] . "', 
			'" . $address_shipping['last_name'] . "', 
			'" . $address_shipping['country'] . "', 
			'" . $address_shipping['phone'] . "',
			'" . $address_shipping['phone2'] . "', 
			'" . $address_shipping['address1'] . "', 
			'" . $address_shipping['address2'] . "',
			'" . $address_shipping['address3'] . "',
			'" . $address_shipping['address4'] . "', 
			'" . $address_shipping['address5'] . "', 
			'" . $address_shipping['city'] . "', 
			'" . $address_shipping['post_code'] . "'
	   )");



    if ($insert)
    {
      return true;
    }
    else
    {
      return false;
    }

  }

  public function createHistoryAddressBilling($order_id, $address_billing)
  {


    $insert = $this->conn->query("INSERT INTO history_order_address_billing
			(	
			order_id,
			first_name, 
			last_name, 
			country,
			phone, 
			phone2, 
			address1,
			address2,
			address3, 
			address4,
			address5, 
			city, 
			post_code
                                                                          
        )
       VALUES (
			'" . $address_billing['order_id'] . "', 
	    '" . $address_billing['first_name'] . "', 
			'" . $address_billing['last_name'] . "', 
			'" . $address_billing['country'] . "', 
			'" . $address_billing['phone'] . "',
			'" . $address_billing['phone2'] . "', 
			'" . $address_billing['address1'] . "', 
			'" . $address_billing['address2'] . "',
			'" . $address_billing['address3'] . "',
			'" . $address_billing['address4'] . "', 
			'" . $address_billing['address5'] . "', 
			'" . $address_billing['city'] . "', 
			'" . $address_billing['post_code'] . "'
	   )");



    if ($insert)
    {
      return true;
    }
    else
    {
      return false;
    }

  }
  public function checkTokenSessionCart($token_session, $user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT CartID FROM cart WHERE 
								UserID = '" . $user_id . "' AND TokenSession = '" . $token_session . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;

        }
        else
        {

            return false;

        }
    }

    public function createCart($token_session, $customer_id, $user_id, $firstname, $lastname, $grand_total, $item_count)
    {

        //Lets process
        $exist = $this->checkTokenSessionCart($token_session, $user_id);

        $cart_id = $exist;

        if ($cart_id != null)
        {

            $update = $this
                ->conn
                ->query("UPDATE cart SET 
								FirstName = '" . $firstname . "',
                      			LastName = '" . $lastname . "',
                  				GrandTotal = '" . $grand_total . "',
              					ItemCount = '" . $item_count . "',
								UpdatedDate = '" . $this->get_current_time() . "'
								WHERE 
								UserID = '" . $user_id . "' AND TokenSession = '" . $token_session . "' ");
            if ($update)
            {

                while ($row = $cart_id->fetch_assoc())
                {
                    $rows = $row['CartID'];
                }

                return $rows;

            }
            else
            {

                return false;

            }

        }
        else
        {

            $insert = $this
                ->conn
                ->query("INSERT INTO cart
                                 (TokenSession,
                                 CustomerID,
                                 UserID,
                                 FirstName,
                                 LastName,
                                 GrandTotal,
                                 ItemCount,                                
                                 CreatedDate


                                  )
                                   VALUES
                                  ('" . $token_session . "',
                                 	'" . $customer_id . "',
                              		'" . $user_id . "',
                          			'" . $firstname . "',
                      				'" . $lastname . "',
                  					'" . $grand_total . "',
              						'" . $item_count . "',

              						'" . $this->get_current_time() . "'
              					)");

            if ($insert)
            {

                $id = mysqli_insert_id($this->conn);

                return $id;

            }
            else
            {
                return false;
            }

        }
    }

    /**
     * Create New User
     */
    public function createCategory($user_id, $category_name, $category_code)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO master_category
									(CategoryName,
									Active,
									CategoryCode,
									sub_category1,
									CreatedDate
									) 
								VALUES 
									('" . $category_name . "',
									'0',
									'" . $category_code . "',
									'0',								
									'" . $this->get_current_time() . "'
									)");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create New User
     */
    public function createBrand($user_id, $brand_name, $brand_code)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO master_brand
									(BrandName,
									Active,
									BrandCode,								
									CreatedDate
									) 
								VALUES 
									('" . $brand_name . "',
									'0',
									'" . $brand_code . "',							
									'" . $this->get_current_time() . "'
									)");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create New User
     */
    public function createColor($user_id, $color_name, $color_code)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO master_color
									(ColorName,
									Active,
									ColorCode,								
									CreatedDate
									) 
								VALUES 
									('" . $color_name . "',
									'0',
									'" . $color_code . "',							
									'" . $this->get_current_time() . "'
									)");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function createPaymentFasapay($fp_paidto, $fp_paidby, $fp_amnt, $fp_fee_amnt, $fp_fee_mode, $fp_total, $fp_currency, $fp_batchnumber, $fp_store, $fp_timestamp, $fp_merchant_ref, $fp_hash, $fp_hash_2, $trx_id, $fp_custom)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO fasapay
                                                                          (fp_paidto,
                                                                           fp_paidby,
                                                                           fp_amnt,
                                                                           fp_fee_amnt,
                                                                           fp_fee_mode,
                                                                           fp_total,
                                                                           fp_currency,
                                                                           fp_batchnumber,
                                                                           fp_store,
                                                                           fp_timestamp,
                                                                           fp_merchant_ref,
                                                                           fp_hash,
                                                                           fp_hash_2,
                                                                           trx_id,
                                                                           fp_custom
                                                                           )
                                                                          VALUES
                                                                          ('" . $fp_paidto . "',
                                                                           '" . $fp_paidby . "',
                                                                           '" . $fp_amnt . "',
                                                                          '" . $fp_fee_amnt . "',
                                                                           '" . $fp_fee_mode . "',
                                                                           '" . $fp_total . "',
                                                                           '" . $fp_currency . "',
                                                                           '" . $fp_batchnumber . "',
                                                                           '" . $fp_store . "',
                                                                           '" . $fp_timestamp . "',
                                                                           '" . $fp_merchant_ref . "',
                                                                           '" . $fp_hash . "',
                                                                           '" . $fp_hash_2 . "',
                                                                            '" . $trx_id . "',
                                                                           '" . $fp_custom . "'
                                                                           
                                                                           )");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create New User
     */
    public function createVariant($user_id, $color_name, $color_code)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO variant
									(
									UserID,
									ColorName,
									ColorCode
									) 
								VALUES 
									(
									'" . $user_id . "',
									'" . $color_name . "',
									'" . $color_code . "'
									)");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create New User
     */
    public function createDetailVariant($user_id, $size_name, $size_code)
    {

        $insert = $this
            ->conn
            ->query("insert into detail_variant(ColorName,  SizeName , SizeCode , Stock) 
		select ColorName, '" . $size_name . "',  '" . $size_code . "' , '1' 
		from variant WHERE NOT EXISTS (SELECT ColorName from detail_variant WHERE SizeName = '" . $size_name . "'");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create New User
     */
    public function createSize($user_id, $size_name, $size_code)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO master_size
									(SizeName,
									Active,
									SizeCode,								
									CreatedDate
									) 
								VALUES 
									('" . $size_name . "',
									'0',
									'" . $size_code . "',							
									'" . $this->get_current_time() . "'
									)");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get user data by email
     */
    public function getDataTokoByPhone($phone)
    {

        $check = $this->checkTokoRegister($phone);
        if ($check)
        {

            $query_get = $this
                ->conn
                ->query("SELECT 
                                                   *
                                          FROM 
                                                    master_toko	
                                         
                                          WHERE 
                                          Phone = '" . $phone . "' 
                                          AND Active=1");
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check if user exist
     */
    public function checkTokoRegister($phone)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_toko WHERE Phone = '" . $phone . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Pending Order Nurse
     */
    public function processPendingOrder($user_id, $latitude, $longitude, $location, $category_id, $notes, $total_price, $company_fee_percent, $unique_code = '')
    {

        $order_no = 0;

        //Count Transport Fare
        $transport_price = 0;

        //Count Percentage
        $companyRevenuePercent = $company_fee_percent;
        $nurseRevenuePercent = 100 - $companyRevenuePercent;
        $companyRevenue = $this->countPercentage($total_price, $companyRevenuePercent);
        $nurseRevenue = $total_price - $companyRevenue;

        //Lets process
        $exist = $this->checkPendingOrder($user_id);
        if ($exist)
        {
            //Order Pending Exist
            $update = $this
                ->conn
                ->query("UPDATE nrz_orders_current SET 
											OrderNo 		= '" . $order_no . "',
											Latitude 		= '" . $latitude . "',
											Longitude 		= '" . $longitude . "',
											Location 		= '" . $location . "',
											CategoryID 		= '" . $category_id . "',
											Notes 			= '" . $notes . "',
											TotalPrice		= '" . $total_price . "',
											TransportPrice	= '" . $transport_price . "',
											TotalPayment 	= '" . $total_price . "',
											OrderDate		= '" . $this->get_current_time() . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											UserID = '" . $user_id . "' AND OrderStatusID=1 AND Active=1 ");

            if ($update)
            {
                $exist = $exist->fetch_assoc();
                //create order log
                $order_id = $exist['OrderID'];
                $order_status_id = 1;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = 0;
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);

                $this->sendJobOffer($user_id, $category_id, $latitude, $longitude);
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            //Create New Order
            $insert = $this
                ->conn
                ->query("INSERT INTO nrz_orders_current 
										(OrderNo,
										OrderDate,
										UserID,
										Latitude,
										Longitude,
										Location,
										CategoryID,
										Notes,
										TotalPrice,
										TransportPrice,
										TotalPayment,
										CompanyRevenue,
										NurseRevenue,
										CompanyRevenuePercent,
										NurseRevenuePercent,
										CreatedDate,
										UniqueCode
										) 
									VALUES 
										('" . $order_no . "',
										'" . $this->get_current_time() . "',
										'" . $user_id . "',
										'" . $latitude . "',
										'" . $longitude . "',
										'" . $location . "',
										'" . $category_id . "',
										'" . $notes . "',
										'" . $total_price . "',
										'" . $transport_price . "',
										'" . $total_price . "',
										'" . $companyRevenue . "',
										'" . $nurseRevenue . "',
										'" . $companyRevenuePercent . "',
										'" . $nurseRevenuePercent . "',
										'" . $this->get_current_time() . "',
										'" . $unique_code . "'
										) ");
            // echo "INSERT INTO nrz_orders_current
            // 						(OrderNo,
            // 						OrderDate,
            // 						UserID,
            // 						Latitude,
            // 						Longitude,
            // 						Location,
            // 						CategoryID,
            // 						Notes,
            // 						TotalPrice,
            // 						TransportPrice,
            // 						TotalPayment,
            // 						CompanyRevenue,
            // 						NurseRevenue,
            // 						CompanyRevenuePercent,
            // 						NurseRevenuePercent,
            // 						CreatedDate,
            // 						UniqueCode
            // 						)
            // 					VALUES
            // 						('".$order_no."',
            // 						'".$this->get_current_time()."',
            // 						'".$user_id."',
            // 						'".$latitude."',
            // 						'".$longitude."',
            // 						'".$location."',
            // 						'".$category_id."',
            // 						'".$notes."',
            // 						'".$total_price."',
            // 						'".$transport_price."',
            // 						'".$total_price."',
            // 						'".$companyRevenue."',
            // 						'".$nurseRevenue."',
            // 						'".$companyRevenuePercent."',
            // 						'".$nurseRevenuePercent."',
            // 						'".$this->get_current_time()."',
            // 						'".$unique_code."'
            // 						) ";
            if ($insert)
            {
                //create order log
                $order_id = $this
                    ->conn->insert_id;
                $order_status_id = 1;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = 0;
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);

                // send push notif
                $this->sendJobOffer($user_id, $category_id, $latitude, $longitude);
                return true;

            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Check pending order
     */
    public function checkPendingOrderPharmacy($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM apt_orders WHERE UserID = '" . $user_id . "' AND AptOrderStatusID=1 AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Pending Order Pharmacy
     */
    public function processPendingOrderPharmacy($user_id, $longitude, $latitude, $location, $notes, $pharmacy_id = NULL)
    {

        $order_no = 0;

        //Count Transport Fare
        $transport_price = 0;

        //Lets process
        $exist = $this->checkPendingOrderPharmacy($user_id);
        // var_dump($exist->fetch_assoc());
        if ($exist)
        {

            //Order Pending Exist
            $update = $this
                ->conn
                ->query("UPDATE apt_orders SET 
											OrderNo 		= '" . $order_no . "',
											Latitude 		= '" . $latitude . "',
											Longitude 		= '" . $longitude . "',
											Longitude 		= '" . $longitude . "',
											Location 		= '" . $location . "',
											Notes 			= '" . $notes . "',
											OrderDate		= '" . $this->get_current_time() . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											UserID = '" . $user_id . "' AND AptOrderStatusID=1 AND Active=1 ");
            if ($update)
            {
                $exist = $exist->fetch_assoc();
                //create order log
                $order_id = $exist['AptOrderID'];
                $pharmacy_id = $exist['PharmacyID'];
                $order_status_id = 1;
                $description = 'Log Order Pharmacy, created by sistem api';
                $pharmacy_id = 0;
                //send push notif
                $this->sendJobOfferPharmacy($user_id, $latitude, $longitude);
                $this->createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description);

                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {

            $insert = $this
                ->conn
                ->query("INSERT INTO apt_orders 
										(OrderNo,
										OrderDate,
										UserID,
										Latitude,
										Longitude,
										Location,
										Notes,
										SubTotal,
										Transport,
										TotalPayment,
										CreatedDate
										) 
									VALUES 
										('" . $order_no . "',
										'" . $this->get_current_time() . "',
										'" . $user_id . "',
										'" . $latitude . "',
										'" . $longitude . "',
										'" . $location . "',
										'" . $notes . "',
										'0',
										'0',
										'0',
										'" . $this->get_current_time() . "'
										) ");

            if ($insert)
            {
                //create order log
                $order_id = $this
                    ->conn->insert_id;
                $order_status_id = 1;
                $description = 'Log Order Pharmacy, created by sistem api';
                $pharmacy_id = 0;
                //send push notif
                $this->sendJobOfferPharmacy($user_id, $latitude, $longitude);
                $this->createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description);

                return true;

            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Process Pending Order Pharmacy
     */
    //    public function processTrimaOrderPharmacy($order_id, $user_id)
    //    {
    //
    //        $order_no = 1;
    //
    //        //Count Transport Fare
    //        $transport_price = 0;
    //
    //
    //        // var_dump($exist->fetch_assoc());
    //        if ($exist) {
    //            //Order Pending Exist
    //            $update = $this->conn->query("UPDATE apt_orders SET
    //											OrderNo 		= '" . $order_no . "',
    //											AptOrderStatusID 		= '" . $order_status_id . "'
    //										WHERE
    //											UserID = '" . $user_id . "' AND AptOrderID= '" . $order_id . "'Active=1 ");
    //            if ($update) {
    //                $exist = $exist->fetch_assoc();
    //                //create order log
    //                //$order_id = $exist['AptOrderID'];
    //                $order_status_id = 3;
    //                $description = 'Log Order Pharmacy, created by sistem api';
    //                $pharmacy_id = 0;
    //                //$this->createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description);
    //
    //                //send push notif
    //                $this->sendTerimaPharmacy($user_id);
    //                return true;
    //            } else {
    //                return false;
    //            }
    //
    //        }
    //
    //    }
    
    /**
     * Process Cancel Order
     */

    public function updateStokBySKU($variant_details)

    {

        $obj = json_decode($variant_details, true);

        //print_r ($variant_details);die;
        foreach ($obj as $item)
        {

            $update = $this
                ->conn
                ->query("UPDATE product_variant_details SET 
										Stock = Stock - '" . $item['qty'] . "'
									WHERE 
										SkuID = '" . $item['sku'] . "'");

        }

        if ($update)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function deleteCartDetailByUser($user_id, $cart_id)
    {

        $delete = $this
            ->conn
            ->query("delete from  cart
									WHERE 
										UserID = '" . $user_id . "' and CartID = '" . $cart_id . "'  ");

        $update = $this
            ->conn
            ->query("delete from  cart_details
								WHERE 
										UserID = '" . $user_id . "' and CartID = '" . $cart_id . "'  ");

        if ($update)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get Nurse Data From Order ID
     */
    public function getPharmacyByOrderID($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.AptOrderID,
											b.FirstName,
											b.LastName,
											b.PharmacyID
										FROM apt_orders a
										INNER JOIN apt_users b ON a.PharmacyID = b.PharmacyID 
										WHERE a.Active=1 AND a.AptOrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check pending order
     */
    public function checkPharmacyPendingOrder($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM apt_orders WHERE UserID = '" . $user_id . "' AND AptOrderStatusID=1 AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Order Logs
     */
    public function createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description)
    {
        $q = $this
            ->conn
            ->query("INSERT INTO apt_order_logs 
									(AptOrderID,
									CreatedDate,
									AptOrderStatusID,
									PharmacyID,
									Description
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'" . $order_status_id . "',
									'" . $pharmacy_id . "',
									'" . $description . "'
									) ");
        if ($q)
        {
            return $this
                ->conn->insert_id;
        }
        else
        {
            return false;
        }
    }

    public function sendNotif_PatientAccept($order_id, $user_id)
    {

        //$row = $query->fetch_assoc();
        $custom_data = array(
            'type' => '92',
            'body' => "Ada Patient menerima tawaran anda",
            'title' => "Order Anda",

            "url" => "",
            'AptOrderID' => $order_id

        );

        // var_dump($custom_data);
        //Notify Patient
        $query_nrz = $this
            ->conn
            ->query("SELECT a.AptOrderID, b.FirebaseID FROM apt_orders a INNER JOIN master_users b ON b.UserID=a.UserID WHERE a.AptOrderID = '" . $order_id . "' ");

        if (mysqli_num_rows($query_nrz) > 0)
        {
            $row_nrz = $query_nrz->fetch_assoc();
            $this->sendNotification_Pharmacy($row_nrz['FirebaseID'], $custom_data);
        }

    }

    /**
     * Process Order Offer
     */
    public function saveOrderPharmacy($order_id, $user_id, $total_payment, $apt_user_id)
    {

        //Lets process
        //$exist = $this->checkOrderOffer($order_id, $pharmacy_id);
        //
        //if ($exist!=null) {
        //Order Offer Exist
        $order_status_id = 3;
        $update = $this
            ->conn
            ->query("UPDATE apt_orders SET 
											AptOrderStatusID 		= '" . $order_status_id . "',
											TotalPayment = '" . $total_payment . "',
											AptUserID = '" . $apt_user_id . "'
											
										WHERE 
											AptOrderID = '" . $order_id . "'  and UserID = '" . $user_id . "'");

        if ($update)
        {
            //$data = $exist->fetch_assoc();
            //create details
            //$this->processDetailOffer($data['OrderOfferID'], $details);
            //create order log
            // $order_id = $data['AptOrderID'];
            $order_status_id = 3;
            //$this->createOrderLog($order_id, $order_status_id, $pharmacy_id);
            //send notif to patient
            $this->sendNotif_PatientAccept($order_id, $user_id);
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * Get Order Offer Detail
     */
    public function getOrderPatientDetail($user_id, $order_id)
    {

        $query_get = $this
            ->conn
            ->query("select 
                                            b.Name, 
                                            b.Address as Location , 
                                            a.Location as 'Address', 
                                            a.AptUserID, 
                                            a.AptOrderID,
                                            d.MedicineID, 
                                            CASE
                                                WHEN e.MedicineName IS NULL THEN
                                                e.NamaAlkes ELSE e.MedicineName 
                                            END AS `MedicineName`, 
                                            d.Price as 'PriceMedicine', 
                                            a.Transport, 
                                            a.TotalPayment , 
                                            (select Jumlah from apt_order_detail where apt_order_detail.AptOrderID = '" . $order_id . "' AND apt_order_detail.MedicineID =d.MedicineID) as Jumlah ,
                                            IFNULL(a.UniqueCode, 0) as 'UniqueCode', 
                                            a.SubTotal,
                                            a.UserID, 
                                            a.AptOrderStatusID, 
                                            a.Notes,
                                            a.PaymentTypeID ,
                                            a.PharmacyID, 
                                            a.OrderNo ,
                                            g.PaymentType,
                                            h.StatusName,
                                            a.NoResi,
                                            j.JasaPengiriman,
                                            a.voucher_code,
                                            a.nominal,
                                            (CASE WHEN a.voucher_code = '0' THEN null WHEN a.voucher_code = '' THEN null ELSE a.voucher_code END) as voucher_code,
											(CASE WHEN a.nominal = '0' THEN null WHEN a.nominal = '' THEN null ELSE a.nominal END) as nominal	
                                            from
                                            apt_orders as a 
                                            LEFT JOIN apt_pharmacies b ON b.PharmacyID = a.PharmacyID
                                            LEFT JOIN apt_order_offers c ON c.AptOrderID = a.AptOrderID
                                            LEFT JOIN apt_order_offer_detail d ON d.OrderOfferID = c.OrderOfferID
                                            LEFT JOIN apt_medicines e ON e.MedicineID = d.MedicineID
                                            LEFT JOIN master_payment_type g on g.PaymentTypeID = a.PaymentTypeID
                                            LEFT JOIN apt_order_status h on h.AptOrderStatusID = a.AptOrderStatusID
                                            LEFT JOIN jasa_pengiriman j ON j.JasaPengirimanID = a.JasaPengiriman 


											WHERE 
											
												a.UserID =  '" . $user_id . "' AND
												a.Active = 1 and a.AptOrderID =  '" . $order_id . "'
											");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    //Order Offer to Pharmacy
    public function sendJobOfferPharmacy($user_id, $latitude, $longitude)
    {
        //Get ID
        $order_id = $this->getPharmacyPendingOrderID($user_id);

        $custom_data = array(
            'type' => '91', //Job Offer Pharmacy
            'body' => "Hi, ada tawaran order nih ",
            'title' => "Tawaran Order",
            'OrderID' => $order_id
        );

        //Notify Online Pharmacy, within 30KM around
        $query_nrz = $this
            ->conn
            ->query("SELECT a.*, a.Active,b.Active,c.Active,c.Verified,
												(3959 * acos(cos(radians(" . $latitude . "))*cos(radians(c.Latitude))*cos(radians(c.Longitude)-radians(" . $longitude . ")) + sin(radians(" . $latitude . "))*sin(radians(c.Latitude)))) AS distance
											FROM apt_users a
											INNER JOIN apt_user_location b ON b.AptUserID = a.AptUserID
											INNER JOIN apt_pharmacies c ON c.PharmacyID = a.PharmacyID
											WHERE b.Active = 1
											HAVING 
												distance <= 30  AND distance > 0");

        if (mysqli_num_rows($query_nrz) > 0)
        {

            while ($row_nrz = $query_nrz->fetch_assoc())
            {
                //$this->sendNotification_Pharmacy($row_nrz['FirebaseID'], $custom_data);
                $this->sendNotification_Pharmacy($row_nrz['FirebaseID'], $custom_data);
            }

        }

    }

    /**
     * Get pending order ID Pharmacy
     */
    public function getPharmacyPendingPharmacyID($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * from apt_orders WHERE UserID = '" . $user_id . "'  AND Active=1 LIMIT 1");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['PharmacyID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get pending order ID Pharmacy
     */
    public function getCurrentOrderID($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT order_id FROM history_orders WHERE user_id = '" . $user_id . "' ORDER BY order_id DESC LIMIT 1");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['order_id'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get pending order ID Pharmacy
     */
    public function getPharmacyPendingOrderID($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * from apt_orders WHERE UserID = '" . $user_id . "'  AND AptOrderStatusID = 1 AND Active=1 LIMIT 1");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['AptOrderID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Function Send GCM to Pharmacy
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Pharmacy($firebase_id, $custom_data)
    {

        $registrationIds = array(
            $firebase_id
        );

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );

        $headers = array(
            'Authorization: key=AAAAvDmzVp8:APA91bG1hwgkf10mYwAtax_n1NOKFSDMCzxnzZXG_BxL5TxJn8hHM6ywdCnvf0Gg6bJAqGhaD_wk_PUS0i3xTZRC2WCmeVZnFODU2JB7CknAdE_oDGYsz5GWoNQ_D-m7rbOUVycJ_6_3',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

    }

    /**
     * Process Detail Order Pharmacy
     */
    public function processPharmacyDetailOrder($medicine_id, $get_id, $jumlah)
    {
        //print_r($medicine_id);die();
        //delete first
        $del = $this
            ->conn
            ->query("DELETE FROM apt_order_detail WHERE AptOrderID='" . $get_id . "' ");

        //process insert batch
        $sql = array();

        $m = new MultipleIterator();
        $m->attachIterator(new ArrayIterator($medicine_id) , 'medicine');
        $m->attachIterator(new ArrayIterator($jumlah) , 'jumlah');

        foreach ($m as $item)
        {
            //print_r($item[0]);die();
            $sql[] = '("' . $item[0] . '", ' . $get_id . ' , "' . $item[1] . '")';
        }
        //print_r($sql);die();
        // foreach ($medicine_id as $row) {
        // $sql[] = '("' . $row . '", ' . $get_id . ' , ' .$jumlah . ')';
        // }
        // print_r($sql);die();
        $ins = $this
            ->conn
            ->query("INSERT INTO apt_order_detail (MedicineID, AptOrderID, Jumlah) VALUES " . implode(',', $sql) . "");
        if ($ins)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Pending Order Doctor
     */
    public function processPendingOrderDoctor($user_id, $latitude, $longitude, $location, $category_id, $notes, $price_from, $price_to)
    {

        $order_no = 0;

        //check doctor in range price
        // $q = $this->conn->query("SELECT * FROM doc_doctors WHERE Price >= $price_from AND price <= $price_to ");
        // 	if(mysqli_num_rows($q)){
        // }
        $exist = $this->checkPendingOrderDoctor($user_id);
        if ($exist)
        {
            //Order Pending Exist
            $query = $this
                ->conn
                ->query("UPDATE doc_orders_current SET 
											OrderNo 		= '" . $order_no . "',
											Latitude 		= '" . $latitude . "',
											Longitude 		= '" . $longitude . "',
											Location 		= '" . $location . "',
											CategoryID 		= '" . $category_id . "',
											Notes 			= '" . $notes . "',
											RangePriceFrom	= '" . $price_from . "',
											RangePriceTo	= '" . $price_to . "',
											OrderDate		= '" . $this->get_current_time() . "',
											ModifiedDate	= '" . $this->get_current_time() . "'
										WHERE 
											UserID = '" . $user_id . "' AND OrderStatusID=1 AND Active=1 ");

            if ($query)
            {
                $exist = $exist->fetch_assoc();
                //create order log
                $order_id = $exist['OrderID'];
                $order_status_id = 1;
                $description = 'Log Order Doctor, created by sistem api';
                $doctor_id = 0;
                $this->createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description);

                // send push notif
                $this->sendJobOfferDoctor($user_id, $category_id, $price_from, $price_to);
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            //Create New Order
            $query = $this
                ->conn
                ->query("INSERT INTO doc_orders_current 
										(OrderNo,
										OrderDate,
										UserID,
										Latitude,
										Longitude,
										Location,
										CategoryID,
										Notes,
										RangePriceFrom,
										RangePriceTo,
										CreatedDate
										) 
									VALUES 
										('" . $order_no . "',
										'" . $this->get_current_time() . "',
										'" . $user_id . "',
										'" . $latitude . "',
										'" . $longitude . "',
										'" . $location . "',
										'" . $category_id . "',
										'" . $notes . "',
										'" . $price_from . "',
										'" . $price_to . "',
										'" . $this->get_current_time() . "'
										) ");

            if ($query)
            {
                //create order log
                $order_id = $this
                    ->conn->insert_id;
                $order_status_id = 1;
                $description = 'Log Order Doctor, created by sistem api';
                $doctor_id = 0;
                $this->createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description);

                // send push notif
                $this->sendJobOfferDoctor($user_id, $category_id, $price_from, $price_to);
                return true;

            }
            else
            {
                return false;
            }
        }

        // if ($query) {
        // 	$this->sendJobOfferDoctor($user_id, $category_id, $price_from, $price_to);
        // 	return true;
        // } else {
        // 	return false;
        // }
        
    }

    //Job Offer Nurse
    public function sendJobOffer($user_id, $category_id, $latitude, $longitude)
    {
        //Get ID
        $order_id = $this->getPendingOrderID($user_id);

        $custom_data = array(
            'type' => '1', //Job Offer Nurse
            'body' => "Hi, ada tawaran pekerjaan nih",
            'title' => "Tawaran Pekerjaan",
            'OrderID' => $order_id
        );

        //Notify Online Nurses, within 10KM around
        // distance <= 10 AND  before
        $query_nrz = $this
            ->conn
            ->query("SELECT a.*, b.Active,
												(3959 * acos(cos(radians(" . $latitude . "))*cos(radians(b.Latitude))*cos(radians(b.Longitude)-radians(" . $longitude . ")) + sin(radians(" . $latitude . "))*sin(radians(b.Latitude)))) AS distance
											FROM nrz_nurses a
											INNER JOIN nrz_nurse_location b ON b.NurseID = a.NurseID
											HAVING 
												distance <= 10 AND 
												a.CategoryID='" . $category_id . "' AND 
												a.FirebaseID IS NOT NULL AND 
												a.Active=1 AND 
												b.Active=1 AND 
												a.Verified=1 ");
        if (mysqli_num_rows($query_nrz) > 0)
        {

            while ($row_nrz = $query_nrz->fetch_assoc())
            {
                //check nurse bill reach maximum limit
                $nurse_max_bill = $this->checkNurseBillReachMaximum($row_nrz['NurseID']);
                if (!$nurse_max_bill)
                {
                    $this->sendNotification_Nurse($row_nrz['FirebaseID'], $custom_data);
                }
            }

        }
    }

    //Job Offer Doctor
    public function sendJobOfferDoctor($user_id, $category_id, $price_from, $price_to)
    {
        //Get ID
        $order_id = $this->getDoctorPendingOrderID($user_id);

        $custom_data = array(
            'type' => '31', //Job Offer Doctor
            'body' => "Hi, ada tawaran pekerjaan baru",
            'title' => "Tawaran Pekerjaan",
            'OrderID' => $order_id
        );

        //Notify Online Doctors
        //        $query_nrz = $this->conn->query("SELECT a.* FROM doc_doctors a
        //											INNER JOIN doc_doctor_location b ON b.DoctorID = a.DoctorID
        //											WHERE a.CategoryID='" . $category_id . "' AND a.IsLogin = 1 AND a.FirebaseID IS NOT NULL AND a.Active=1 AND b.Active = 1 AND a.Verified=1 AND a.Price >= " . $price_from . " AND a.Price <= " . $price_to . "");
        $query_nrz = $this
            ->conn
            ->query("SELECT a.* FROM doc_doctors a
											INNER JOIN doc_doctor_location b ON b.DoctorID = a.DoctorID
											WHERE a.CategoryID='" . $category_id . "' AND a.IsLogin = 1 AND a.FirebaseID IS NOT NULL AND a.Active=1 AND b.Active = 1 AND a.Verified=1");

        if (mysqli_num_rows($query_nrz) > 0)
        {
            while ($row_nrz = $query_nrz->fetch_assoc())
            {
                $this->sendNotification_Doctor($row_nrz['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Process Detail Order
     */
    public function processDetailOrder($action_id, $order_id)
    {

        //delete first
        $del = $this
            ->conn
            ->query("DELETE FROM nrz_orders_detail WHERE OrderID='" . $order_id . "' ");

        //process insert batch
        $sql = array();

        foreach ($action_id as $row)
        {
            $sql[] = '("' . $row . '", ' . $order_id . ')';
        }

        $ins = $this
            ->conn
            ->query('INSERT INTO nrz_orders_detail (ActionID, OrderID) VALUES ' . implode(',', $sql));
        if ($ins)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * Process Detail Order
     */
    public function processVariant($product_variants, $product_id)
    {

        $dec = json_decode($product_variants);

        for ($idx = 0;$idx < count($dec);$idx++)
        {
            $obj = (Array)$dec[$idx];
            //echo $obj["ProductVariantID"];die;
            //      $sql[] = '( ' .$obj["ProductVariantID"] . ' ,' .$obj["UserID"] . ' , ' . $product_id . ' , "' .$obj["ProductVariantName"] . '")';
            $sql[] = '( ' . $obj["UserID"] . ' , ' . $product_id . ' , "' . $obj["ProductVariantName"] . '")';
        }

        //        $ins = $this->conn->query('INSERT INTO product_variants (ProductVariantID , UserID , ProductID , ProductVariantName) VALUES ' . implode(',', $sql));
        $ins = $this
            ->conn
            ->query('INSERT INTO product_variants (UserID , ProductID , ProductVariantName) VALUES ' . implode(',', $sql));
        if ($ins)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    public function processCartDetails($product_id, $quantity, $product_variant_id, $product_variant_detail_id, $cart_id)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO cart_details
                                                                     (ProductID,
                                                                      Quantity,
                                                                      ProductVariantID,
                                                                      ProductVariantDetailID,
                                                                      CartID,
                                                                      CreatedDate
                                                                      
                                                                      )
                                                                     VALUES
                                                                     ('" . $product_id . "',
                                                                      '" . $quantity . "',
                                                                      '" . $product_variant_id . "',
                                                                      '" . $product_variant_detail_id . "',
                                                                      '" . $cart_id . "',
                                                                      '" . $this->get_current_time() . "'
                                                                      )");

        if ($insert)
        {

            return true;
        }
        else
        {
            return false;
        }

    }

    public function processImageProducts($image_products, $product_id)
    {

        $dec = json_decode($image_products);

        for ($idx = 0;$idx < count($dec);$idx++)
        {
            $obj = (Array)$dec[$idx];
            //echo $obj["ProductVariantID"];die;
            $sql[] = '( ' . $obj["UserID"] . ' , ' . $product_id . ' , "' . $obj["ImageProductName"] . '")';
        }

        $ins = $this
            ->conn
            ->query('INSERT INTO image_products (UserID , ProductID , ImageProductName) VALUES ' . implode(',', $sql));
        if ($ins)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    public function processImageProductVariants($image_product_variants, $variant_id)
    {

        $dec = json_decode($image_product_variants);

        for ($idx = 0;$idx < count($dec);$idx++)
        {
            $obj = (Array)$dec[$idx];
            //echo $obj["ProductVariantID"];die;
            $sql[] = '( ' . $obj["UserID"] . ' , ' . $variant_id . ' , "' . $obj["ImageProductVariantName"] . '")';
        }

        $ins = $this
            ->conn
            ->query('INSERT INTO image_product_variants (UserID , ProductVariantID , ImageProductVariantName) VALUES ' . implode(',', $sql));
        if ($ins)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * Process Detail Order
     */
    public function processVariantDetail($product_id, $variant_id, $product_variant_details)
    {
        $dec = json_decode($product_variant_details);

        for ($idx = 0;$idx < count($dec);$idx++)
        {
            $obj = (Array)$dec[$idx];
            $sql[] = '( ' . $obj["UserID"] . ' , ' . $product_id . ' ,' . $variant_id . ' , "' . $obj["ProductVariantDetailName"] . '" , "' . $obj["Price"] . '" ,   "' . $obj["PriceSell"] . '" , "' . $obj["Stock"] . '"
     , "' . $obj["SKU"] . '", "' . $obj["Barcode"] . '")';
        }

        $ins = $this
            ->conn
            ->query('INSERT INTO product_variant_details (UserID , ProductID, ProductVariantID , ProductVariantDetailName,
                              Price , PriceSell , Stock , SKU , Barcode) VALUES ' . implode(',', $sql));
        if ($ins)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * Get user data by email
     */
    public function getUserByEmail($email)
    {

        $check = $this->checkUserRegister($email);
        if ($check)
        {
            // $query_get = $this->conn->query("SELECT *, '' AS Password FROM master_users WHERE Phone = '".$phone."' AND Active=1 ");
            // edit by elim
            $query_get = $this
                ->conn
                ->query("SELECT 
                                               a.UserID,
											   a.TokoID,
											   a.FirstName,
											   a.LastName,
											   a.Email,
											   a.Password,
											   a.LevelID,
											   a.Token,
											   a.FirebaseID,
											   a.FirebaseTime,
											   a.DeviceBrand,
											   a.DeviceModel,
											   a.DeviceSerial,
											   a.DeviceOS,
											   a.ReferralBy,
											   a.GoogleUserID,
											   b.TokoName,
											   b.Address,
											   b.Phone
                                          FROM 
                                                    users a	
                                         LEFT JOIN master_toko b ON a.TokoID = b.TokoID
                                         
                                          WHERE 
                                          a.Email = '" . $email . "' 
                                          AND a.Active=1");
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get user data by phone
     */
    public function getEmergencyContactByUserPhone($phone)
    {

        $check = $this->checkUserRegister($phone);
        if ($check)
        {
            $query_get = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE Phone = '" . $phone . "' AND Active=1 ");

            $user_id = $query_get->fetch_assoc();

            $UserID = $user_id['UserID'];

            $query_get_emergency = $this
                ->conn
                ->query("SELECT * FROM master_patients WHERE UserID = '" . $UserID . "' AND Emergency = 1");

            return $query_get_emergency;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get nurse categories
     */
    public function getNurseCategories()
    {
        $query_get = $this
            ->conn
            ->query("SELECT * FROM nrz_categories WHERE Active = 1");
        return $query_get;
    }

    /**
     * Get Bank Account
     */
    public function getPaymentType($method = null)
    {
        $filter = "";
        if ($method == 1)
        { //doctor
            $filter = " AND PaymentTypeID NOT IN (1)";
        }
        $query_get = $this
            ->conn
            ->query("SELECT *, '' AS Icon FROM master_payment_type WHERE Active = 1" . $filter);
        return $query_get;
    }

    /**
     * Get doctor categories
     */
    public function getDoctorCategories()
    {
        $query_get = $this
            ->conn
            ->query("SELECT CategoryID, Description AS CategoryName FROM doc_categories WHERE Active = 1");
        return $query_get;
    }

    /**
     * Get doctor categories
     */
    public function getMessageSetting($user_id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT MessageName  FROM message WHERE UserID = '" . $user_id . "'");
        return $query_get;
    }

    public function loadData($user_id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT   
											a.MessageName,
											b.Phone
										
										FROM message a
										INNER JOIN contact b ON a.UserID = b.UserID
										WHERE a.Active=1 ");
        return $query_get;
    }
    /**
     * Get banners
     */
    public function getBanners()
    {
        $query_get = $this
            ->conn
            ->query("SELECT BannerID, Title, Caption, App, CONCAT( '" . $this->uploaddir . "', '/banners/', BannerID,'.jpg') AS Url FROM master_banners WHERE App='3' AND Active = 1");
        return $query_get;
    }

    /**
     * Get Profile
     */
    public function getProfile($user_id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT * FROM users WHERE UserID = '" . $user_id . "' and  Active = 1");
        return $query_get;
    }

    /**
     * Get articles
     */
    public function getArticles($num = null)
    {
        $limit = "";
        if ($num != null)
        {
            $limit = " LIMIT " . $num;
        }
        $query_get = $this
            ->conn
            ->query("SELECT PublishedDate, ArticleID, Title, Caption, CreatedDate, CONCAT('" . $this->uploaddir . "', '/articles/', ArticleID,'.jpg') AS Url FROM master_articles WHERE Active = 1 AND TypeID = 3
			ORDER BY PublishedDate DESC " . $limit);
        return $query_get;
    }

    /**
     * Get articles
     */
    public function getArticleDetail($id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT ArticleID, Title, Caption, Content AS Description, CreatedDate, CONCAT( '" . $this->uploaddir . "', '/articles/', ArticleID,'.jpg') AS Url, Source FROM master_articles WHERE Active = 1 AND ArticleID = " . $id);
        return $query_get;
    }

    /**
     * Get featured promo
     */
    public function getFeaturedPromos()
    {
        $query_get = $this
            ->conn
            ->query("SELECT
                                        PromoID,
                                        Title,
                                        Harga,
                                        Stock,
                                        (
                                        CASE
                                                
                                                WHEN OwnerKategori = 'apt_pharmacies' THEN
                                                \"Pharmacy\"
                                                WHEN OwnerKategori = 'master_hospitals' THEN
                                                    \"Hospital\"
                                                WHEN OwnerKategori = 'lab_laboratoriums' THEN
                                                    \"Laboratorium\"
                                                ELSE \"The quantity is something else\" 
                                            END 
                                            ) AS OwnerKategori,
                                        OwnerID,
                                        CreatedDate,
                                        (
                                        CASE
                                                
                                                WHEN OwnerKategori = 'apt_pharmacies' THEN
                                                ( SELECT apt_pharmacies.`Name` FROM apt_pharmacies WHERE PharmacyID = OwnerID ) 
                                                WHEN OwnerKategori = 'master_hospitals' THEN
                                                ( SELECT master_hospitals.HospitalName FROM master_hospitals WHERE HospitalID = OwnerID ) 
                                                WHEN OwnerKategori = 'lab_laboratoriums' THEN
                                                ( SELECT lab_laboratoriums.`Name` FROM lab_laboratoriums WHERE LabID = OwnerID ) 
                                                ELSE \"The quantity is something else\" 
                                            END 
                                            ) AS Owner 
                                        FROM
                                            master_promo 
                                        WHERE
                                            Active = 1 
                                            AND 
                                            IsFeaturedPromo = 1
                                    ORDER BY
                                        CreatedDate DESC");

        return $query_get;
    }

    /**
     * Get promo
     */
    public function getPromos()
    {
        $query_get = $this
            ->conn
            ->query("SELECT
                                        PromoID,
                                        Title,
                                        Harga,
                                        Stock,
                                        (
                                        CASE
                                                
                                                WHEN OwnerKategori = 'apt_pharmacies' THEN
                                                \"Pharmacy\"
                                                WHEN OwnerKategori = 'master_hospitals' THEN
                                                    \"Hospital\"
                                                WHEN OwnerKategori = 'lab_laboratoriums' THEN
                                                    \"Laboratorium\"
                                                ELSE \"The quantity is something else\" 
                                            END 
                                            ) AS OwnerKategori,
                                        OwnerID,
                                        CreatedDate,
                                        (
                                        CASE
                                                
                                                WHEN OwnerKategori = 'apt_pharmacies' THEN
                                                ( SELECT apt_pharmacies.`Name` FROM apt_pharmacies WHERE PharmacyID = OwnerID ) 
                                                WHEN OwnerKategori = 'master_hospitals' THEN
                                                ( SELECT master_hospitals.HospitalName FROM master_hospitals WHERE HospitalID = OwnerID ) 
                                                WHEN OwnerKategori = 'lab_laboratoriums' THEN
                                                ( SELECT lab_laboratoriums.`Name` FROM lab_laboratoriums WHERE LabID = OwnerID ) 
                                                ELSE \"The quantity is something else\" 
                                            END 
                                            ) AS Owner 
                                        FROM
                                            master_promo 
                                        WHERE
                                            Active = 1 
                                    ORDER BY
                                        CreatedDate DESC");

        return $query_get;
    }

    /**
     * Check pending order
     */
    public function checkPendingOrderPromo($promo_id, $user_id)
    {
        //cek yang status nya Pending
        $query = $this
            ->conn
            ->query("SELECT * FROM promo_order WHERE UserID = '" . $user_id . "' AND PromoID = '" . $promo_id . "' AND PromoOrderStatusID=1 AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Pending Order Promo
     */
    public function processPendingOrderPromo($promo_id, $user_id, $alamat_pengiriman, $transport, $total, $unique_code, $payment_type_id)
    {

        //Lets process
        $exist = $this->checkPendingOrderPromo($promo_id, $user_id);
        // var_dump($exist->fetch_assoc());
        //Create PromoOrderNo
        $q = $this
            ->conn
            ->query("SELECT IFNULL(MAX(Right(PromoOrderNo,8)),0) AS OrderNo
											FROM promo_order 
											WHERE 
												DATE_FORMAT(PromoOrderDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(PromoOrderDate, '%Y')='" . date('Y') . "'");

        $data_promo_orderid = $q->fetch_assoc();

        if ($data_promo_orderid['OrderNo'] == 0)
        {
            //Start From First
            $num = date('y') . date('m') . "0001";
            $order_no = "PR" . $num;
        }
        else
        {
            //Continue Number +1
            $num = $data_promo_orderid['OrderNo'] + 1;
            $order_no = "PR" . $num;
        }

        if ($exist)
        {
            //Order Pending Exist
            $update = $this
                ->conn
                ->query("UPDATE promo_order SET 
											PromoOrderNo 		        = '" . $order_no . "',
											UserID 		                = '" . $user_id . "',
											AlamatPengiriman 		    = '" . $alamat_pengiriman . "',
											Transport 		            = '" . $transport . "',
											TotalPayment 		        = '" . $total . "',
											KodeUnik 			        = '" . $unique_code . "',
											PaymentTypeID 			    = '" . $payment_type_id . "',
											PromoOrderDate		        = '" . $this->get_current_time() . "',
											ModifiedDate	            = '" . $this->get_current_time() . "'
										WHERE 
											UserID = '" . $user_id . "' 
											AND
											PromoID = '" . $promo_id . "' 
											AND PromoOrderStatusID=1 AND Active=1 ");
            if ($update)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $insert = $this
                ->conn
                ->query("INSERT INTO promo_order 
										(PromoOrderNo,
										PromoOrderDate,
										PromoID,
										UserID,
									    AlamatPengiriman,
										KodeUnik,
										Transport,
										TotalPayment,
										PaymentTypeID,
										CreatedDate,
										PromoOrderStatusID
										) 
									VALUES 
										('" . $order_no . "',
										'" . $this->get_current_time() . "',
										'" . $promo_id . "',
										'" . $user_id . "',
									    '" . $alamat_pengiriman . "',
										'" . $unique_code . "',
										'" . $transport . "',
										'" . $total . "',
										'" . $payment_type_id . "',
										'" . $this->get_current_time() . "',
										'1'
										) ");

            if ($insert)
            {

                return true;

            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Check if order promo exist
     */
    public function checkProductExist($product_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM product WHERE ProductID = '" . $product_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkProductBySKU($sku_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM product_variant_details WHERE SkuID = '" . $sku_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Payment Promo Status By User
     */
    public function processPaymentPromo($promo_order_id, $payment_type_id, $unique_code)
    {

        //Check If its free or no
        $check_price = $this
            ->conn
            ->query("SELECT * FROM promo_order WHERE PromoOrderID='" . $promo_order_id . "' ");
        $data_price = $check_price->fetch_assoc();
        $total_payment = $data_price['TotalPayment'];

        $myStatus = "2"; //Menunggu Pembayaran
        

        //Update
        $update = $this
            ->conn
            ->query("UPDATE promo_order SET 
										PromoOrderStatusID = '" . $myStatus . "',
										
										PaymentTypeID = '" . $payment_type_id . "',
										KodeUnik = " . $unique_code . ",
										TotalPayment = " . $total_payment . "
									WHERE 
										PromoOrderID = '" . $promo_order_id . "'");

        if ($update)
        {
            $dt = $this->getPromoByOrderID($promo_order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $promo_order_id = $promo_order_id;
                $order_status_id = $myStatus;
                $description = 'Log Order Promo, created by sistem api';
                $promo_id = $dt['PromoID'];
                $this->createOrderLogPromo($promo_order_id, $order_status_id, $promo_id, $description);
            }
            // $this->dealNotificationPharmacy($order_id);
            //$this->NoDealNotificationDoctor($order_id);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function createOrderLogPromo($promo_order_id, $order_status_id, $description)
    {
        $q = $this
            ->conn
            ->query("INSERT INTO promo_order_logs 
									(PromoOrderID,
									CreatedDate,
									CreatedBy,
									PromoOrderStatusID,
							
									Description
									) 
								VALUES 
									('" . $promo_order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
								
									'" . $description . "'
									) ");
        if ($q)
        {
            return $this
                ->conn->insert_id;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get Order Data Promo
     */
    public function getPromoOrderData($promo_order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.*,
											c.Firstname AS 'user_firstname',
											c.LastName AS 'user_lastname',
											c.Email AS 'user_email',
											d.OwnerKategori,
											d.OwnerID,
											d.Title
										FROM promo_order a
										LEFT JOIN master_users c ON c.UserID = a.UserID
										LEFT JOIN master_promo d ON d.PromoID = a.PromoID
										WHERE a.PromoOrderID = '" . $promo_order_id . "' AND a.Active = 1");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Doctor Data From Order ID
     */
    public function getPromoByOrderID($promo_order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.PromoOrderID,
											a.PromoID,
											b.FirstName,
											b.LastName,
											b.UserID,
											a.TotalPayment,
	                                        a.KodeUnik 
										FROM promo_order a
										INNER JOIN master_users b ON a.UserID = b.UserID
										WHERE a.Active=1 AND a.PromoOrderID='" . $promo_order_id . "' ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Data From AptOrder
     */
    public function getAptOrderByOrderID($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.AptOrderID,
											a.PharmacyID,
											a.SubTotal,
											a.Transport,
											a.TotalPayment,
	                                        a.UniqueCode, 
	                                        a.AptUserID, 
											b.FirstName,
											b.LastName,
											b.UserID
										FROM apt_orders a
										INNER JOIN master_users b ON a.UserID = b.UserID
										WHERE a.Active=1 AND a.AptOrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Process Cancel Order
     */

    public function deleteProduct($product_id)
    {
        //set PromoOrderStatusID to '6' means 'dibatalkan'
        $update = $this
            ->conn
            ->query("DELETE from products
									WHERE 
										ProductID = '" . $product_id . "'");

        if ($update)
        {
            // $dt = $this->getPromoByOrderID($promo_order_id);
            // if ($dt != null) {
            // $dt = $dt->fetch_assoc();
            // }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process terima barang
     * PromoOrderStatusID => 5 = selesai
     */

    public function terimaBarang($promo_order_id)
    {
        //set PromoOrderStatusID to '6' means 'dibatalkan'
        $update = $this
            ->conn
            ->query("UPDATE promo_order SET 
										PromoOrderStatusID = '5'
									WHERE 
										PromoOrderID = '" . $promo_order_id . "'");

        if ($update)
        {
            $dt = $this->getPromoByOrderID($promo_order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                $promo_order_id = $promo_order_id;
                $order_status_id = 5;
                $description = 'Log Order Pharmacy, created by sistem api';
                $promo_id = $dt['PromoID'];
                $this->createOrderLogPromo($promo_order_id, $order_status_id, $promo_id, $description);

                $user_id = $dt['UserID'];
                $user = $this->getUserByID($user_id);

                if ($user)
                {
                    $user = $user->fetch_assoc();

                    /*function point user*/
                    $amount_per_point = $this->getConfig('amount_nominal_per_point_user')
                        ->fetch_assoc();
                    $amount_per_point = $amount_per_point['Value'];
                    $total_point = 0;
                    $user_update = 0;
                    if ($amount_per_point != null && $amount_per_point != 0)
                    {
                        $point = (int)(($dt['TotalPayment'] - $dt['KodeUnik']) / $amount_per_point);

                        /*function udate user point*/
                        $deskripsi = 'Poin dari hasil Order Promo, created by system api';
                        $total_point = $user['Point'] + $point;
                        $user_update = $this->updateUserPoint($promo_order_id, $user_id, $total_point, $deskripsi);
                        if ($user_update)
                        {
                            $user_update = 1;
                        }
                        /*end function*/
                    }

                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process terima barang Pharmacy
     * PromoOrderStatusID => 6 = selesai
     */

    public function terimaBarangPharmacy($order_id)
    {
        //set AptOrderStatusID to '6' means 'Selesai'
        $update = $this
            ->conn
            ->query("UPDATE apt_orders SET 
										AptOrderStatusID = '6'
									WHERE 
										AptOrderID = '" . $order_id . "'");

        if ($update)
        {
            $dt = $this->getAptOrderByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                $order_id = $order_id;
                $order_status_id = 6;
                $description = 'Log Order Pharmacy, created by sistem api';
                $pharmacy_id = $dt['PharmacyID'];
                $this->createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description);

                $user_id = $dt['UserID'];
                $user = $this->getUserByID($user_id);

                if ($user)
                {
                    $user = $user->fetch_assoc();

                    /*function point user*/
                    $amount_per_point = $this->getConfig('amount_nominal_per_point_user')
                        ->fetch_assoc();
                    $amount_per_point = $amount_per_point['Value'];
                    $total_point = 0;
                    $user_update = 0;
                    if ($amount_per_point != null && $amount_per_point != 0)
                    {
                        $point = (int)(($dt['SubTotal']) / $amount_per_point);

                        /*function udate user point*/
                        $deskripsi = 'Poin dari hasil Order Pharmacy, created by system api';

                        $total_point = $user['Point'] + $point;
                        $user_update = $this->updateUserPoint($order_id, $user_id, $total_point, $deskripsi);
                        if ($user_update)
                        {
                            $user_update = 1;
                        }
                        /*end function*/
                    }

                }

                $custom_data = array(
                    'type' => '5',
                    'body' => "Barang Sudah diterima oleh pasien",
                    'title' => "Barang diterima pasien",
                    "url" => "",
                    'AptOrderID' => $order_id,
                    'PharmacyID' => $pharmacy_id,
                    'AptUserID' => $dt['AptUserID']

                );

                // var_dump($custom_data);
                //Notify Pharmacy
                $query_pharmacy = $this
                    ->conn
                    ->query("SELECT a.AptOrderID, b.FirebaseID FROM apt_orders a LEFT JOIN apt_users b ON b.AptUserID=a.AptUserID WHERE a.AptOrderID = '" . $order_id . "' ");

                if (mysqli_num_rows($query_pharmacy) > 0)
                {
                    $row_pharmacy = $query_pharmacy->fetch_assoc();
                    $this->sendNotification_Pharmacy($row_pharmacy['FirebaseID'], $custom_data);
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Function update point user
     * @param : $user_id, $user_total_point, $order_id, $amount
     * returns boolean
     */
    public function updateUserPoint($order_id, $user_id, $user_total_point, $description)
    {
        $q = $this
            ->conn
            ->query("UPDATE master_users SET
    							Point = " . $user_total_point . ",
								PointModifiedDate = '" . $this->get_current_time() . "'
								WHERE 
								UserID=" . $user_id . "");

        if ($q)
        {
            $q2 = $this->insertUserPointLog($order_id, $user_id, $user_total_point, $description);

            if ($q2)
            {
                return $q2;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * Function insert point user
     * @param : $order_id, $user_id, $amount, $description, $created_by = '9-', $modified_by = '9-'
     * returns boolean
     */
    public function insertUserPointLog($order_id, $user_id, $amount, $description, $created_by = '9-')
    {

        $q2 = $this
            ->conn
            ->query("INSERT INTO user_point_log
								(
									OrderID,
    								UserID,
    								Amount,
    								Description,
    								CreatedDate,
    								CreatedBy
								)VALUES(
									" . $order_id . ",
									" . $user_id . ",
									" . $amount . ",
									'" . $description . "',
									'" . $this->get_current_time() . "',
									'" . $created_by . "'								
								)");
        if ($q2)
        {
            return $this
                ->conn->insert_id;
        }
        else
        {
            return false;
        }
    }

    public function getProvinsi()
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_provinsi");
        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getKabupaten($provinsi)
    {
        $query = $this
            ->conn
            ->query("SELECT 
                                      master_kabupaten.KabupatenId, 
                                      master_kabupaten.kabupatenName 
                                      FROM 
                                      master_kabupaten 
                                      left join master_provinsi on master_provinsi.ProvinsiId = master_kabupaten.ProvinsiId
                                      WHERE
                                      master_kabupaten.ProvinsiId = " . $provinsi . "
                                      ");
        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getKecamatan($provinsi, $kabupaten)
    {
        $query = $this
            ->conn
            ->query("SELECT
                                        master_kecamatan.KecamatanId,
                                        master_kecamatan.KecamatanName 
                                    FROM
                                        master_kecamatan
                                        LEFT JOIN master_kabupaten ON master_kabupaten.KabupatenId = master_kecamatan.KabupatenId 
	                                    LEFT JOIN master_provinsi ON master_provinsi.ProvinsiId = master_kabupaten.ProvinsiId
                                    WHERE
                                        master_provinsi.ProvinsiId = " . $provinsi . "
                                            AND
                                        master_kecamatan.KabupatenId = " . $kabupaten . "
                                      ");
        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getKelurahan($provinsi, $kabupaten, $kecamatan)
    {
        $query = $this
            ->conn
            ->query("SELECT
                                            master_kelurahan.KelurahanId,
                                            master_kelurahan.KelurahanName,
                                            master_kelurahan.KodePos                                             
                                        FROM
                                            master_kelurahan
                                            LEFT JOIN master_kecamatan ON master_kecamatan.KecamatanId = master_kelurahan.KecamatanId
                                            LEFT JOIN master_kabupaten ON master_kabupaten.KabupatenId = master_kecamatan.KabupatenId
                                            LEFT JOIN master_provinsi ON master_provinsi.ProvinsiId = master_kabupaten.ProvinsiId 
                                        WHERE
                                        master_provinsi.ProvinsiId = " . $provinsi . "
                                            AND
                                        master_kecamatan.KabupatenId = " . $kabupaten . "
                                         AND
                                        master_kelurahan.KecamatanId = " . $kecamatan . "
                                      ");
        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get pending order ID Nurse
     */
    public function getProductID($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM products WHERE UserID = '" . $user_id . "' Order BY ProductID Desc LIMIT 1");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['ProductID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get pending order ID Nurse
     */
    public function getCartID($customer_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM cart WHERE CustomerID = '" . $customer_id . "' Order BY CartID Desc LIMIT 1");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['CartID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }
    /**
     * Get pending order ID Nurse
     */
    public function getVariantID($user_id, $product_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM product_variants WHERE UserID = '" . $user_id . "' and ProductID = '" . $product_id . "'  LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['ProductVariantID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    public function getProductVariantID($product_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM product_variants WHERE ProductID = '" . $product_id . "'  LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['ProductVariantID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }
    /**
     * Get pending order ID Nurse
     */
    public function getPendingOrderID($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_current WHERE UserID = '" . $user_id . "' AND OrderStatusID=1 AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['OrderID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check if payment confirmation exist (Promo Order)
     */
    public function checkConfirmPaymentPendingPromo($promo_order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM promo_payment_transfer WHERE PromoOrderID = '" . $promo_order_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get pending promo order ID
     */
    public function getPendingPromoOrderID($promo_id, $user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT PromoOrderID as 'OrderID' FROM promo_order WHERE UserID = '" . $user_id . "' AND PromoID = '" . $promo_id . "' AND PromoOrderStatusID=1 AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['OrderID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get promo detail
     */
    public function getPromoDetail($id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT
                                        PromoID,
                                        Title,
                                        Content,
                                        Harga,
                                        Stock,
                                        (
                                        CASE
                                                
                                                WHEN OwnerKategori = 'apt_pharmacies' THEN
                                                \"Pharmacy\"
                                                WHEN OwnerKategori = 'master_hospitals' THEN
                                                    \"Hospital\"
                                                WHEN OwnerKategori = 'lab_laboratoriums' THEN
                                                    \"Laboratorium\"
                                                ELSE \"The quantity is something else\" 
                                            END 
                                            ) AS OwnerKategori,
                                        OwnerID,
                                        CreatedDate,
                                        (
                                        CASE
                                                
                                                WHEN OwnerKategori = 'apt_pharmacies' THEN
                                                ( SELECT apt_pharmacies.`Name` FROM apt_pharmacies WHERE PharmacyID = OwnerID ) 
                                                WHEN OwnerKategori = 'master_hospitals' THEN
                                                ( SELECT master_hospitals.HospitalName FROM master_hospitals WHERE HospitalID = OwnerID ) 
                                                WHEN OwnerKategori = 'lab_laboratoriums' THEN
                                                ( SELECT lab_laboratoriums.`Name` FROM lab_laboratoriums WHERE LabID = OwnerID ) 
                                                ELSE \"The quantity is something else\" 
                                            END 
                                            ) AS Owner FROM master_promo WHERE Active = 1 AND PromoID = " . $id);
        return $query_get;
    }

    /**
     * Create Payment Confirmation (Promo Order)
     */
    public function confirmPaymentTransferPromo($user_id, $payment_accound_id, $promo_order_id, $bank_name, $account_name, $account_no, $trf_date, $total)
    {
        //7 Menuggu Verifikasi
        $update = $this
            ->conn
            ->query("UPDATE promo_order SET 
											PromoOrderStatusID 		= '7'
										WHERE 
											PromoOrderID = '" . $promo_order_id . "'");

        //Status nya '0' artinya sudah siap di verifikasi oleh admin
        $insert = $this
            ->conn
            ->query("INSERT INTO promo_payment_transfer 
										(PromoOrderID,								
										PaymentTypeID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,
										Status,
										CreatedBy,
										CreatedDate
										) 
									VALUES 
										('" . $promo_order_id . "',
								
										'2',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $trf_date . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										'0',
										'" . $user_id . "',
										'" . $this->get_current_time() . "'
										) ");

        if ($insert)
        {
            $insert_id = $this
                ->conn->insert_id;
            $dt = $this->getPromoByOrderID($promo_order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $promo_order_id = $promo_order_id;
                $order_status_id = 7; //Menunggu Verifikasi
                $description = 'Log Order Promo, created by sistem api';
                $promo_id = $dt['PromoID'];
                $this->createOrderLogPromo($promo_order_id, $order_status_id, $description);
            }
            return $insert_id;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get Promo Order Offer
     */
    public function getOrderPromo($user_id, $promo_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT
                                            a.AlamatPengiriman,
                                            a.UserID,
                                            a.PromoOrderStatusID,
                                            a.PromoID,
                                            a.PromoOrderID,
                                            a.Transport,
                                            a.TotalPayment,
                                            d.Harga,
                                            IFNULL( a.KodeUnik, 0 ) AS 'KodeUnik',
                                            a.PaymentTypeID,
                                            a.PromoOrderNo,
                                            b.PaymentType,
                                            c.StatusName 
                                            FROM
                                            promo_order AS a
                                            INNER JOIN master_payment_type b ON b.PaymentTypeID = a.PaymentTypeID
                                            INNER JOIN promo_order_status c ON c.PromoOrderStatusID = a.PromoOrderStatusID 
                                            INNER JOIN master_promo d ON d.PromoID = a.PromoID
											WHERE 
                                            a.UserID =  '" . $user_id . "' 
                                            AND
                                            a.Active = 1 
                                            and 
                                            a.PromoID =  '" . $promo_id . "'
											");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Promo Order Offer Detail
     */
    public function getImageProduct($product_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT
                                           *
                                        from
                                        image_products
					
											WHERE 
                                            ProductID =  '" . $product_id . "'
                                        
                                         
											");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }


    public function getImageProductVariants($product_variant_id)
    {

        $query_get = $this->conn->query("SELECT
                                           *
                                        from
                                        image_product_variants
					
											WHERE 
                                            ProductVariantID =  '" . $product_variant_id . "'
                                        
                                         
											");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }
    /**
     * Get Promo Order Detail
     */
    public function getPromoOrderDetail($promo_id, $user_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT
                                            a.AlamatPengiriman,
                                            a.UserID,
                                            a.PromoOrderStatusID as 'OrderStatusID',
                                            a.PromoID as 'ProductID',
                                            a.PromoOrderID as 'OrderID',
											d.Title,
                                            a.Transport,
                                            a.TotalPayment,
                                            d.Harga,
                                            IFNULL( a.KodeUnik, 0 ) AS 'KodeUnik',
                                            a.PaymentTypeID,
                                            a.PromoOrderNo as 'OrderNo',
                                            b.PaymentType,
                                            c.StatusName 

                                            FROM
                                            promo_order AS a
                                            INNER JOIN master_payment_type b ON b.PaymentTypeID = a.PaymentTypeID
                                            INNER JOIN promo_order_status c ON c.PromoOrderStatusID = a.PromoOrderStatusID 
                                            INNER JOIN master_promo d ON d.PromoID = a.PromoID
					
											WHERE 
                                            a.PromoID =  '" . $promo_id . "' 
                                            AND
											a.UserID =  '" . $user_id . "' 
											and
                                            a.Active = 1 
                                            AND
                                            a.PromoOrderStatusID = 1
                                         
											");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Top Up User Detail
     */
    public function getTopUpUserDetail($user_id, $amount)
    {

        $query_get = $this
            ->conn
            ->query("SELECT
                                            *
                                            FROM
                                            user_wallet_topup
                                            LEFT JOIN user_wallet_topup_status ON user_wallet_topup.TopUpStatusID = user_wallet_topup_status.UserWalletStatusID 
                                            WHERE UserID = '" . $user_id . "' AND Amount = '" . $amount . "' ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Top Up User Detail
     */
    public function getTopUpUser($user_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT
                                            a.*,
                                            b.StatusName
                                            FROM
                                            user_wallet_topup a
                                            LEFT JOIN user_wallet_topup_status b ON a.TopUpStatusID = b.UserWalletStatusID 
                                            LEFT JOIN master_users c ON a.UserID = c.UserID
                                            WHERE a.UserID = '" . $user_id . "'
                                            ORDER BY a.OrderID DESC
                                            ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get neareset pharmacies, around 10KM
     */
    public function getPharmacies($latitude, $longitude)
    {
        $query_get = $this
            ->conn
            ->query("SELECT *, 
												CONCAT( '" . $this->uploaddir . "', '/pharmacies/', PharmacyID,'.jpg') AS Image,
												ROUND((3959 * acos(cos(radians(" . $latitude . "))*cos(radians(Latitude))*cos(radians(Longitude)-radians(" . $longitude . ")) + sin(radians(" . $latitude . "))*sin(radians(Latitude)))), 2) AS distance
										 FROM apt_pharmacies 
										 HAVING distance <= 10 AND Active = 1");

        return $query_get;
    }

    /**
     * Get neareset laboratorium, around 10KM
     * @param : Latitude, Longitude
     * returns boolean
     */
    public function getLaboratoriumByLocation($latitude, $longitude)
    {
        $query_get = $this
            ->conn
            ->query("SELECT *, 
												CONCAT( '" . $this->uploaddir . "', '/labs/', LabID,'.jpg') AS Image,
												ROUND((3959 * acos(cos(radians(" . $latitude . "))*cos(radians(Latitude))*cos(radians(Longitude)-radians(" . $longitude . ")) + sin(radians(" . $latitude . "))*sin(radians(Latitude)))), 2) AS distance
										 FROM lab_laboratoriums 
										 HAVING distance <= 10 AND Active = 1");
        return $query_get;
    }

    /**
     * Get payment account join bank
     * @param : Latitude, Longitude
     * returns boolean
     */
    public function getPaymentAccount()
    {
        $q = $this
            ->conn
            ->query("SELECT a.AccountName, a.AccountNumber, a.Branch,b.BankName AS Bank, b.BankID, CONCAT( '" . $this->uploaddir . "', '/banks/', b.image) as image
 			FROM master_payment_account a
 			JOIN master_bank b ON a.BankID = b.BankID
 			WHERE a.Active = 1 AND b.Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get config from master_config
     * @param : $configName
     * returns boolean
     */
    public function getConfig($configName)
    {
        $q = $this
            ->conn
            ->query("SELECT * FROM master_config 
			WHERE ConfigName = '" . $configName . "' AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get nurse actions
     */
    public function getNurseActions($category_id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT * FROM nrz_actions WHERE CategoryID='" . $category_id . "' AND Active = 1");
        return $query_get;
    }

    /**
     * Get nurse data detail
     */
    public function getNurseData($nurse_id)
    {
        //get nurse rating order success
        $rating = $this->getRating($nurse_id);

        $query_get = $this
            ->conn
            ->query("SELECT NurseID,FirstName,LastName,Phone,Email,Degree,BirthDate,No_STR,YearExperience,Location, $rating AS Rating  FROM nrz_nurses WHERE NurseID = '" . $nurse_id . "' AND Active=1");
        return $query_get;
    }

    /**
     * Get doctor data detail
     */
    public function getPharmacyDetail($PharmacyID)
    {
        //get pharmacy detail
        $query_get = $this
            ->conn
            ->query("SELECT *  FROM apt_pharmacies WHERE PharmacyID = '" . $PharmacyID . "'");
        return $query_get;
    }

    /**
     * Get doctor data detail
     */
    public function getDoctorData($doctor_id)
    {
        //get nurse rating order success
        $rating = $this->getRatingDoctor($doctor_id);

        $query_get = $this
            ->conn
            ->query("SELECT DoctorID,FirstName,LastName,Phone,Email,Degree,BirthDate,No_STR,YearExperience,Location, $rating AS Rating  FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' AND Active=1");
        return $query_get;
    }

    /**
     * Get pharmacy data detail
     */
    public function getPharmacyData($pharmacy_id)
    {
        //get nurse rating order success
        //$rating = $this->getRatingPharmacy($pharmacy_id);
        $query_get = $this
            ->conn
            ->query("SELECT PharmacyID,FirstName,LastName,Phone,Email,BirthDate AS Rating  FROM apt_users WHERE PharmacyID = '" . $pharmacy_id . "' AND Active=1");
        return $query_get;
    }

    /**
     * Get nurse education
     */
    public function getNurseEducation($nurse_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT NurseEducationID,University,Degree,GraduationYear FROM nrz_nurse_educations WHERE NurseID = '" . $nurse_id . "' AND Active=1 ORDER BY NurseEducationID DESC");
        return $query_get;
    }

    /**
     * Get Bank Account
     */
    public function getBankAccount($payment_accound_id = '')
    {
        $condition = '';
        if ($payment_accound_id != '')
        {
            $condition .= ' && a.PaymentAccountID =' . $payment_accound_id;
        }

        $query_get = $this
            ->conn
            ->query("SELECT a.*, 
					(CASE WHEN Image IS NOT NULL THEN CONCAT( '" . $this->uploaddir . "', '/banks/', b.Image) 
					ELSE CONCAT( '" . $this->uploaddir . "', '/banks/', 'default', '.jpg') 
					END) AS image
					FROM master_payment_account a
					JOIN master_bank b ON b.BankID = a.BankID
					WHERE a.Active = 1 && b.Active = 1" . $condition);
        return $query_get;
    }

    /**
     * Get doctor education
     */
    public function getDoctorEducation($doctor_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT DoctorEducationID,University,Degree,GraduationYear FROM doc_doctor_educations WHERE DoctorID = '" . $doctor_id . "' AND Active=1 ORDER BY DoctorEducationID DESC");
        return $query_get;
    }

    /**
     * Get nurse experience
     */
    public function getNurseExperience($nurse_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT NurseExperienceID, InstituteName, EntryDate, OutDate, JobDesk FROM nrz_nurse_experiences WHERE NurseID = '" . $nurse_id . "' AND Active=1 ORDER BY NurseExperienceID DESC");
        return $query_get;
    }

    /**
     * Get doctor experience
     */
    public function getDoctorExperience($doctor_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT DoctorExperienceID, InstituteName, EntryDate, OutDate, JobDesk FROM doc_doctor_experiences WHERE DoctorID = '" . $doctor_id . "' AND Active=1 ORDER BY DoctorExperienceID DESC");
        return $query_get;
    }

    /**
     * Get pending order ID Nurse
     */
    //    public function getPendingOrderID($user_id)
    //    {
    //        $query = $this->conn->query("SELECT * FROM nrz_orders_current WHERE UserID = '" . $user_id . "' AND OrderStatusID=1 AND Active=1 LIMIT 1 ");
    //
    //        if (mysqli_num_rows($query) > 0) {
    //            $row = $query->fetch_assoc();
    //            $current_id = $row['OrderID'];
    //
    //            return $current_id;
    //        } else {
    //            return null;
    //        }
    //    }
    
    /**
     * Get pending order ID Doctor
     */
    public function getDoctorPendingOrderID($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM doc_orders_current WHERE UserID = '" . $user_id . "' AND OrderStatusID=1 AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $current_id = $row['OrderID'];

            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get filename of nurse chat
     */
    public function getFileChatNurse($chat_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_chat WHERE ChatID = '" . $chat_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $filename = $row['Filename'];

            return $filename;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get filename of doctor chat
     */
    public function getFileChatDoctor($chat_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM doc_chat WHERE ChatID = '" . $chat_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $filename = $row['Filename'];

            return $filename;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Data
     */
    public function getOrderData($order_id, $nurse_id = null)
    {

        if ($nurse_id != null)
        {
            //Count estimation transport fare
            $perKM = $this->getConfig('biaya_transportasi_per_km')
                ->fetch_assoc();
            $current_price = 0;
            $current_lat = 0;
            $current_lng = 0;
            $current_distance = 0;

            $queryID = $this
                ->conn
                ->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1");
            $rowID = $queryID->fetch_assoc();
            $current_lat = $rowID['Latitude'];
            $current_lng = $rowID['Longitude'];
            $current_price = $rowID['TotalPrice'];
            //get distance from nurse location to user location
            $queryTransport = $this
                ->conn
                ->query("SELECT 
													(3959 * acos(cos(radians(" . $current_lat . "))*cos(radians(Latitude))*cos(radians(Longitude)-radians(" . $current_lng . ")) + sin(radians(" . $current_lat . "))*sin(radians(Latitude)))) AS distance
												FROM nrz_nurse_location 
												WHERE NurseID='" . $nurse_id . "' ");
            $rowT = $queryTransport->fetch_assoc();
            $current_distance = ceil($rowT['distance']);
            // transaport price nurse per KM
            $transportTotal = ($perKM['Value'] * $current_distance);

            if ($transportTotal < 7000)
            {
                $transportTotal = 8000;
            }

            $orderTotal = ($current_price + $transportTotal);
        }
        else
        {
            $transportTotal = "a.TransportPrice";
            $orderTotal = "a.TotalPayment";
        }

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.OrderID,
											a.OrderDate,
											a.Notes,
											a.TotalPrice,
											a.OrderStatusID,
											a.Location AS Address,
											a.OrderNo,
											b.NurseID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											b.YearExperience,
											b.Location,
											" . $transportTotal . " AS TransportPrice,
											" . $orderTotal . " AS TotalPayment,
											c.FirstName AS user_firstname,
											c.LastName AS user_lastname,
											c.Email AS user_email
										FROM nrz_orders_current a
										INNER JOIN nrz_nurses b ON b.NurseID = a.NurseID
										INNER JOIN master_users c ON c.UserID = a.UserID
										WHERE a.OrderID = '" . $order_id . "' AND a.Active = 1");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Data Doctor
     */
    public function getPharmacyOrderData($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.*,
											-- a.OrderID,
											-- a.OrderDate,
											-- a.Notes,
											-- a.TotalPrice,
											-- a.OrderNo,
											-- a.OrderStatusID,
											-- a.UserID,
											b.PharmacyID,
											b.Name,
											b.Phone,
											c.Firstname AS 'user_firstname',
											c.LastName AS 'user_lastname',
											c.Email AS 'user_email'
										FROM apt_orders a
										LEFT JOIN apt_pharmacies b ON b.PharmacyID = a.PharmacyID
										LEFT JOIN master_users c ON c.UserID = a.UserID
										WHERE a.OrderID = '" . $order_id . "' AND a.Active = 1");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Data Doctor
     */
    public function getDoctorOrderData($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.*,
											-- a.OrderID,
											-- a.OrderDate,
											-- a.Notes,
											-- a.TotalPrice,
											-- a.OrderNo,
											-- a.OrderStatusID,
											-- a.UserID,
											b.DoctorID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											b.YearExperience,
											b.Location,
											b.Email,
											c.Firstname AS 'user_firstname',
											c.LastName AS 'user_lastname',
											c.Email AS 'user_email'
										FROM doc_orders_current a
										LEFT JOIN doc_doctors b ON b.DoctorID = a.DoctorID
										LEFT JOIN master_users c ON c.UserID = a.UserID
										WHERE a.OrderID = '" . $order_id . "' AND a.Active = 1");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Nurse History
     */
    public function getOrderNurseHistory($user_id, $order_id = null, $page, $limit)
    {

        $filter = "";
        if ($order_id != null)
        {
            $filter = " AND a.OrderID = '" . $order_id . "' ";
        }

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.OrderID,
											a.OrderNo,
											DATE(a.OrderDate) AS OrderDate,
											a.Notes,
											a.TotalPrice,
											a.TotalPrice as SubTotal,
											a.Rating AS Rate,
											a.TransportPrice,
											a.TotalPayment,
											a.UniqueCode,
											b.NurseID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											b.YearExperience,
											b.Location,
											c.OrderStatusID,
											c.StatusName,
											d.PaymentType,
											a.voucher_code,
											a.nominal,
											(CASE WHEN a.voucher_code = '0' THEN null WHEN a.voucher_code = '' THEN null ELSE a.voucher_code END) as voucher_code,
											(CASE WHEN a.nominal = '0' THEN null WHEN a.nominal = '' THEN null ELSE a.nominal END) as nominal
										FROM nrz_orders_current a
										INNER JOIN nrz_nurses b ON b.NurseID = a.NurseID
										INNER JOIN nrz_order_status c ON c.OrderStatusID = a.OrderStatusID
										INNER JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
										WHERE a.UserID = '" . $user_id . "' " . $filter . " AND a.OrderStatusID NOT IN (1) AND a.Active = 1
										ORDER BY a.OrderID DESC " . $condition);

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Doctor History
     */
    public function getOrderDoctorHistory($user_id, $order_id = null, $page, $limit)
    {

        $filter = "";
        if ($order_id != null)
        {
            $filter = " AND a.OrderID = '" . $order_id . "' ";
        }

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.*,
											-- a.OrderID,
											-- a.OrderNo,
											DATE(a.OrderDate) AS OrderDate,
											-- a.Notes,
											-- a.TotalPrice,
											a.Rating AS Rate,
											b.DoctorID,
											b.FirstName,
											b.LastName,
											b.BirthDate,
											b.YearExperience,
											b.Location,
											b.Price,
											c.OrderStatusID,
											c.StatusName,
											
											d.PaymentType
										FROM doc_orders_current a
										INNER JOIN doc_doctors b ON b.DoctorID = a.DoctorID
										INNER JOIN doc_order_status c ON c.OrderStatusID = a.OrderStatusID
										INNER JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
										WHERE a.UserID = '" . $user_id . "' " . $filter . " AND a.OrderStatusID NOT IN (1) AND a.Active = 1
										ORDER BY a.OrderID DESC " . $condition);

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check user by ID
     */
    public function checkOrderStatusById($id)
    {
        $query = $this
            ->conn
            ->query("SELECT aptOrderStatusID from apt_orders WHERE UserID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query->fetch_assoc();;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get Order Pharmacy History
     */
    public function getOrderPromoHistory($user_id, $promo_order_id = null, $page, $limit)
    {

        $filter = "";
        if ($promo_order_id != null)
        {
            $filter = " AND a.PromoOrderID = '" . $promo_order_id . "' ";
        }

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        // $a = "a.PharmacyID"
        $query_get = $this
            ->conn
            ->query("SELECT 
										a.PromoOrderID,
										a.PromoOrderNo, 
										a.PromoID,
										DATE(a.PromoOrderDate) AS PromoOrderDate,							
										a.TotalPayment, 
										a.Transport, 
										a.TotalPayment, 
										a.KodeUnik, 
										b.Title, 
										c.PromoOrderStatusID, 
										c.StatusName,
										d.PaymentType 
										FROM promo_order a
									LEFT JOIN master_promo b ON a.PromoID = b.PromoID
							
										LEFT JOIN promo_order_status c ON c.PromoOrderStatusID = a.PromoOrderStatusID
										LEFT JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
									
										WHERE a.UserID = '" . $user_id . "' " . $filter . " AND a.Active = 1
										ORDER BY a.promoOrderID DESC " . $condition);

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Pharmacy History
     */
    public function getOrderPharmacyHistory($user_id, $order_id = null, $page, $limit)
    {

        $filter = "";
        if ($order_id != null)
        {
            $filter = " AND a.AptOrderID = '" . $order_id . "' ";
        }

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        // $a = "a.PharmacyID"
        $query_get = $this
            ->conn
            ->query("SELECT 
										a.AptOrderID,
										a.OrderNo, 
										DATE(a.OrderDate) AS OrderDate,
										 a.Notes, 
										a.SubTotal, 
										a.Transport, 
										a.TotalPayment, a.UniqueCode, b.PharmacyID, 
										b.Name, b.Address, b.Phone, c.AptOrderStatusID, c.StatusName, d.PaymentType
										FROM apt_orders a
									LEFT JOIN apt_order_offers e ON a.AptOrderID = e.AptOrderID
										LEFT JOIN apt_pharmacies b ON b.PharmacyID = a.PharmacyID
										LEFT JOIN apt_order_status c ON c.AptOrderStatusID = a.AptOrderStatusID
										LEFT JOIN master_payment_type d ON d.PaymentTypeID = a.PaymentTypeID
									
										WHERE a.UserID = '" . $user_id . "' " . $filter . " AND a.Active = 1 And a.PharmacyID != '0'
										ORDER BY a.AptOrderID DESC " . $condition);

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Order Pharmacy History
     */
    public function getOrderAmbulanceHistory($user_id, $order_id = null, $page, $limit)
    {

        $filter = "";
        if ($order_id != null)
        {
            $filter = " AND a.EmergencyOrderID = '" . $order_id . "' ";
        }

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.EmergencyOrderID,
											a.OrderNo,
											DATE(a.OrderDate) AS OrderDate,
											a.Notes,
											a.Rating AS Rate,
											a.TotalPayment,
											a.UniqueCode,
											b.HospitalID,
											b.HospitalName,
											b.HospitalAddress,
											b.HospitalPhone,
											c.EmergencyStatusID,
											c.EmergencyStatus
										FROM amb_emergencyorders a
										INNER JOIN master_hospitals b ON b.HospitalID = a.HospitalID
										INNER JOIN amb_emergencystatus c ON c.EmergencyStatusID = a.EmergencyStatusID
										WHERE a.UserID = '" . $user_id . "' " . $filter . " AND a.EmergencyStatusID NOT IN (1) AND a.Active = 1
										ORDER BY a.EmergencyOrderID DESC " . $condition);

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Action - Order Detail
     */
    public function getOrderAction($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.ActionID,
											b.ActionName,
											b.ActionPrice
										FROM nrz_orders_detail a
										INNER JOIN nrz_actions b ON b.ActionID = a.ActionID
										WHERE a.OrderID = '" . $order_id . "'");
        return $query_get;
    }

    /**
     * Get Action - Order Detail
     */
    public function getPharmacyOrderAction($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											d.Name,
											d.Address,
											c.MedicineID,
											CASE
                                                WHEN c.MedicineName IS NULL THEN
                                                c.NamaAlkes ELSE c.MedicineName 
                                            END AS `MedicineName` 
										FROM apt_order_detail a
										LEFT JOIN apt_order_offers b ON b.AptOrderID = a.AptOrderID
										LEFT JOIN apt_medicines c ON c.MedicineID = a.MedicineID
										LEFT JOIN apt_pharmacies d ON b.PharmacyID = d.PharmacyID
										WHERE a.AptOrderID = '" . $order_id . "'");
        return $query_get;
    }

    /**
     * Get Nurse Chat Data
     */
    public function getChat($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											ChatID,
											Message,
											ChatDate,
											(CASE WHEN Filename IS NOT NULL THEN CONCAT( '" . $this->uploaddir . "', '/chats/nurse_orders/', OrderID,'/',Filename) ELSE '' END) AS url,
											(CASE WHEN LEFT(ChatFrom, 3) = 'usr' THEN '1' ELSE '0' END) AS ChatFrom,
											(CASE WHEN LEFT(ChatFrom, 3) = 'usr' THEN 'right' ELSE 'left' END) AS Position,
											(CASE WHEN Filename IS NOT NULL THEN Filename ELSE '' END) AS image_name
										FROM nrz_chat
										WHERE OrderID = '" . $order_id . "'
										ORDER BY ChatID ASC");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Doctor Chat Data
     */
    public function getChatDoctor($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											ChatID,
											Message,
											ChatDate,
											(CASE WHEN Filename IS NOT NULL THEN CONCAT( '" . $this->uploaddir . "', '/chats/doctor_orders/', OrderID,'/',Filename) ELSE '' END) AS url,
											(CASE WHEN LEFT(ChatFrom, 3) = 'usr' THEN '1' ELSE '0' END) AS ChatFrom,
											(CASE WHEN LEFT(ChatFrom, 3) = 'usr' THEN 'right' ELSE 'left' END) AS Position
										FROM doc_chat
										WHERE OrderID = '" . $order_id . "'
										ORDER BY ChatID ASC");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Nurse Data From Order ID
     */
    public function getHistoryOrder($user_id , $marketplace)
    {

        $query_get = $this
            ->conn
            ->query("SELECT  order_id , statuses from history_orders
										WHERE marketplace='" . $marketplace . "' and (statuses = 10 or statuses= 3)");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get Doctor Data From Order ID
     */
    public function getDoctorByOrderID($order_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											a.OrderID,
											b.FirstName,
											b.LastName,
											b.DoctorID
										FROM doc_orders_current a
										INNER JOIN doc_doctors b ON a.DoctorID = b.DoctorID 
										WHERE a.Active=1 AND a.OrderID='" . $order_id . "' ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserLogin($email, $password)
    {
        $check = $this
            ->conn
            ->query("SELECT * FROM users WHERE Email = '" . $email . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0)
        {

            $row = $check->fetch_assoc();
            //$salt = $row['PasswordSalt'];
            $salt = '38ebeaedce';
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this
                ->conn
                ->query("SELECT * FROM users WHERE Email = '" . $email . "' AND Password='" . $password . "' AND Active=1");

            if (mysqli_num_rows($check_pass) > 0)
            {
                //Generate new token
                $new_token = $this->generateToken();
                $upd = $this
                    ->conn
                    ->query("UPDATE users SET IsLogin=1, Token='" . $new_token . "' WHERE Email='" . $email . "' AND Active=1 ");

                if ($upd)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }

    /*create by elim*/
    /**
     * Check if user exist
     */
    public function checkUserLoginGoogle($email, $google_user_id)
    {
        $check = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE Email = '" . $email . "' AND GoogleUserID = '" . $google_user_id . "' ");

        if (mysqli_num_rows($check) > 0)
        {
            //Generate new token
            $new_token = $this->generateToken();
            $upd = $this
                ->conn
                ->query("UPDATE master_users SET IsLogin=1, Token='" . $new_token . "' WHERE Email='" . $email . "' AND GoogleUserID= '" . $google_user_id . "' ");

            if ($upd)
            {
                $q = $this->checkUserRegister2($email, $google_user_id);
                return $q;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }

    public function checkUserLoginGoogle2($email, $google_user_id)
    {
        //Generate new token
        $new_token = $this->generateToken();
        $upd = $this
            ->conn
            ->query("UPDATE master_users SET IsLogin=1, Token='" . $new_token . "', GoogleUserID= '" . $google_user_id . "' WHERE Email='" . $email . "'");

        if ($upd)
        {
            $q = $this->checkUserRegister2($email, $google_user_id);
            return $q;
        }
        else
        {
            return false;
        }
    }
    /*End created by elim*/

    /**
     * Check profile is complete
     */
    public function checkIsProfileComplete($email)
    {
        $check_pass = $this
            ->conn
            ->query("SELECT TokoID FROM users WHERE Email='" . $email . "' AND Active=1");

        if (mysqli_num_rows($check_pass) > "0")
        {
            //profile not complete
            return 0;
        }
        else
        {
            //profile complete
            return 1;
        }
    }

    // Create by elim
    
    /** Check profile is complete
     */
    public function checkIsProfileComplete2($email, $google_user_id)
    {
        $check_pass = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE Email='" . $email . "' AND GoogleUserID = '" . $google_user_id . "' AND Active=1 AND BirthDate IS NULL AND BirthPlace='' AND Email='' AND Address='' AND Height=0 AND Weight=0 ");

        if (mysqli_num_rows($check_pass) > 0)
        {
            //profile not complete
            return 0;
        }
        else
        {
            //profile complete
            return 1;
        }
    }
    // End create by elim
    
    /**
     * Check login by new generated pass (forgot pass)
     */
    public function checkUserLoginByForgot($email, $password)
    {

        $now = $this->get_chat_time();

        $check_pass = $this
            ->conn
            ->query("SELECT * FROM users WHERE Email = '" . $email . "' AND ForgotPassword='" . $password . "' AND ForgotPasswordExpired > STR_TO_DATE('" . $now . "', '%Y-%m-%d %H:%i:%s') AND Active=1 ");

        if (mysqli_num_rows($check_pass) > 0)
        {
            $new_token = $this->generateToken();
            $upd = $this
                ->conn
                ->query("UPDATE users SET IsLogin=1, Token='" . $new_token . "' WHERE Email='" . $email . "' AND Active=1 ");
            if ($upd)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Check user password
     */
    public function checkUserPassword($user_id, $password)
    {
        $check = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE UserID = '" . $user_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0)
        {

            $row = $check->fetch_assoc();
            $salt = $row['PasswordSalt'];
            $encrypted_password = $this->checkhashSSHA($salt, $password);

            $check_pass = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE UserID = '" . $user_id . "' AND Password='" . $encrypted_password . "' ");

            if (mysqli_num_rows($check_pass) > 0)
            {
                return true;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }

    /**
     * Check user password
     */
    public function checkUserPasswordForgot($user_id, $password)
    {
        $check = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE UserID = '" . $user_id . "' AND Active=1 ");

        if (mysqli_num_rows($check) > 0)
        {

            $check_pass = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE UserID = '" . $user_id . "' AND ForgotPassword='" . $password . "' ");

            if (mysqli_num_rows($check_pass) > 0)
            {
                return true;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserRegister($email)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM users WHERE Email = '" . $email . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserByUserIDRegister($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM users WHERE UserID = '" . $user_id . "' AND Active=1");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkHistoryOrderByOrder($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * from history_orders WHERE order_id = '" . $order_id . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }

    }
	    public function getDetailHistoryOrderByTracking($tracking_number)
    {
        $query = $this
            ->conn
            ->query("SELECT * from history_order_details WHERE tracking_code = '" . $tracking_number . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }

    }

    public function checkOrderNumber($order_number)
    {
        $query = $this
            ->conn
            ->query("Select * from history_orders WHERE order_number = '" . $order_number . "'");

        return $query;

    }

    public function checkCart($user_id, $token_session)
    {

        $query = $this
            ->conn
            ->query("SELECT 
p.ProductName,
c.CartID,
cd.SKU,
cd.Price,
cd.Quantity,
pv.ProductVariantName,
pvd.ProductVariantDetailName,
ipv.ImageProductVariantName
FROM cart AS c
LEFT JOIN cart_details AS cd
ON c.CartID = cd.CartID
LEFT JOIN product_variant_details AS pvd
ON cd.SKU = pvd.SkuID 
LEFT JOIN product_variants AS pv
ON pvd.ProductVariantID = pv.ProductVariantID
LEFT JOIN products AS p
ON pv.ProductID = p.ProductID 
LEFT JOIN image_product_variants AS ipv
ON pv.ProductVariantID = ipv.ProductVariantID
WHERE (ipv.IsDefault = 1 and c.UserID = '" . $user_id . "') and c.TokenSession = '" . $token_session . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkCategoryName($category_name)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_category WHERE CategoryName = '" . $category_name . "'");

        if (mysqli_num_rows($query) > 0)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkBrandName($brand_name)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_brand WHERE BrandName = '" . $brand_name . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkVariantName($color_name, $color_code)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM variant WHERE ColorName = '" . $color_name . "' and ColorCode = '" . $color_code . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkColorName($color_name)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_color WHERE ColorName = '" . $color_name . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkSizeName($size_name)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_size WHERE SizeName = '" . $size_name . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkTokoIDRegister($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT TokoID from users WHERE UserID = '" . $user_id . "' AND Active=1");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if user exist
     */
    public function checkPhoneTokoRegister($phone)
    {
        $query = $this
            ->conn
            ->query("SELECT * from master_toko WHERE Phone = '" . $phone . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check email exist by_phone_number
     */
    public function checkEmailbyPhone($phone)
    {
        $query = $this
            ->conn
            ->query("SELECT Email, FirstName, LastName FROM master_users WHERE Phone = '" . $phone . "' AND Active=1 limit 1");
        $row = $query->fetch_assoc();

        if (mysqli_num_rows($query) > 0)
        {
            return $row;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if token exist
     */
    public function checkToken($token, $user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM users WHERE Token = '" . $token . "' AND UserID = '" . $user_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check pending order
     */
    public function checkPendingOrder($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_current WHERE UserID = '" . $user_id . "' AND OrderStatusID=1 AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check finish order nurse
     */
    public function checkFinishOrder($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND OrderStatusID=7 AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check finish order doctor
     */
    public function checkFinishOrderDoctor($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM doc_orders_current WHERE OrderID = '" . $order_id . "' AND OrderStatusID=6 AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check pending order
     */
    public function checkPendingOrderDoctor($user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM doc_orders_current WHERE UserID = '" . $user_id . "' AND OrderStatusID=1 AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if order nurse exist
     */
    public function checkOrderExist($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if topup exist
     */
    public function checkTopUpExist($topup_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM user_wallet_topup WHERE OrderID = '" . $topup_id . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if order nurse exist
     */
    public function checkOrderExist2($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if order doctor exist
     */
    public function checkDoctorOrderExist($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM doc_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkPharmacyOrderExist($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM apt_orders WHERE AptOrderID = '" . $order_id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check detail order exist
     */
    public function checkDetailOrder($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_detail WHERE OrderID = '" . $order_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check user by ID
     */
    public function checkUserById($id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE UserID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check nurse by ID
     */
    public function checkNurseById($id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_nurses WHERE NurseID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check doctor by ID
     */
    public function checkDoctorById($id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM doc_doctors WHERE DoctorID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check pharmacy by ID
     */
    public function checkPharmacyById($id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM apt_users WHERE PharmacyID = '" . $id . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check monthly order
     */
    public function checkMonthlyOrder()
    {
        $year = date("Y");
        $month = date("m");

        $query = $this
            ->conn
            ->query("SELECT * FROM `nrz_orders_current` WHERE YEAR(OrderDate)='" . $year . "' AND MONTH(OrderDate) = '" . $month . "' AND Active=1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check Activation Code
     *
     */
    public function checkActivationCode($phone, $code)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            $referal_id = $this->generateToken(8);
            $this
                ->conn
                ->query("UPDATE master_users SET Active = 1, IsLogin=1, ReferralID='" . $referal_id . "' WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkActivationCode2($phone, $code)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            $referal_id = $this->generateToken(8);
            $this
                ->conn
                ->query("UPDATE master_users SET Active = 1, IsLogin=1, Verified =1, ReferralID='" . $referal_id . "' WHERE Phone = '" . $phone . "' AND ActivationCode='" . $code . "' ");
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Resend Activation Code
     */
    public function resendActivationCode($phone)
    {
        // echo 'test 3';
        $query_sms = $this
            ->conn
            ->query("SELECT FirstName, Phone, ActivationCode, Active FROM master_users WHERE Phone = '" . $phone . "' ");
        // var_dump($query_sms);
        if (mysqli_num_rows($query_sms) > 0)
        {
            // var_dump($row_sms);
            $row_sms = $query_sms->fetch_assoc();
            $name = $row_sms['FirstName'];
            $code = $row_sms['ActivationCode'];
            $is_active = $row_sms['Active'];

            if ($is_active == "0")
            {
                $this->send_sms($phone, $code, $name);

                return true;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }

    }

    /**
     * Resend Activation Code
     */
    public function sendMessage($phone, $message)
    {

        // echo 'test 3';
        // $query_sms = $this->conn->query("SELECT FirstName, Phone, ActivationCode, Active FROM master_users WHERE Phone = '" . $phone . "' ");
        // var_dump($query_sms);
        // if (mysqli_num_rows($query_sms) > 0) {
        // var_dump($row_sms);
        // $row_sms = $query_sms->fetch_assoc();
        // $name = $row_sms['FirstName'];
        ///$code = $row_sms['ActivationCode'];
        //$is_active = $row_sms['Active'];
        // if ($is_active == "0") {
        //  $this->send_sms($phone, $code, $name);
        return true;
        // } else {
        //  return false;
        //}
        //} else {
        //return false;
        //}
        
    }

    /**
     * Check if payment confirmation exist (Nurse Order)
     */
    public function checkConfirmPaymentPending($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_payment_transfers WHERE OrderID = '" . $order_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if payment confirmation exist (TopUp Order)
     */
    public function checkConfirmPaymentPendingTopUp($topup_id)
    {
        //        0: "ready confirmation by admin";1: accepted; 2: decline
        $query = $this
            ->conn
            ->query("SELECT * FROM user_wallet_topup_payment_transfer WHERE TopUpID = '" . $topup_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if payment confirmation exist (Pharmacy Order)
     */
    public function checkConfirmPaymentPendingPharmacy($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM apt_payment_transfers WHERE AptOrderID = '" . $order_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if payment confirmation exist (Doctor Order)
     */
    public function checkConfirmPaymentPendingDoctor($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM doc_payment_transfers WHERE OrderID = '" . $order_id . "' AND Status=0 ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update FirebaseID
     */
    public function updateFirebase($email, $firebase_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE master_users SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										Email = '" . $email . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update FirebaseID
     */
    public function acceptOrders($user_id, $order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 2
									WHERE 
										order_id = '" . $order_id . "' and user_id = '" . $user_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update FirebaseID
     */
    public function setShip($user_id, $order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 3
									WHERE 
										order_id = '" . $order_id . "' and user_id = '" . $user_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

 public function setRts($user_id , $order_id)
    {

        $update = $this->conn->query("UPDATE history_orders SET 
										statuses 		= 10
									WHERE 
										order_id = '" . $order_id . "' and user_id = '" . $user_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     * Update FirebaseID
     */
    public function setDelivery($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 4
									WHERE 
										order_id = '" . $order_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function setReturn($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 5
									WHERE 
										order_id = '" . $order_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function setCancel($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 6
									WHERE 
										order_id = '" . $order_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function setFiled($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 7
									WHERE 
										order_id = '" . $order_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function setUnpaid($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 8
									WHERE 
										order_id = '" . $order_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function setProses($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE history_orders SET 
										statuses 		= 10
									WHERE 
										order_id = '" . $order_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update FirebaseID2
     */
    public function updateFirebase2($email, $google_user_id, $firebase_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE master_users SET 
										FirebaseID 		= '" . $firebase_id . "'
									WHERE 
										Email = '" . $email . "'
										AND GoogleUserID = '" . $google_user_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update New Password
     */
    public function updatePassword($user_id, $new_password)
    {

        $hash = $this->hashSSHA($new_password);
        $encrypted_password = $hash["encrypted"]; // encrypted new password
        $salt_password = $hash["salt"]; // salt new
        $update = $this
            ->conn
            ->query("UPDATE master_users SET 
										Password	 = '" . $encrypted_password . "',
										PasswordSalt = '" . $salt_password . "'
									WHERE 
										UserID = '" . $user_id . "' AND Active=1 ");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Forgot Password
     */
    public function forgotPassword($phone)
    {

        $new_pass = $this->randomPassword(8);
        $expired_date = date('Y-m-d h:i:s', strtotime('+1 days'));

        $update = $this
            ->conn
            ->query("UPDATE master_users SET 
										ForgotPassword	 		= '" . $new_pass . "',
										ForgotPasswordExpired 	= '" . $expired_date . "'
									WHERE 
										Phone = '" . $phone . "' AND Active=1 ");

        if ($update)
        {
            $this->send_sms_password($phone, $new_pass);
            return $new_pass;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update User Profile
     */
    public function updateProfile($user_id, $firstname, $lastname, $phone, $email, $birthdate, $gender, $nik, $birthplace, $address, $height, $weight, $Referral_by)
    {

        $check = $this->checkUserById($user_id);
        if ($check)
        {
            $update = $this
                ->conn
                ->query("UPDATE master_users SET 
											FirstName 	= '" . $firstname . "',
											LastName 	= '" . $lastname . "',
											Phone 		= '" . $phone . "',
											Email 		= '" . $email . "',
											BirthDate 	= '" . $birthdate . "',
											Gender 		= '" . $gender . "',
											NIK 		= '" . $nik . "',
											BirthPlace	= '" . $birthplace . "',
											Address 	= '" . $address . "',
											Height	 	= '" . $height . "',
											Weight	 	= '" . $weight . "',
											ModifiedBy	= '" . $user_id . "',
											ModifiedDate= '" . $this->get_current_time() . "',
											ReferralBy  = '" . $Referral_by . "'
										WHERE 
											UserID = '" . $user_id . "'");

            if ($update)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Choose Nurse By User
     */
    public function chooseNurse($order_id, $nurse_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE nrz_orders_current SET 
										NurseID 		= '" . $nurse_id . "',
										OrderStatusID 	= 2
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update)
        {
            // create order log
            $order_id = $order_id;
            $order_status_id = 2;
            $description = 'Log Order Nurse, created by sistem api';
            $nurse_id = 0;
            $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Choose Pharmacy By User
     */
    public function choosePharmacy($order_id, $pharmacy_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE apt_orders SET 
										PharmacyID 		= '" . $pharmacy_id . "',
										OrderStatusID 	= 2
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update)
        {
            // create order log
            $order_id = $order_id;
            $order_status_id = 2;
            $description = 'Log Order Pharmacy, created by sistem api';
            $pharmacy_id = 0;
            $this->createOrderLog($order_id, $order_status_id, $pharmacy_id, $description);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Choose Doctor By User
     */
    public function chooseDoctor($order_id, $doctor_id)
    {

        $query_get = $this
            ->conn
            ->query("SELECT DoctorID, Price FROM doc_doctors WHERE DoctorID = '" . $doctor_id . "' ");
        $q = $this->getConfig('company_fee_percent')
            ->fetch_assoc();
        $company_fee_percent = $q['Value'];

        $total = "0";
        if (mysqli_num_rows($query_get) > 0)
        {
            $row = $query_get->fetch_assoc();
            $total = $row['Price'];
        }
        $company_fee_nominal = $total * ($company_fee_percent / 100);
        $doctor_fee_percent = 100 - $company_fee_percent;
        $doctor_fee_nominal = $total - $company_fee_nominal;

        $update = $this
            ->conn
            ->query("UPDATE doc_orders_current SET 
										DoctorID = '" . $doctor_id . "',
										TotalPrice = '" . $total . "',
										CompanyFeePercent = '" . $company_fee_percent . "',
										CompanyFeeNominal = '" . $company_fee_nominal . "',
										DoctorFeePercent = '" . $doctor_fee_percent . "',
										DoctorFeeNominal = '" . $doctor_fee_nominal . "',
										OrderStatusID 	 = 2
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update)
        {
            //create order log
            $order_id = $order_id;
            $order_status_id = 2;
            $description = 'Log Order Doctor, created by sistem api';
            $doctor_id = $doctor_id;
            $this->createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Cancel Nurse Status By User
     */
    public function cancelNurse($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE nrz_orders_current SET 
										OrderStatusID = '8'
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update)
        {
            $dt = $this->getNurseByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();
                // create order log
                $order_id = $order_id;
                $order_status_id = 8;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = $dt['NurseID'];
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Cancel Doctor Status By User
     */
    public function cancelDoctor($order_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE doc_orders_current SET 
										OrderStatusID = '7'
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update)
        {
            $dt = $this->getDoctorByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();
                // create order log
                $order_id = $order_id;
                $order_status_id = 7;
                $description = 'Log Order Nurse, created by sistem api';
                $doctor_id = $dt['DoctorID'];
                $this->createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Payment Nurse Status By User
     */
    public function processPayment($order_id, $total_transport, $total_price, $payment_type_id, $unique_code = 0, $kode_voucher, $nominal)
    {

        //Count Total Payment
        if ($nominal == '')
        {
            $nominal = 0;
        }
        $total_payment = ($total_price + $total_transport + $unique_code - $nominal);

        //Check Payment Status
        if ($payment_type_id == "1")
        {
            $payment_status = "4";
        }
        else
        {
            $payment_status = "3";
        }

        /* Generate Order No (Invoice Number) */
        //Get Last Order ID +1
        $check_orderid = $this
            ->conn
            ->query("SELECT IFNULL(MAX(Right(OrderNo,8)),0) AS OrderNo
											FROM nrz_orders_current 
											WHERE 
												DATE_FORMAT(OrderDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(OrderDate, '%Y')='" . date('Y') . "'");
        $data_orderid = $check_orderid->fetch_assoc();

        if ($data_orderid['OrderNo'] == 0)
        {
            //Start From First
            $num = date('y') . date('m') . "1001";
            $order_no = "NRZ" . $num;
        }
        else
        {
            //Continue Number +1
            $num = $data_orderid['OrderNo'] + 1;
            $order_no = "NRZ" . $num;
        }
        /* End generate */

        $update = $this
            ->conn
            ->query("UPDATE nrz_orders_current SET 
										OrderStatusID = '" . $payment_status . "',
										OrderNo = '" . $order_no . "',
										TotalPayment = '" . $total_payment . "',
										TransportPrice = '" . $total_transport . "',
										PaymentTypeID = '" . $payment_type_id . "',
										UniqueCode = '" . $unique_code . "',
										voucher_code = '" . $kode_voucher . "',
										nominal = " . $nominal . "									
									WHERE 
										OrderID = '" . $order_id . "'");

        if ($update)
        {
            $dt = $this->getNurseByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = $payment_status;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = $dt['NurseID'];
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
            }
            $this->dealNotification($order_id);
            $this->NoDealNotification($order_id);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Payment TopUp Patient
     */
    public function processPaymentTopUp($topup_id, $kode_unik, $payment_type_id, $totalpayment)
    {

        $update = $this
            ->conn
            ->query("UPDATE 
                                          user_wallet_topup 
                                        SET 
                                        PaymentTypeID =  '" . $payment_type_id . "',
										TopUpStatusID = '2'					
									WHERE 
										OrderID = '" . $topup_id . "'
										AND TopUpStatusID = '1'
										");

        if ($update)
        {
            $user = $this->getUserByOrderID($topup_id);
            if ($user != null)
            {
                return $user;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Process Cancel Payment TopUp Patient
     */
    public function processCancelTopUp($topup_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE user_wallet_topup SET 
										TopUpStatusID = '5'									
									WHERE 
										OrderID = '" . $topup_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getUserByOrderID($topup_id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT   
											a.OrderID,
											a.OrderNo,
											a.Amount,
											a.KodeUnik,
											b.FirstName,
											b.LastName,
											b.UserID,
											b.Email,
											b.FirebaseID
										FROM user_wallet_topup a
										INNER JOIN master_users b ON a.UserID = b.UserID
										WHERE a.OrderID='" . $topup_id . "' ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Create Payment Confirmation (Nurse Order)
     */
    public function confirmPaymentTransfer($user_id, $order_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total)
    {

        $upd = $this
            ->conn
            ->query("UPDATE nrz_orders_current 
									SET OrderStatusID = 10
									WHERE OrderID = '" . $order_id . "'");
        $insert = $this
            ->conn
            ->query("INSERT INTO nrz_payment_transfers 
										(OrderID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,
										Status,
										CreatedBy,
										CreatedDate
										) 
									VALUES 
										('" . $order_id . "',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $trf_date . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										0,
										'" . $user_id . "',
										'" . $this->get_current_time() . "'
										) ");

        if ($insert)
        {
            $insert_id = $this
                ->conn->insert_id;
            $dt = $this->getNurseByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 10;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = $dt['NurseID'];
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
            }
            return $insert_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Create Payment Confirmation (TopUp Order)
     */
    public function confirmPaymentTransferTopUp($user_id, $topup_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total, $kode_unik)
    {
        //        4 (Menunggu Verifikasi
        $upd = $this
            ->conn
            ->query("UPDATE user_wallet_topup 
									SET TopUpStatusID = 4
									WHERE OrderID = '" . $topup_id . "'");
        $insert = $this
            ->conn
            ->query("INSERT INTO user_wallet_topup_payment_transfer 
										(TopUpID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,										
										Status,
										UserID,
										UniqueCode,
										CreatedBy,
										CreatedDate
										) 
									VALUES 
										('" . $topup_id . "',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $trf_date . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										0,
										'" . $user_id . "',
										'" . $kode_unik . "',
										'" . $user_id . "',
										'" . $this->get_current_time() . "'
										) ");

        if ($insert)
        {
            $insert_id = $this
                ->conn->insert_id;
            return $insert_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Create Payment Confirmation (Pharmacy Order)
     */
    public function confirmPaymentTransferPharmacy($user_id, $order_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total)
    {
        //set AptOrderStatusID = 9 Menunggu Verifikasi
        $upd = $this
            ->conn
            ->query("UPDATE apt_orders 
									SET AptOrderStatusID = 9
									WHERE AptOrderID = '" . $order_id . "'");
        $insert = $this
            ->conn
            ->query("INSERT INTO apt_payment_transfers 
										(AptOrderID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,
										Status,
										CreatedBy,
										CreatedDate
										) 
									VALUES 
										('" . $order_id . "',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $trf_date . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										0,
										'" . $user_id . "',
										'" . $this->get_current_time() . "'
										) ");

        if ($insert)
        {
            $insert_id = $this
                ->conn->insert_id;
            $dt = $this->getPharmacyByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 10;
                $description = 'Log Order Pharmacy, created by sistem api';
                $pharmacy_id = $dt['PharmacyID'];
                $this->createOrderLogPharmacy($order_id, $order_status_id, $pharmacy_id, $description);
            }
            return $insert_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Process Payment Doctor Status By User
     */
    public function processPaymentDoctor($order_id, $payment_type_id, $unique_code = 0)
    {

        /* Generate Order No (Invoice Number) */
        //Get Last Order ID +1
        $check_orderid = $this
            ->conn
            ->query("SELECT IFNULL(MAX(Right(OrderNo,8)),0) AS OrderNo
											FROM doc_orders_current 
											WHERE 
												DATE_FORMAT(OrderDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(OrderDate, '%Y')='" . date('Y') . "'");
        $data_orderid = $check_orderid->fetch_assoc();

        if ($data_orderid['OrderNo'] == 0)
        {
            //Start From First
            $num = date('y') . date('m') . "1001";
            $order_no = "DOC" . $num;
        }
        else
        {
            //Continue Number +1
            $num = $data_orderid['OrderNo'] + 1;
            $order_no = "DOC" . $num;
        }
        /* End generate */

        //Check If its free or no
        $check_price = $this
            ->conn
            ->query("SELECT * FROM doc_orders_current WHERE OrderID='" . $order_id . "' ");
        $data_price = $check_price->fetch_assoc();
        $order_price = $data_price['TotalPrice'];
        $total_payment = $order_price + $unique_code;

        // if order price '0', payment type id cash
        if ($order_price == 0)
        {
            $myStatus = "4";
        }
        else
        {
            $myStatus = "3";
        }

        //Update
        $update = $this
            ->conn
            ->query("UPDATE doc_orders_current SET 
										OrderStatusID = '" . $myStatus . "',
										OrderNo = '" . $order_no . "',
										PaymentTypeID = '" . $payment_type_id . "',
										UniqueCode = " . $unique_code . ",
										TotalPrice = " . $total_payment . "
									WHERE 
										OrderID = '" . $order_id . "'");
        // echo "UPDATE doc_orders_current SET
        // 							OrderStatusID = '".$myStatus."',
        // 							OrderNo = '".$order_no."',
        // 							PaymentTypeID = '".$payment_type_id."',
        // 							UniqueCode = ".$unique_code.",
        // 							TotalPrice = ".$total_payment."
        // 						WHERE
        // 							OrderID = '".$order_id."'";
        if ($update)
        {
            $dt = $this->getDoctorByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = $myStatus;
                $description = 'Log Order Doctor, created by sistem api';
                $doctor_id = $dt['DoctorID'];
                $this->createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description);
            }
            $this->dealNotificationDoctor($order_id);
            $this->NoDealNotificationDoctor($order_id);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create Payment Confirmation (Nurse Order)
     */
    public function confirmPaymentTransferDoctor($user_id, $order_id, $payment_accound_id, $bank_name, $account_name, $account_no, $trf_date, $total)
    {

        $this
            ->conn
            ->query("UPDATE doc_orders_current SET OrderStatusID = 9	WHERE OrderID = '" . $order_id . "'");
        $insert = $this
            ->conn
            ->query("INSERT INTO doc_payment_transfers 
										(OrderID,
										SenderBankName,
										SenderBankAccName,
										SenderBankAccNo,
										TransferDate,
										PaymentAccountID,
										Total,
										Status,
										CreatedBy,
										CreatedDate
										) 
									VALUES 
										('" . $order_id . "',
										'" . $bank_name . "', 
										'" . $account_name . "', 
										'" . $account_no . "',
										'" . $trf_date . "',
										'" . $payment_accound_id . "',
										'" . $total . "',
										0,
										'" . $user_id . "',
										'" . $this->get_current_time() . "'
										) ");

        if ($insert)
        {
            $insert_id = $this
                ->conn->insert_id;
            $dt = $this->getDoctorByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $order_status_id = 9;
                $description = 'Log Order Doctor, created by sistem api';
                $doctor_id = $dt['DoctorID'];
                $this->createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description);
            }
            return $insert_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Create Chat Message to Nurse
     */
    public function createChat($order_id, $message, $from, $to)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO nrz_chat 
									(OrderID, 
									Message, 
									ChatFrom,
									ChatTo,
									ChatDate
									) 
								VALUES 
									('" . $order_id . "', 
									'" . $message . "',
									'" . $from . "',
									'" . $to . "',
									'" . $this->get_current_time() . "'
									) ");

        if ($insert)
        {
            $this->chatNotification($order_id, $message, $to);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create Chat Image Message to Nurse
     */
    public function createChatFile($order_id, $message, $from, $to, $filename)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO nrz_chat 
									(OrderID, 
									Message, 
									ChatFrom,
									ChatTo,
									ChatDate,
									IsFile,
									Filename
									) 
								VALUES 
									('" . $order_id . "', 
									'" . $message . "',
									'" . $from . "',
									'" . $to . "',
									'" . $this->get_current_time() . "',
									'1',
									'" . $filename . "'
									) ");

        if ($insert)
        {

            $url = "";
            $current_id = $this
                ->conn->insert_id;

            $query_get = $this
                ->conn
                ->query("SELECT   
											ChatID,
											(CASE WHEN Filename IS NOT NULL THEN CONCAT( '" . $this->uploaddir . "', '/chats/nurse_orders/', OrderID,'/',Filename) ELSE '' END) AS url
										FROM nrz_chat
										WHERE ChatID = '" . $current_id . "'");

            if (mysqli_num_rows($query_get) > 0)
            {
                $row = $query_get->fetch_assoc();
                $url = $row['url'];
            }

            $this->chatNotification($order_id, $message, $to, $url);
            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Create Chat Message to Doctor
     */
    public function createChatDoctor($order_id, $message, $from, $to)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO doc_chat 
									(OrderID, 
									Message, 
									ChatFrom,
									ChatTo,
									ChatDate
									) 
								VALUES 
									('" . $order_id . "', 
									'" . $message . "',
									'" . $from . "',
									'" . $to . "',
									'" . $this->get_current_time() . "'
									) ");

        if ($insert)
        {
            $this->chatDoctorNotification($order_id, $message, $to);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Create Chat Image Message to Doctor
     */
    public function createChatFileDoctor($order_id, $message, $from, $to, $filename)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO doc_chat 
									(OrderID, 
									Message, 
									ChatFrom,
									ChatTo,
									ChatDate,
									IsFile,
									Filename
									) 
								VALUES 
									('" . $order_id . "', 
									'" . $message . "',
									'" . $from . "',
									'" . $to . "',
									'" . $this->get_current_time() . "',
									'1',
									'" . $filename . "'
									) ");

        if ($insert)
        {

            $url = "";
            $current_id = $this
                ->conn->insert_id;

            $query_get = $this
                ->conn
                ->query("SELECT   
											ChatID,
											(CASE WHEN Filename IS NOT NULL THEN CONCAT( '" . $this->uploaddir . "', '/chats/doctor_orders/', OrderID,'/',Filename) ELSE '' END) AS url
										FROM doc_chat
										WHERE ChatID = '" . $current_id . "'");

            if (mysqli_num_rows($query_get) > 0)
            {
                $row = $query_get->fetch_assoc();
                $url = $row['url'];
            }

            $this->chatDoctorNotification($order_id, $message, $to, $url);
            return $current_id;
        }
        else
        {
            return null;
        }
    }

    /**
     * Give Rating & Feedback Nurse
     */
    public function giveRating($order_id, $rate, $feedback)
    {

        $upd = $this
            ->conn
            ->query("UPDATE nrz_orders_current SET 
										Rating 		= '" . $rate . "',
										Feedback 	= '" . $feedback . "'
									WHERE 
										OrderID = '" . $order_id . "' ");

        if ($upd)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Give Rating & Feedback Doctor
     */
    public function giveRatingDoctor($order_id, $rate, $feedback)
    {

        $upd = $this
            ->conn
            ->query("UPDATE doc_orders_current SET 
										Rating 		= '" . $rate . "',
										Feedback 	= '" . $feedback . "'
									WHERE 
										OrderID = '" . $order_id . "' ");

        if ($upd)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function chatNotification($order_id, $message, $to, $url = '')
    {

        $custom_data = array(
            'type' => '2', //Notification Chat Nurse
            'body' => $message,
            'title' => "Pesan Baru",
            'ChatDate' => $this->get_chat_time() ,
            'ChatFrom' => '0',
            'Message' => $message,
            'OrderID' => $order_id,
            'url' => $url
        );

        $type = $this->str_before($to, ':');
        $send_to = $this->str_after($to, ':');

        if ($type == "nrz")
        {
            //Notify Nurse
            $query_nrz = $this
                ->conn
                ->query("SELECT * FROM nrz_nurses WHERE NurseID='" . $send_to . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0)
            {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Nurse($row_nrz['FirebaseID'], $custom_data);
            }
        }
        else if ($type == "usr")
        {
            //Notify User
            $query_nrz = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE UserID='" . $send_to . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0)
            {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Patient($row_nrz['FirebaseID'], $custom_data);
            }
        }
    }

    public function chatDoctorNotification($order_id, $message, $to, $url = '')
    {

        $custom_data = array(
            'type' => '32', //Notification Chat Doctor
            'body' => $message,
            'title' => "Pesan Baru",
            'ChatDate' => $this->get_chat_time() ,
            'ChatFrom' => '0',
            'Message' => $message,
            'OrderID' => $order_id,
            'url' => $url
        );

        $type = $this->str_before($to, ':');
        $send_to = $this->str_after($to, ':');

        if ($type == "doc")
        {
            //Notify Doctor
            $query_nrz = $this
                ->conn
                ->query("SELECT * FROM doc_doctors WHERE DoctorID='" . $send_to . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0)
            {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Doctor($row_nrz['FirebaseID'], $custom_data);
            }
        }
        else if ($type == "usr")
        {
            //Notify User
            $query_nrz = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE UserID='" . $send_to . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0)
            {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Patient($row_nrz['FirebaseID'], $custom_data);
            }
        }

    }

    public function dealNotification($order_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $nurse_id = $row['NurseID'];
            $q = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE UserID = '" . $row['UserID'] . "'");
            $user_name = '';
            if (mysqli_num_rows($q) > 0)
            {
                $q = $q->fetch_assoc();
                $user_name = $q['FirstName'] . ' ' . $q['LastName'];
            }

            $custom_data = array(
                'type' => '3', //History Order Nurse
                'body' => "Selamat anda terpilih untuk mengerjakan order atas nama " . $user_name,
                'title' => "Konfirmasi Pekerjaan",
                'OrderID' => $order_id
            );

            //Notify User
            $query_nrz = $this
                ->conn
                ->query("SELECT * FROM nrz_nurses WHERE NurseID='" . $nurse_id . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0)
            {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Nurse($row_nrz['FirebaseID'], $custom_data);
            }
        }
    }

    public function dealNotificationDoctor($order_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM doc_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $doctor_id = $row['DoctorID'];
            $q = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE UserID = '" . $row['UserID'] . "'");
            $user_name = '';
            if (mysqli_num_rows($q) > 0)
            {
                $q = $q->fetch_assoc();
                $user_name = $q['FirstName'] . ' ' . $q['LastName'];
            }
            $custom_data = array(
                'type' => '33', //History Order Doctor
                'body' => "Selamat anda terpilih untuk mengerjakan order atas nama " . $user_name,
                'title' => "Konfirmasi Pekerjaan",
                'OrderID' => $order_id
            );

            //Notify User
            $query_nrz = $this
                ->conn
                ->query("SELECT * FROM doc_doctors WHERE DoctorID='" . $doctor_id . "' AND Active=1 ");
            if (mysqli_num_rows($query_nrz) > 0)
            {
                $row_nrz = $query_nrz->fetch_assoc();

                $this->sendNotification_Doctor($row_nrz['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Process Logout
     */
    public function processLogout($user_id)
    {

        $update = $this
            ->conn
            ->query("UPDATE users SET 
										IsLogin = '0',
										FirebaseID = '',
										Token 	= ''
									WHERE 
										UserID = '" . $user_id . "'");

        if ($update)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //-------------------------------- Another Function Goes Here ------------------------//
    
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password)
    {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array(
            "salt" => $salt,
            "encrypted" => $encrypted
        );
        return $hash;
    }

    /**
     * Count Percentage
     * @param total, percent
     * returns salt and encrypted password
     */
    function countPercentage($angka, $persen)
    {
        $hasil = $persen * $angka / 100;
        return $hasil;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password)
    {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }

    /**
     * Function Get Current Time
     * @param : No
     * returns current time
     */
    function get_current_time()
    {
        // $myDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('GMT'));
        // $myDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        // $now = $myDateTime->format('Y-m-d H:i:s');
        $now = date("Y-m-d H:i:s");
        return $now;
    }

    function get_chat_time()
    {
        // $myDateTime = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('GMT'));
        // $myDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));
        // $now = $myDateTime->format('Y-m-d H:i:s');
        $now = date("Y-m-d H:i:s");
        return $now;
    }

    /**
     * Get String After (:) characters
     * @param string, charactr
     * returns string
     */
    function str_after($string, $substring)
    {
        $pos = strpos($string, $substring);
        if ($pos === false) return $string;
        else return (substr($string, $pos + strlen($substring)));
    }

    /**
     * Get String Before (:) characters
     * @param string, charactr
     * returns string
     */
    function str_before($string, $substring)
    {
        $pos = strpos($string, $substring);
        if ($pos === false) return $string;
        else return (substr($string, 0, $pos));
    }

    /**
     * Function Send SMS code
     * @param : Phone, Code, Name
     * returns boolean
     */
    function send_sms($phone, $code, $name)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL: Hi " . $name . ", Terima Kasih telah melakukan registrasi di VTAL. Mohon masukan kode aktivasi berikut ini: " . $code;
        $url = "https://reguler.zenziva.net/apps/smsapi.php";
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey=' . $userkey . '&passkey=' . $passkey . '&nohp=' . $phone . '&pesan=' . urlencode($message));
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    /**
     * Function Send SMS code
     * @param : Phone, Code, Name
     * returns boolean
     */

  function send_email($subjek , $message)
  {



    date_default_timezone_set('Etc/UTC');
    $email_pengirim = "no_replay@twinzahra.masuk.id";
    $isi=$message;
  //  $subjek=$status;
    $email_tujuan="twinzahrashop@gmail.com";

    $mail = new PHPMailer();

    $mail->IsHTML(true);    // set email format to HTML
    $mail->IsSMTP();   // we are going to use SMTP
    $mail->SMTPAuth   = true; // enabled SMTP authentication
    $mail->SMTPSecure = "ssl";  // prefix for secure protocol to connect to the server
    $mail->Host       = "mail.twinzahra.masuk.id";      // setting GMail as our SMTP server
    $mail->Port       = 465;                   // SMTP port to connect to GMail
    $mail->Username   = $email_pengirim;  // alamat email kamu
    $mail->Password   = "Klapaucius92!";            // password GMail
    $mail->SetFrom($email_pengirim, 'Twinzahra Shop');  //Siapa yg mengirim email
    $mail->Subject    = $subjek;
    $mail->Body       = $isi;
    $mail->AddAddress($email_tujuan);

    if(!$mail->Send()) {
      echo "Eror: ".$mail->ErrorInfo;
      exit;
    }else {
     // echo "<div class='alert alert-success'><strong>Berhasil!</strong> Email telah berhasil dikirim.</div>";
    }
  }


  /**
     * Function Send SMS new password
     * @param : Phone, New Password
     * returns boolean
     */
    function send_sms_password($phone, $code)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL: Hi, berikut adalah password baru anda: " . $code . ", diharapkan segera ubah password anda";
        $url = "https://reguler.zenziva.net/apps/smsapi.php";
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey=' . $userkey . '&passkey=' . $passkey . '&nohp=' . $phone . '&pesan=' . urlencode($message));
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    /**
     * Function Generate Code Verification
     * @param : Digits
     * returns code
     */
    function generatePIN($digits = 6)
    {
        $i = 0;
        $pin = "";
        while ($i < $digits)
        {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    /**
     * Function Generate Random Password (forgot password)
     * @param : Digits
     * returns code
     */
    function randomPassword($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0;$i < $length;++$i)
        {
            $str .= $keyspace[random_int(0, $max) ];
        }
        return $str;
    }

    /**
     * Function Generate API Token
     * @param : Num Digits (optional)
     * returns code
     */
    function generateToken($length = 15)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0;$i < $length;$i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1) ];
        }
        return $randomString;
    }

    /**
     * Function to check image type (JPG, PNG)
     * @param : filename+path
     * returns boolean
     */
    function is_image($path)
    {
        $a = getimagesize($path);
        $image_type = $a[2];
        if (in_array($image_type, array(
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG
        )))
        {
            return true;
        }
        return false;
    }

    /**
     * Function Send GCM to Nurse
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Nurse($firebase_id, $custom_data)
    {

        $registrationIds = array(
            $firebase_id
        );

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );

        $headers = array(
            'Authorization: key=AAAAAnMyp9o:APA91bH42xYduMpF-y0sSkT3iM63HmL-k9cKSi8O5kePGmAMJ8RUJr98bDvNKHzoatdIsM7p2WPmjPttuEZNR99uA9vXayJpmHNBWoIDxpmby6pijVOgxzlIfB5u1oSaoNb60aO0OVx-',

            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        //echo $result;exit;
        
    }

    /**
     * Function Send GCM to Patient
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Patient($firebase_id, $custom_data)
    {

        $registrationIds = array(
            $firebase_id
        );

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );

        $headers = array(
            'Authorization: key=AAAAg12wFPo:APA91bFG6K8jrVOr7A1n_sK_EIyKiTZvBC-SB1jncFzXhEyL3tWzd8pcVxNBqVUj0Cz8wFjSn_BOGIxI_NOtBR-C_Flh0v6vhkwnLXeHSWoPNZuKtoBI8CgFT_1ii0esL7MhSbwAVsSR',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        //echo $result;exit;
        
    }

    /**
     * Function Send GCM to Doctor
     * @param : FirebaseID, Custom Data JSON
     * returns boolean
     */
    function sendNotification_Doctor($firebase_id, $custom_data)
    {

        $registrationIds = array(
            $firebase_id
        );

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $custom_data
        );

        $headers = array(
            'Authorization: key=AAAASnUGctw:APA91bES-Btw7Ufa9jH2pOUQt56hbo0wY45QlLR0527ZH-rgcPC2q_-ujEXMgVj4VhUhwL8KVesG6lmY5RL7rxo6RhrA4MB2mBJUdmVPsA5VQU5HNswXdU92Zv-EEJGvuovTLAJDo5iq',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        // echo $result;exit;
        
    }

    /** Created by elim
     * /** Function get data master_config
     * @param : $configName
     * returns data
     */
    function getConfigForceUpdate($configName)
    {
        $q = $this
            ->conn
            ->query("SELECT * FROM master_config 
			WHERE ConfigName = '" . $configName . "' AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Function Get nurse rating
     * @param : $nurse_id
     * returns data
     */
    public function getRating($nurse_id)
    {
        $query = $this
            ->conn
            ->query("SELECT COUNT(OrderID) AS total_order, SUM(Rating) AS total_rating FROM nrz_orders_current WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID=7 AND Active=1 ");

        $row = $query->fetch_assoc();
        $order = $row['total_order'];
        $rate = $row['total_rating'];

        if ($order > 0)
        {
            $dataRating = round($rate / $order, 1);
            return $dataRating;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Function Get doctor rating
     * @param : $nurse_id
     * returns data
     */
    public function getRatingDoctor($doctor_id)
    {
        $query = $this
            ->conn
            ->query("SELECT COUNT(OrderID) AS total_order, SUM(Rating) AS total_rating FROM doc_orders_current WHERE DoctorID = '" . $doctor_id . "' AND OrderStatusID=6 AND Active=1 ");

        $row = $query->fetch_assoc();
        $order = $row['total_order'];
        $rate = $row['total_rating'];

        if ($order > 0)
        {
            $dataRating = round($rate / $order, 1);
            return $dataRating;
        }
        else
        {
            return 0;
        }
    }

    public function NoDealNotification($order_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_current WHERE OrderID = '" . $order_id . "' AND Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $nurse_id = $row['NurseID'];
            $q = $this
                ->conn
                ->query("SELECT * FROM master_users WHERE UserID = '" . $row['UserID'] . "'");
            $user_name = '';
            if (mysqli_num_rows($q) > 0)
            {
                $q = $q->fetch_assoc();
                $user_name = $q['FirstName'] . ' ' . $q['LastName'];
            }

            $custom_data = array(
                'type' => '0', //Only notif
                'body' => "Mohon maaf Order atas nama " . $user_name . " telah diberikan ke pihak lain",
                'title' => "Konfirmasi Pekerjaan",
                'OrderID' => $order_id
            );

            //Notify User
            $query_nrz = $this
                ->conn
                ->query("SELECT a.NurseID, b.FirebaseID 
				FROM nrz_orders_nurse_accept a 
				JOIN nrz_nurses b ON a.NurseID = b.NurseID
				WHERE a.OrderID='" . $order_id . "' AND a.NurseID != '" . $nurse_id . "' ");

            if (mysqli_num_rows($query_nrz) > 0)
            {
                // $row_nrz = $query_nrz->fetch_assoc();
                while ($row_nrz = $query_nrz->fetch_assoc())
                {
                    $this->sendNotification_Nurse($row_nrz['FirebaseID'], $custom_data);
                }
            }
        }
    }

    public function NoDealNotificationDoctor($order_id)
    {
        $query = $this
            ->conn
            ->query("SELECT a.*,
										b.FirstName,
										b.LastName 
										FROM doc_orders_current a
										LEFT JOIN master_users b ON b.UserID = a.UserID
										WHERE a.OrderID = '" . $order_id . "' 
										AND a.Active=1 LIMIT 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            $row = $query->fetch_assoc();
            $doctor_id = $row['DoctorID'];
            $user_name = $row['FirstName'] . ' ' . $row['LastName'];
            $custom_data = array(
                'type' => '0', //History Order Doctor
                'body' => "Mohon maaf Order atas nama " . $user_name . " telah diberikan ke pihak lain",
                'title' => "Konfirmasi Pekerjaan",
                'OrderID' => $order_id
            );

            //Notify User
            $query2 = $this
                ->conn
                ->query("SELECT a.DoctorID, b.FirebaseID 
				FROM doc_orders_doctor_accept a 
				JOIN doc_doctors b ON a.DoctorID = b.DoctorID
				WHERE a.OrderID='" . $order_id . "' AND a.DoctorID != '" . $doctor_id . "' ");

            if (mysqli_num_rows($query2) > 0)
            {

                while ($row2 = $query2->fetch_assoc())
                {
                    $this->sendNotification_Doctor($row2['FirebaseID'], $custom_data);
                }
            }
        }
    }

    public function createUser2($firstname, $lastname, $firebase_id, $firebase_time, $device_brand, $device_model, $device_serial, $device_os, $google_user_id, $email)
    {

        //Generate Encrypt Password
        // $hash 				= $this->hashSSHA($password);
        // $encrypted_password = $hash["encrypted"];
        // $salt_password 		= $hash["salt"];
        $code = $this->generatePIN();

        //Generate Token
        $token = $this->generateToken();

        // $ref = "0";
        // if($referral_by!=""){
        // 	$ref = $referral_by;
        // }
        $insert = $this
            ->conn
            ->query("INSERT INTO users 
									(FirstName, 
									LastName, 
									Token,
									FirebaseID,
									FirebaseTime,
									DeviceBrand,
									DeviceModel,
									DeviceSerial,
									DeviceOS,
									CreatedDate,
									ActivationCode,
									GoogleUserID,
									Email
									) 
								VALUES 
									('" . $firstname . "', 
									'" . $lastname . "',
									'" . $token . "',
									'" . $firebase_id . "',
									'" . $firebase_time . "',
									'" . $device_brand . "',
									'" . $device_model . "',
									'" . $device_serial . "',
									'" . $device_os . "',
									'" . $this->get_current_time() . "',
									'" . $code . "',
									'" . $google_user_id . "',
									'" . $email . "'
									) ");

        $q2 = $this
            ->conn
            ->query("SELECT * FROM users WHERE Email= '" . $email . "' AND GoogleUserID = '" . $google_user_id . "' ");
        if ($insert)
        {
            // $name = $firstname;
            // $this->send_sms($phone, $code, $name);
            // $arr[] = array(
            // 	"FirstName" 	=> $firstname,
            // 	"LastName" 		=> $lastname,
            // 	"PasswordSalt"  => $salt_password,
            // 	"Token" 		=> $token,
            // 	"FirebaseID" 	=> $firebase_id,
            // 	"FirebaseTime"  => $firebase_time,
            // 	"DeviceBrand"   => $device_brand,
            // 	"DeviceModel"   => $device_model,
            // 	"DeviceSerial"  => $device_serial,
            // 	"DeviceOS"      => $device_os,
            // 	"CreatedDate"   => $this->get_current_time(),
            // 	"ActivationCode"=> $code,
            // 	"GoogleUserID"  => $google_user_id,
            // 	"Email"         => $email
            // );
            $arr[] = $q2->fetch_assoc();
            return $arr;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserRegister2($email, $google_user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT 
                                      master_users.*, 
                                      master_patients.PatientName as NameEmergency,
                                      master_patients.Email as EmailEmergency,
                                      master_patients.Gender as GenderEmergency,
                                      master_patients.Telp as PhoneEmergency 
                                  FROM master_users 
                                  LEFT JOIN master_patients ON master_patients.UserID = master_users.UserID AND master_patients.Emergency = 1
                                  WHERE master_users.Email = '" . $email . "' 
                                  AND master_users.GoogleUserID = '" . $google_user_id . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    /**
     * Check if user exist
     */
    public function checkUserRegister3($email, $google_user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM users WHERE Email = '" . $email . "' AND Active = 1 OR GoogleUserID = '" . $google_user_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function updateUserPhone($user_id, $phone)
    {
        $q = $this
            ->conn
            ->query("UPDATE master_users SET 
										Phone 		= '" . $phone . "',
									WHERE 
										UserID = '" . $user_id . "' ");

        $q2 = $this
            ->conn
            ->query("SELECT * FROM master_users 
    		WHERE UserID = '" . $user_id . "' ");
        if ($q)
        {

            if (mysqli_num_rows($q2) > 0)
            {
                $q2 = $q2->fetch_assoc();
                $name = $q2->FirstName;
                $code = $q2->ActivationCode;
                $this->send_sms($phone, $code, $name);
            }

            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * Get user data by id
     */
    public function getUserByID($user_id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT * FROM users WHERE UserID = '" . $user_id . "' ");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    public function getUserByEmailExceptUserID($user_id, $email)
    {
        $query_get = $this
            ->conn
            ->query("SELECT * FROM  users WHERE Email = '" . $email . "' AND UserID != '" . $user_id . "' AND Active = 1");

        if (mysqli_num_rows($query_get) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update user profile and send sms verification
     */
    public function updateProfileSendSMS($user_id, $firstname, $lastname, $phone, $email, $birthdate, $gender, $nik, $birthplace, $address, $height, $weight, $Referral_by)
    {

        $update = $this
            ->conn
            ->query("UPDATE master_users SET 
										FirstName 	= '" . $firstname . "',
										LastName 	= '" . $lastname . "',
										Phone 		= '" . $phone . "',
										Email 		= '" . $email . "',
										BirthDate 	= '" . $birthdate . "',
										Gender 		= '" . $gender . "',
										NIK 		= '" . $nik . "',
										BirthPlace	= '" . $birthplace . "',
										Address 	= '" . $address . "',
										Height	 	= '" . $height . "',
										Weight	 	= '" . $weight . "',
										ModifiedBy	= '" . $user_id . "',
										ModifiedDate= '" . $this->get_current_time() . "',
										ReferralBy  = '" . $Referral_by . "'
									WHERE 
										UserID = '" . $user_id . "'");

        if ($update)
        {
            $q2 = $this->getUserByID($user_id);
            if ($q2 != null)
            {
                $q3 = $q2->fetch_assoc();
                if ($q3['Active'] == 0)
                {
                    $name = $q3['FirstName'];
                    $code = $q3['ActivationCode'];
                    $this->send_sms($phone, $code, $name);
                }
                return $q3;
            }
            else
            {
                return 'not_found';
            }

        }
        else
        {
            return null;
        }
    }

    /**
     * Check if token exist
     */
    public function checkToken2($token, $user_id)
    {
        $query = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE Token = '" . $token . "' AND UserID = '" . $user_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Send push notif to nurse had bid an order and order canceled by user
     */
    public function pushNotifCancelNurse($order_id, $user_id)
    {
        $q = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE UserID='" . $user_id . "'");

        $user_name = '';
        if (mysqli_num_rows($q) > 0)
        {
            $q = $q->fetch_assoc();
            $user_name = $q['FirstName'] . ' ' . $q['LastName'];
        }

        $custom_data = array(
            'type' => '0', //Tidak dialihkan ke page
            'body' => "Mohon maaf Order atas nama " . $user_name . " telah dibatalkan",
            'title' => "Order Nurse",
            'OrderID' => $order_id
        );

        //Notify User
        $q2 = $this
            ->conn
            ->query("SELECT a.*, b.FirebaseID FROM nrz_orders_nurse_accept a
			JOIN nrz_nurses b
			ON b.NurseID = a.NurseID
			WHERE a.OrderID='" . $order_id . "'");
        if (mysqli_num_rows($q2) > 0)
        {
            while ($row = $q2->fetch_assoc())
            {
                $this->sendNotification_Nurse($row['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Send push notif to nurse had bid an order and order canceled by user
     */
    public function pushNotifCancelDoctor($order_id, $user_id)
    {
        $q = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE UserID='" . $user_id . "'");

        $user_name = '';
        if (mysqli_num_rows($q) > 0)
        {
            $q = $q->fetch_assoc();
            $user_name = $q['FirstName'] . ' ' . $q['LastName'];
        }

        $custom_data = array(
            'type' => '0', //Tidak dialihkan ke page
            'body' => "Mohon maaf Order atas nama " . $user_name . " telah dibatalkan",
            'title' => "Order Doctor",
            'OrderID' => $order_id
        );

        //Notify User
        $q2 = $this
            ->conn
            ->query("SELECT a.*, b.FirebaseID FROM doc_orders_doctor_accept a
			JOIN doc_doctors b
			ON b.DoctorID = a.DoctorID
			WHERE a.OrderID='" . $order_id . "'");
        if (mysqli_num_rows($q2) > 0)
        {
            while ($row = $q2->fetch_assoc())
            {
                // echo $row['FirebaseID'];
                $this->sendNotification_Doctor($row['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Send push notif to nurse had bid an order and order give to other nurse
     */
    public function pushNotifBidCancelNurse($order_id, $user_id, $nurse_id_deal)
    {
        $q = $this
            ->conn
            ->query("SELECT * FROM master_users WHERE UserID='" . $user_id . "'");

        $user_name = '';
        if (mysqli_num_rows($q) > 0)
        {
            $q = $q->fetch_assoc();
            $user_name = $q['FirstName'] . ' ' . $q['LastName'];
        }

        $custom_data = array(
            'type' => '0', //Tidak dialihkan ke page
            'body' => "Mohon maaf Order atas nama " . $user_name . " telah telah diberikan ke pihak lain",
            'title' => "Order Nurse",
            'OrderID' => $order_id
        );

        //Notify User
        $q2 = $this
            ->conn
            ->query("SELECT a.*, b.FirebaseID FROM nrz_orders_nurse_accept a
			JOIN nrz_nurses b
			ON b.NurseID = a.NurseID
			WHERE a.OrderID='" . $order_id . "'
			AND a.NurseID != '" . $nurse_id_deal . "'");
        if (mysqli_num_rows($q2) > 0)
        {
            while ($row = $q2->fetch_assoc())
            {
                $this->sendNotification_Nurse($row['FirebaseID'], $custom_data);
            }
        }
    }

    /**
     * Get bills nurse (HOME)
     */
    public function getBills($nurse_id)
    {
        $query_total = $this
            ->conn
            ->query("SELECT IFNULL(SUM(CompanyRevenue),0) AS total_bill FROM nrz_orders_current WHERE NurseID = '" . $nurse_id . "' AND OrderStatusID=7 AND Active=1 AND PaymentTypeID=1 ");
        $row1 = $query_total->fetch_assoc();

        $query_paid = $this
            ->conn
            ->query("SELECT IFNULL(SUM(Total),0) AS total_paid FROM nrz_payment_confirmations WHERE NurseID = '" . $nurse_id . "' AND Status=1 ");
        $row2 = $query_paid->fetch_assoc();

        $total_bill = $row1['total_bill'];
        $total_paid = $row2['total_paid'];

        if ($total_bill > 0)
        {
            $total = ($total_bill - $total_paid);
            return $total;
        }
        else
        {
            return 0;
        }
    }

    function checkNurseBillReachMaximum($nurse_id)
    {
        $bills = $this->getBills($nurse_id);
        $max_bill = $this->getConfig('nurse_maximum_bill')
            ->fetch_assoc();

        $max_bill = $max_bill['Value'];
        if ($bills > $max_bill)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function createOrderLog($order_id, $order_status_id, $nurse_id, $description)
    {
        $q = $this
            ->conn
            ->query("INSERT INTO nrz_orders_logs 
									(OrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									NurseID,
									Description
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
									'" . $nurse_id . "',
									'" . $description . "'
									) ");

        if ($q)
        {
            return $this
                ->conn->insert_id;
        }
        else
        {
            return false;
        }
    }

    public function createOrderLogDoctor($order_id, $order_status_id, $doctor_id, $description)
    {
        $q = $this
            ->conn
            ->query("INSERT INTO doc_orders_logs 
									(OrderID,
									CreatedDate,
									CreatedBy,
									OrderStatusID,
									DoctorID,
									Description
									) 
								VALUES 
									('" . $order_id . "',
									'" . $this->get_current_time() . "',
									'9-',
									'" . $order_status_id . "',
									'" . $doctor_id . "',
									'" . $description . "'
									) ");
        if ($q)
        {
            return $this
                ->conn->insert_id;
        }
        else
        {
            return false;
        }
    }

    public function getOrderLogNurseByOrderId($order_id, $order_status_id)
    {
        $q = $this
            ->conn
            ->query("SELECT * FROM nrz_orders_logs 
    							WHERE OrderID = " . $order_id . " AND OrderStatusID = " . $order_status_id . "
    							ORDER BY LogID DESC
    							LIMIT 1
    						");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    public function getOrderLogDoctorByOrderId($order_id, $order_status_id)
    {
        $q = $this
            ->conn
            ->query("SELECT * FROM doc_orders_logs 
    							WHERE OrderID = " . $order_id . " AND OrderStatusID = " . $order_status_id . "
    							ORDER BY LogID DESC
    							LIMIT 1
    						");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get package laboratorium, around 10KM
     * @param : $lab_id
     * returns array or false
     */
    public function getLabPackageByLabId($lab_id)
    {
        $q = $this
            ->conn
            ->query("SELECT lab_products.*,
									(CASE WHEN Image IS NOT NULL THEN CONCAT( '" . $this->uploaddir . "', '/labproducts/', LabID,'/',Image) ELSE '' END) AS Image
								 FROM lab_products
								 WHERE LabID = " . $lab_id . "
								 AND Active = 1");
        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    public function insertLabOrder($user_id, $lab_id, $lab_products_id, $description, $price)
    {
        $q = $this
            ->conn
            ->query("SELECT IFNULL(MAX(Right(OrderNo,8)),0) AS OrderNo
											FROM lab_orders 
											WHERE 
												DATE_FORMAT(OrderDate, '%m')='" . date('m') . "' AND 
												DATE_FORMAT(OrderDate, '%Y')='" . date('Y') . "'");

        $data_orderid = $q->fetch_assoc();

        if ($data_orderid['OrderNo'] == 0)
        {
            //Start From First
            $num = date('y') . date('m') . "0001";
            $order_no = "LO" . $num;
        }
        else
        {
            //Continue Number +1
            $num = $data_orderid['OrderNo'] + 1;
            $order_no = "LO" . $num;
        }

        $q2 = $this
            ->conn
            ->query("INSERT INTO lab_orders
								(OrderNo, 
								OrderDate, 
								UserID, 
								LabID, 
								PaymentTypeID, 
								LabProductsID, 
								LabProductsDescription, 
								Price, 
								Total, 
								ModifiedBy, 
								CreatedDate)
								VALUES
								('" . $order_no . "',
								'" . $this->get_current_time() . "',
								" . $user_id . ",
								" . $lab_id . ",
								'0',
								" . $lab_products_id . ",
								'" . $description . "',
								" . $price . ",
								" . $price . ",
								'9-',
								'" . $this->get_current_time() . "')");

        if ($q2)
        {
            $q3 = $this->getLaboratoriumOrderById($this
                ->conn
                ->insert_id);
            return $q3;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get package hospital by id
     * @param : $HospitalID
     * returns array or false
     */
    public function getHospitalById($HospitalID)
    {
        $q = $this
            ->conn
            ->query("SELECT *
								 FROM master_hospitals 
								 WHERE HospitalID = " . $HospitalID . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get package laboratorium by id
     * @param : $lab_id
     * returns array or false
     */
    public function getLaboratoriumById($lab_id)
    {
        $q = $this
            ->conn
            ->query("SELECT *
								 FROM lab_laboratoriums 
								 WHERE LabID = " . $lab_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get package laboratorium by id
     * @param : $lab_id
     * returns array or false
     */
    public function getLaboratoriumOrderById($order_id)
    {
        $q = $this
            ->conn
            ->query("SELECT *
								 FROM lab_orders 
								 WHERE OrderID = " . $order_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get package laboratorium by id
     * @param : $lab_id
     * returns array or false
     */
    public function getLaboratoriumOrderByUserId($user_id)
    {
        $q = $this
            ->conn
            ->query("SELECT *
								 FROM lab_orders 
								 WHERE UserID = " . $user_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    public function generateOrderNoByYearMonth($prefix, $last_no, $last_year, $last_month)
    {
        $prefix = 'USW';
        $year = date('y');
        $month = date('m');
        $no = $last_no;

        if ($last_year == $year && $last_month == $month)
        {
            $no = (int)$no + 1;
        }

        $curr_order_no = $prefix . $year . $month . $no;

        return $curr_order_no;
    }

    /**
     * Get Nominal TopUp
     */
    public function getNominalTopUp()
    {

        $query_get = $this
            ->conn
            ->query("SELECT   
											*
										FROM nominal_topup");

        if (mysqli_num_rows($query_get) > 0)
        {
            return $query_get;
        }
        else
        {
            return null;
        }
    }

    /**
     * Get user wallet by user_id
     * @param : $lab_id
     * returns array or false
     */
    public function getUserWalletByUserId($user_id)
    {
        $q = $this
            ->conn
            ->query("SELECT *
								 FROM user_wallet 
								 WHERE UserID = " . $user_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    public function createWalletLog($user_id, $amount)
    {
        //Insert ke table user_wallet_log
        $data_wallet = $this
            ->conn
            ->query("select * from user_wallet where UserID = $user_id");
        if (mysqli_num_rows($data_wallet) > 0)
        {
            //Jika user nya sudah ada, maka update Total topup nya
            $data_wallet_result = $data_wallet->fetch_assoc();
            $wallet_id = $data_wallet_result['WalletID'];
        }

        $data_log = $this
            ->conn
            ->query("select * from user_wallet_log order by WalletLogID desc LIMIT 1");
        if (mysqli_num_rows($data_log) > 0)
        {
            //Jika user nya sudah ada, maka update Total topup nya
            $data_result = $data_log->fetch_assoc();
            $order_number = $data_result['OrderNo'];
        }

        //USW180710000
        $prefix = 'USW';
        $last_year = date('y');
        $last_month = date('m');
        $last_no = (int)10000;
        if ($data_log != null)
        {
            $last_order_no = $order_number;
            $last_no = substr($last_order_no, 7);
            $last_month = substr($last_order_no, 5, 2);
            $last_year = substr($last_order_no, 3, 2);
        }

        $order_no = $this->generateOrderNoByYearMonth($prefix, $last_no, $last_year, $last_month);

        $q_wallet_log = $this
            ->conn
            ->query("INSERT INTO user_wallet_log(
									WalletID,
									OrderNo,
									UserID,
									Amount,
									Description,
									CreatedBy,
									CreatedDate,
									ModifiedBy
								)VALUES(
									" . $wallet_id . ",
									'" . $order_no . "',
									" . $user_id . ",
									" . $amount . ",
									'Top Up wallet User, created by sistem api',
									'9-',
									'" . $this->get_current_time() . "',
									'9-')
								");
    }

    /**
     * Pending Top Up user wallet by user_id
     * @param : $user_id, $amount
     * returns array or false
     */
    public function pendingTopUpUserWalletByUserId($user_id, $amount, $nominal_id, $unique_code, $payment_type_id)
    {
        //Insert into user_wallet_topup with Status 1 (Pending)
        $total = $amount;

        //Jika user nya sudah ada, maka update Total topup nya
        $data_log = $this
            ->conn
            ->query("select * from user_wallet_topup order by OrderID desc LIMIT 1");

        if (mysqli_num_rows($data_log) > 0)
        {
            $data_result = $data_log->fetch_assoc();
            $order_number = $data_result['OrderNo'];
        }
        else
        {
            $last_year = date('y');
            $last_month = date('m');
            $order_number = 'USW' . $last_year . $last_month . '10000';
        }

        //USW180710000
        $prefix = 'USW';
        $last_year = date('y');
        $last_month = date('m');
        $last_no = (int)10000;
        if ($data_log != null)
        {
            $last_order_no = $order_number;
            $last_no = substr($last_order_no, 7);
            $last_month = substr($last_order_no, 5, 2);
            $last_year = substr($last_order_no, 3, 2);
        }

        $generate_order_no = $this->generateOrderNoByYearMonth($prefix, $last_no, $last_year, $last_month);

        $q2 = $this
            ->conn
            ->query("INSERT INTO user_wallet_topup(
									UserID,
									Amount,
									CreatedDate,
									TopUpStatusID,
									NominalID,
									KodeUnik,
									PaymentTypeID,
									OrderNo
								)VALUES(
									" . $user_id . ",
									" . $total . ",
									'" . $this->get_current_time() . "',
									'1',
									'" . $nominal_id . "',
									'" . $unique_code . "',
									'" . $payment_type_id . "',
									'" . $generate_order_no . "'
									)
								");

        if ($q2)
        {
            return $this
                ->conn->insert_id;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get user poin by user_id
     * @param : $user_id
     * returns array or false
     */
    public function getOrderID($topup_id)
    {
        $q = $this
            ->conn
            ->query("SELECT *
								 FROM user_wallet_topup
								 WHERE OrderID = " . $topup_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get user poin by user_id
     * @param : $user_id
     * returns array or false
     */
    public function getUserPoinByUserId($user_id)
    {
        $q = $this
            ->conn
            ->query("SELECT Point
								 FROM master_users
								 WHERE UserID = " . $user_id . "
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get nurse order pending transfer
     * @param :
     * returns array or false
     */
    public function getOrderPendingTransfer()
    {
        $q = $this
            ->conn
            ->query("SELECT *
								 FROM nrz_orders_current 
								 WHERE OrderStatusID = 3
								 AND PaymentTypeID = 2
								 AND Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get nurse order pending transfer
     * @param :
     * returns array or false
     */
    public function updateNrzOrderStatus($order_id, $order_status_id)
    {
        $q = $this
            ->conn
            ->query("UPDATE nrz_orders_current SET 
									OrderStatusID 		= '" . $order_status_id . "',
									ModifiedBy 			= '9-'
								WHERE 
									OrderID = '" . $order_id . "' ");

        if (mysqli_num_rows($q) > 0)
        {
            $dt = $this->getNurseByOrderID($order_id);
            if ($dt != null)
            {
                $dt = $dt->fetch_assoc();

                //create order log
                $order_id = $order_id;
                $description = 'Log Order Nurse, created by sistem api';
                $nurse_id = $dt['NurseID'];
                $this->createOrderLog($order_id, $order_status_id, $nurse_id, $description);
            }
            return $q;
        }
        else
        {
            return false;
        }
    }

    public function checkEmergencyContactExist($user_id)
    {

        $insert = $this
            ->conn
            ->query("SELECT * FROM master_patients
									WHERE Emergency = 1 AND UserID = " . $user_id . " ");

        if (mysqli_num_rows($insert) > 0)
        {
            return true;

        }
        else
        {
            return false;
        }
    }

    public function setEmergencyContact($Name, $user_id, $Phone, $Email, $Gender)
    {
        $check_emergency_contact = $this->checkEmergencyContactExist($user_id);

        if ($check_emergency_contact)
        {
            $update = $this
                ->conn
                ->query("UPDATE master_patients SET 
									PatientName 		= '" . $Name . "',
									Telp 		= '" . $Phone . "',
									Email 		= '" . $Email . "',
									Gender 		= '" . $Gender . "',
									ModifiedBy 			= '9-'
								WHERE 
									UserID = '" . $user_id . "' AND Emergency = 1 ");
            if ($update)
            {
                return true;

            }
            else
            {
                return false;
            }
        }
        else
        {
            $insert = $this
                ->conn
                ->query("INSERT INTO master_patients 
										(PatientName,
										Telp,
										Email,
										UserID,
										Gender,
										CreatedDate,
										Emergency
										) 
									VALUES 
										('" . $Name . "',
										'" . $Phone . "',
										'" . $Email . "',
										'" . $user_id . "',
										'" . $Gender . "',
										'" . $this->get_current_time() . "',
										'1'
										) ");
            if ($insert)
            {
                return true;

            }
            else
            {
                return false;
            }
        }

    }

    /**
     * Get emergency contact
     * @param : $user_id, $latitude, $longitude
     * returns array or false
     */
    public function getEmergencyContact($user_id, $latitude, $longitude)
    {
        $q = $this
            ->conn
            ->query("SELECT
                                    a.PatientName as EmergencyContactName,
                                    a.PatientID,
                                    a.Telp as EmergencyContactTelp,
                                    CONCAT(b.FirstName, ' ',b.LastName ) as NamaUser,
                                    b.UserID,
	                                b.Phone 
                                FROM
                                    master_patients a
                                    LEFT JOIN master_users b ON b.UserID = a.UserID 
								 WHERE 
								  a.UserID = " . $user_id . " 
								  AND 
								  a.Emergency = 1
								 AND 
								 a.Active = 1
								 LIMIT 1");

        if (mysqli_num_rows($q) > 0)
        {
            $q = $q->fetch_assoc();
            //create order log
            $EmergencyContactName = $q['EmergencyContactName'];
            $EmergencyContactTelp = $q['EmergencyContactTelp'];
            $EmergencyID = $q['PatientID'];
            $NamaUser = $q['NamaUser'];
            $UserID = $q['UserID'];
            $Phone = $q['Phone'];
            $url_google_maps = "https://www.google.com/maps/search/?api=1&query=" . $latitude . "," . $longitude . "";
            $this->send_sms_emergency($EmergencyContactName, $EmergencyContactTelp, $NamaUser, $url_google_maps);

            $this->saveEmergencyContactLog($EmergencyContactTelp, $EmergencyID, $UserID, $Phone, $latitude, $longitude);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Function Send SMS code
     * @param : Phone, Code, Name
     * returns boolean
     */
    function send_sms_emergency($EmergencyContactName, $EmergencyContactTelp, $NamaUser, $url_google_maps)
    {
        $userkey = $this->smsuserkey; //userkey lihat di zenziva
        $passkey = $this->smspasskey; // set passkey di zenziva
        $message = "VTAL - Pesan Emergency: Kepada Yth. " . $EmergencyContactName . ", Keluarga Anda yang bernama " . $NamaUser . ", sedang dalam keadaan Emergency/Darurat. Lokasi terakhir yang dikirim oleh dia adalah: " . $url_google_maps;
        $url = "https://reguler.zenziva.net/apps/smsapi.php";
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey=' . $userkey . '&passkey=' . $passkey . '&nohp=' . $EmergencyContactTelp . '&pesan=' . urlencode($message));
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        $results = curl_exec($curlHandle);
        curl_close($curlHandle);
    }

    public function saveEmergencyContactLog($EmergencyContactTelp, $EmergencyID, $UserID, $Phone, $latitude, $longitude)
    {
        $insert = $this
            ->conn
            ->query("INSERT INTO emergency_button_log 
									(from_id, 
									from_telp, 
									to_id,
									to_telp,
									Latitude,
									Longitude,
									CreatedDate,
									Status
									) 
								VALUES 
									('" . $UserID . "',
									'" . $Phone . "',
									'" . $EmergencyID . "', 
									'" . $EmergencyContactTelp . "',
									'" . $latitude . "',
									'" . $longitude . "',
									'" . $this->get_current_time() . "',
									'1'
									) ");

        if ($insert)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkReferralByExist($user_id, $referral_by)
    {
        //check ReferralBy exist on master_users
        $q = $this
            ->conn
            ->query("SELECT ReferralBy
								 FROM master_users 
								 WHERE UserID = '" . $user_id . "'
								 AND
								 (ReferralBy != '' OR ReferralBy = '" . $referral_by . "')
								 LIMIT 1
							    ");

        if (mysqli_num_rows($q) > 0)
        {
            $q_find = $q->fetch_assoc();
            $ref_by = $q_find['ReferralBy'];
            if ($ref_by == $referral_by || $ref_by == '' || $ref_by == '0' || $ref_by == NULL)
            {
                return $this
                    ->conn
                    ->query("SELECT ReferralBy
								 FROM master_users 
								 WHERE UserID = '" . $user_id . "'
								 AND
								 (ReferralBy != '' OR ReferralBy = '" . $referral_by . "')
								 LIMIT 1
							    ");
            }
            else
            {
                return false;
            }
        }
        else
        {
            $check_v_number = substr($referral_by, 0, 2);
            //echo $check_v_number;die();
            if ($check_v_number == 'V-')
            {
                //Jika referral_by nya tidak ada di master_users tetapi depannya "V-" check ke table referral_code_perusahaan['ReferralID']
                $referralby = $this
                    ->conn
                    ->query("SELECT ReferralID as ReferralBy
								 FROM referral_code_perusahaan 
								 WHERE ReferralID = '" . $referral_by . "' AND Active = 1
								 LIMIT 1
							    ");
                if (mysqli_num_rows($referralby) > 0)
                {
                    return $referralby;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                //check ReferralID exist on master_users
                $q_2 = $this
                    ->conn
                    ->query("SELECT ReferralID as ReferralBy
								 FROM master_users 
								 WHERE
								 ReferralID = '" . $referral_by . "'
								 LIMIT 1
							    ");
                if (mysqli_num_rows($q_2) > 0)
                {
                    return $q_2;
                }
                else
                {
                    //bukan referral code yang benar/invalid ReferralBY
                    return false;
                }

            }
        }
    }

    public function checkKodeVoucher($kode_voucher)
    {
        //Pertama cek ke table vocher_promo ada apa nggak voucher promo tersebut
        $query = $this
            ->conn
            ->query("SELECT
                                        a.voucher_code,
                                        b.nominal,
                                        b.potongan_persen
                                    FROM
                                        voucher_promo a
                                      LEFT JOIN master_voucher_promo b ON b.id = a.id_master_voucher_promo
                                        WHERE
                                
                                        a.voucher_code = '" . $kode_voucher . "' AND a.used = 0");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }

    public function checkKodeVoucherAptKanopiAndNrzOrder($kode_voucher)
    {
        //Pertama cek ke table apt_orders
        $query = $this
            ->conn
            ->query("SELECT
                                        voucher_code
                                    FROM
                                        apt_orders
                                
                                        WHERE
                                
                                        voucher_code = '" . $kode_voucher . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return false;
        }
        else
        {
            //kedua cek ke table nrz_orders_current
            $query2 = $this
                ->conn
                ->query("SELECT
                                        voucher_code
                                    FROM
                                        nrz_orders_current
                                
                                        WHERE
                                
                                        voucher_code = '" . $kode_voucher . "'");

            if (mysqli_num_rows($query2) > 0)
            {
                return false;
            }
            else
            {
                //ketiga cek ke table kanopi_nrz_orders_current
                $query3 = $this
                    ->conn
                    ->query("SELECT
                                        voucher_code
                                    FROM
                                        kanopi_nrz_orders_current
                                
                                        WHERE
                                
                                        voucher_code = '" . $kode_voucher . "'");

                if (mysqli_num_rows($query3) > 0)
                {
                    return false;
                }
                else
                {
                    return $query3;
                }
            }
        }
    }

    /**
     * Get data dari master_asuransi
     */
    public function getMasterCategory()
    {
        $query_get = $this
            ->conn
            ->query("SELECT CategoryID, CategoryName from master_category where Active = 1 ORDER BY CategoryName ASC");
        return $query_get;
    }

    public function getDataSuppliers($user_id)
    {
        $query_get = $this
            ->conn
            ->query("SELECT * from suppliers where UserID = '" . $user_id . "' order by SupplierID DESC");
        return $query_get;
    }

    public function getDataProduct($product_id, $user_id, $status, $page, $limit, $search, $search_size, $search_color)
    {

        $condition = '';
        $where = '';

        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            if ($limit == null) {
                $condition .= "Order by pvd.Stock and pvd.Stock DESC OFFSET " . $p . " ";
            }else{

                $condition .= "Order by pvd.Stock and pvd.Stock DESC LIMIT " . $limit . " OFFSET " . $p . " ";
            }
     

        }

        if ($search != null)
        {

            $query = $this
                ->conn
                ->query("SELECT * FROM 
	                                products AS tp
	                                LEFT JOIN image_products AS ip
	                                ON tp.ProductID = ip.ProductID
									
										where (tp.UserID =" . $user_id . "	and tp.Status =" . $status . ") and (ip.isDefault = 1) and tp.ProductName LIKE CONCAT('%','" . $search . "','%')
										
										Order by tp.ProductID ASC");

        }
        else if ($search_size != null)
        {

            $query = $this
                ->conn
                ->query("
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

        }
        else if ($product_id != null)
        {

          $query = $this
            ->conn
            ->query("
  	SELECT * FROM 
	                                products AS tp
	                                LEFT JOIN image_products AS ip
	                                ON tp.ProductID = ip.ProductID
					LEFT JOIN product_variants AS pv
					ON tp.ProductID = pv.ProductID	
					LEFT JOIN product_variant_details AS pvd
					ON pv.ProductVariantID = pvd.ProductVariantID			
					WHERE (tp.UserID =" . $user_id . ") AND (ip.isDefault = 1 AND tp.Status =" . $status . ") AND (tp.ProductID =" . $product_id . " )
										
										Order by tp.ProductID ASC");

        }
        else
        {

            $query = $this
                ->conn
                ->query("SELECT * FROM 
	                                products AS tp
	                                LEFT JOIN image_products AS ip
	                                ON tp.ProductID = ip.ProductID
                                    where (tp.UserID =" . $user_id . "	and tp.Status =" . $status . ") and (ip.isDefault = 1)
                                    Order by tp.ProductID ASC  ");

        }

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function updateProducts($user_id, $skus, $quantity)
    {

        $query = $this
            ->conn
            ->query(" UPDATE product_variant_details 
										SET Stock = Stock - '" . $quantity . "' Where SkuID = '" . $skus . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function cekStock($skus)
    {

        $query = $this
            ->conn
            ->query("Select * from product_variant_details
										where SkuID = '" . $skus . "'");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

  public function getSkus($user_id , $skus)
  {

    $query = $this->conn->query("Select * from product_variant_details
										where UserID = '" . $user_id . "' and SkuID = '" . $skus . "' ");

    if (mysqli_num_rows($query) > 0)
    {
      return $query;
    }
    else
    {
      return null;
    }

  }
    public function getDataVariantProduct($user_id, $product_id)
    {

        $query = $this
            ->conn
            ->query("SELECT tp.ProductID,
										pv.ProductVariantID,
										pvd.SkuID,
										pvd.Barcode,
										tp.ProductName,									
										pv.ProductVariantName,
										pvd.ProductVariantDetailName,
										pvd.PriceRetail,
										pvd.PriceSale,
										pvd.Stock AS Stock,
										ipv.ImageProductVariantName AS ImageVariantProduct
										FROM products AS tp
										LEFT JOIN product_variant_details AS pvd
										ON tp.ProductID = pvd.ProductID
										LEFT JOIN product_variants AS pv
										ON pvd.ProductVariantID = pv.ProductVariantID
										LEFT JOIN image_product_variants AS ipv
										ON pv.ProductVariantID = ipv.ProductVariantID
										where tp.UserID =  '" . $user_id . "' and tp.ProductId = '" . $product_id . "' and  pv.Status= 1 AND  pvd.Status= 1
                                        Order by tp.ProductID and pvd.ProductVariantDetailName DESC");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getProductIDByUserID($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * from products	where UserID = '" . $user_id . "'	and Status = 1
                                        Order by ProductName ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getProductDataIDByUserID($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM 
										products AS tp
										LEFT JOIN product_variants AS pv 
										ON tp.ProductID = pv.ProductID 
										LEFT JOIN product_variant_details AS pvd
										ON pv.ProductVariantID = pvd.ProductVariantID
										LEFT JOIN image_product_variants AS ipv
										ON pv.ProductVariantID = ipv.ProductVariantID
										where tp.UserID =" . $user_id . "	and tp.Status =1
										");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataProducts($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM 
										products AS p
										LEFT JOIN 
										master_brand AS mb
										ON p.BrandID = mb.BrandID
										where p.UserID =" . $user_id . " and p.Status =1
										");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataProductVariants($ProductID)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM
											product_variants AS pv
											LEFT JOIN product_variant_details AS pvd
											ON pvd.ProductVariantID = pv.ProductVariantID
										where pv.ProductID =" . $ProductID . "
										");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataProductsdanVariant($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT p.ProductID , p.CategoryID , p.ProductName , p.Description , pv.ProductVariantName , 
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

										where p.UserID =" . $user_id . " and p.Status =1
										");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataProductVariants2($user_id, $item)
    {

        $query = $this
            ->conn
            ->query("SELECT p.ProductID , p.CategoryID , p.ProductName , p.Description , pv.ProductVariantName , 
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

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataLazada($user_id , $merchant_name)
    {
    
        if ($merchant_name != null) {

            $query = $this
            ->conn
            ->query(" SELECT * from lazada
										where (UserID = '" . $user_id . "' and merchant_name = '" . $merchant_name . "') and active=1
                                       ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }


        }else{

            $query = $this
            ->conn
            ->query(" SELECT * from lazada
										where UserID = '" . $user_id . "' and active=1
                                       ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }


        }

        

    }

    public function getDataMarketplace($marketplace)
    {
    
        $query = $this->conn->query(" SELECT * from marketplace
										where  marketplace_name = '" . $marketplace . "'
                                       ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }


      
        }

  public function getDataToko($user_id , $seller_id)
  {


    if ($seller_id != null) {

        $query = $this->conn->query(" SELECT * from toko
        where  user_id = '" . $user_id . "' and seller_id = '" . $seller_id . "'
       ");

    if (mysqli_num_rows($query) > 0)
    {
        return $query;
    }
    else
    {
        return null;
    }


    }else{

        $query = $this
        ->conn
        ->query(" SELECT * from toko
        where  user_id = '" . $user_id . "'
                                   ");

    if (mysqli_num_rows($query) > 0)
    {
        return $query;
    }
    else
    {
        return null;
    }


    }

    



  }

    
    public function getDataLazadaByMerchant($user_id, $merchant_name)
    {

        $query = $this
            ->conn
            ->query(" SELECT * from lazada
										where UserID = '" . $user_id . "' and merchant_name = '" . $merchant_name . "' 
                                       ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function checkSkus($user_id)
    {

        $query = $this
            ->conn
            ->query(" SELECT SkuID from product_variant_details
										where UserID = '" . $user_id . "'
                                       ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function updateStockProduct($user_id, $sku_id, $stock_fisik)
    {

        $query = $this
            ->conn
            ->query("UPDATE product_variant_details SET Stock =  '" . $stock_fisik . "' Where SkuID = '" . $sku_id . "'");

        if ($query)
        {
            return true;

        }
        else
        {
            return false;
        }

    }

    public function generateBarcode()
    {

        $query = $this
            ->conn
            ->query(" SELECT 
                             pvd.ProductVariantDetailID,
                            pvd.ProductVariantID,
                            pvd.SkuID,
                            pvd.ProductVariantDetailName,
							pv.ProductVariantName,
                            pvd.PriceRetail,
                            pvd.PriceSale,
                            pvd.PriceParty,
                            pvd.Stock,
                            pvd.Barcode,                       
                            pvd.isDefault 
                                     

	FROM 

product_variant_details AS pvd

LEFT JOIN product_variants AS pv

ON pvd.ProductVariantID = pv.ProductVariantID

LEFT JOIN products AS tp
ON pv.ProductID = tp.ProductID

WHERE pv.ProductID = 1 ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataBarcode($user_id, $barcode)
    {

        $query = $this
            ->conn
            ->query("SELECT 
                            pvd.ProductVariantDetailID,
                            pvd.ProductVariantID,
                            pvd.SkuID,
                            tp.ProductName,
                            pv.ProductVariantName,
                            tp.ProductID,
							tp.Unit,
                            pvd.ProductVariantDetailName,
                            pvd.PriceRetail ,
                            pvd.Stock as StockSystem,
                            pvd.Barcode,                    
                            pvd.isDefault 
                                     

	FROM 

product_variant_details AS pvd

LEFT JOIN product_variants AS pv

ON pvd.ProductVariantID = pv.ProductVariantID

LEFT JOIN products AS tp
ON pv.ProductID = tp.ProductID

										
										WHERE pvd.Barcode = '" . $barcode . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataStok($sku)
    {

        $query = $this
            ->conn
            ->query("SELECT 
                           Stock FROM product_variant_details WHERE SkuID = '" . $sku . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataHistory($order_id)
    {

        $query = $this
            ->conn
            ->query("SELECT 
                           * FROM history_orders WHERE order_id = '" . $order_id . "' ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataCart($user_id, $page, $limit)
    {

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query = $this
            ->conn
            ->query("SELECT a.CustomerID , b.SKU as SkuID, b.Quantity , c.ProductName , d.ProductVariantName , e.ProductVariantDetailName, b.Price FROM cart AS a
                                                                                                     
                                                                                                     LEFT JOIN cart_details AS b ON b.CartID = a.CartID
                                                                                                     LEFT JOIN products AS c ON b.ProductID = c.ProductID
                                                                                                     LEFT JOIN product_variants AS d ON b.ProductVariantID = d.ProductVariantID
                                                                                                     LEFT JOIN product_variant_details AS e ON b.ProductVariantDetailID = e.ProductVariantDetailID
                                                                                                     WHERE  a.UserID = '5'
                                                                                                     Order by b.CreatedDate DESC " . $condition);

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataCartDetail($user_id, $page, $limit)
    {

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query = $this
            ->conn
            ->query("SELECT 
a.CartDetailID,
a.SKU,
	a.Price,
	a.Quantity,
	b.ProductName,
	a.SubTotal
 FROM cart_details AS a
 LEFT JOIN products AS b
  ON a.ProductID = b.ProductID
 WHERE  a.UserID = '" . $user_id . "'
Order by a.CreatedDate DESC " . $condition);
        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataOrders($user_id, $page, $limit, $status_id)
    {

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query = $this
            ->conn
            ->query("SELECT order_id , order_number ,marketplace,branch_number ,warehouse_code,  merchant_name,
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

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }
  public function getDataAddressShipping($order_id)
  {


    $query = $this->conn->query("SELECT * from history_order_address_shipping
                                where order_id = '" . $order_id . "'");

    if (mysqli_num_rows($query) > 0)
    {
      return $query;
    }
    else
    {
      return null;
    }
  }
    public function getDataRts($user_id, $page, $limit, $status_id)
    {

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query = $this
            ->conn
            ->query("SELECT order_id , order_number ,marketplace,branch_number ,warehouse_code,
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

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataOrder($user_id, $page, $limit, $order_id)
    {

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query = $this
            ->conn
            ->query("SELECT order_id , order_number ,marketplace,branch_number ,warehouse_code,
																		customer_first_name , customer_last_name ,  price , 
																									items_count , payment_method ,voucher ,  voucher_code , voucher_platform , voucher_seller , 
																									 gift_option ,gift_message , shipping_fee, 
																									shipping_fee_discount_seller , shipping_fee_discount_platform, promised_shipping_times  ,
																									 national_registration_number,  tax_code ,extra_attributes , remarks , delivery_info ,
																									  statuses , created_at , updated_at
																									 FROM 
																									history_orders
                                                                                                    where user_id = '" . $user_id . "' and order_id = '" . $order_id . "'
                                                                                                     Order by created_at DESC " . $condition);

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataOrderItems($user_id, $page, $limit, $order_id)
    {

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query = $this
            ->conn
            ->query("SELECT * FROM 
				history_order_details
                 where order_id = '" . $order_id . "'
                   Order by created_at DESC ");
        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataRtsItems($user_id, $page, $limit, $order_id)
    {

        $condition = '';
        if ($page != '' && $limit != '')
        {
            if ($page == 1)
            {
                $p = 0;
            }
            else
            {
                $p = ($page - 1) * $limit;
            }

            $condition .= "LIMIT " . $limit . " OFFSET " . $p . " ";
        }

        $query = $this
            ->conn
            ->query("SELECT * FROM 
																									history_order_details
                                                                                                    where order_id = '" . $order_id . "'
                                                                                                     Order by created_at DESC " . $condition);
        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getProductItem($product_id)
    {

        $query = $this
            ->conn
            ->query("SELECT * FROM 
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

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getProductItems($UserID , $ProductID)
    {

        $query = $this->conn->query("SELECT pv.ProductVariantID , 
        pv.ProductVariantName ,
        pvd.ProductVariantDetailName,
        pvd.Price,
        pvd.PriceRetail,
        pvd.PriceReseller,
        pvd.Stock,
        pvd.SkuID,
        pvd.Barcode
        FROM product_variants AS pv 
        LEFT JOIN product_variant_details AS pvd
        ON pvd.ProductVariantID = pv.ProductVariantID
        where pv.ProductID = '" . $ProductID . "' and pv.UserID =  '" . $UserID . "' 
        order by pvd.ProductVariantDetailName");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getImageVariant($product_id)
    {

        $query = $this
            ->conn
            ->query("SELECT 
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

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getImagesProducts($ProductID)
    {

        $query = $this
            ->conn
            ->query("
																			
																			SELECT 
																			ip.ImageProductName
																			FROM 
																			image_products AS ip
																			LEFT JOIN products AS p
																			ON ip.ProductID = p.ProductID
																			WHERE p.ProductID = '" . $ProductID . "'								
																			
									
										
																				");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getProductVariant($product_id)
    {

        $query = $this
            ->conn
            ->query("SELECT 
*

FROM 

product_variants AS pv

LEFT JOIN products AS tp

ON pv.ProductID = tp.ProductID



										
										WHERE pv.ProductID = '" . $product_id . "'
									
order by pv.isDefault = 1 DESC
									
										
                                         ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getProductVariantDetail($product_id)
    {

        $query = $this
            ->conn
            ->query("SELECT 
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

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataCategory($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT
                                   *
						
                                    FROM
                                        master_category  as a
							
								        ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataBrand($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT
                                   *
						
                                    FROM
                                        master_brand 
                                        
							
								        ");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataColor($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT 
                                     *
									
                                    FROM
                                        master_color 
				                        
										ORDER BY ColorID DESC");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getOrderNoCurrent($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT
                                                                OrderNO
                                                                
                                                                FROM
                                                                history_orders
                                                                
                                                                ORDER BY OrderNO DESC LIMIT 1");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getProductsID($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT
                                                                ProductID
                                                                
                                                                FROM
                                                                products
                                                                
                                                                ORDER BY ProductID DESC LIMIT 1");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }

    }

    public function getDataVariants($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT 
                                     *
									
                                    FROM
                                        variants
				                        
										ORDER BY VariantID DESC");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataVariantValues($user_id, $variant_id)
    {
        if ($variant_id != null)
        {
            $conditions = " where VariantID =   '" . $variant_id . "' ORDER BY VariantID DESC";
        }
        else
        {
            $conditions = " ORDER BY VariantID DESC";
        }

        $query = $this
            ->conn
            ->query("SELECT
                                                                *
                                                                
                                                                FROM
                                                                variant_values                                                        
                                                                " . $conditions);

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataSize($user_id)
    {

        $query = $this
            ->conn
            ->query("SELECT 
                                     *
									
                                    FROM
                                        master_size 
				                        
										ORDER BY SizeID DESC");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }
    public function getDataSubCategory1($category_id)
    {

        $query = $this
            ->conn
            ->query("SELECT
                                      *
                                    FROM
                                        sub_category1
									WHERE CategoryID = '" . $category_id . "' 
										ORDER BY SubCategoryID1 DESC");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }

    public function getDataSubCategory2($sub_category_id)
    {

        $query = $this
            ->conn
            ->query("SELECT
                                      *
                                    FROM
                                        sub_category2
									WHERE SubCategoryID1 = '" . $sub_category_id . "' 
										ORDER BY SubCategoryID2 DESC");

        if (mysqli_num_rows($query) > 0)
        {
            return $query;
        }
        else
        {
            return null;
        }
    }
    public function insertDataAsuransi($user_id, $id_master_asuransi, $NoPolis, $StartDate, $EndDate, $PackageName, $Benefit, $JenisAsuransi)
    {
        $insert = $this
            ->conn
            ->query("INSERT INTO master_users_data_asuransi 
									(UserID, 
									id_master_asuransi, 
									NoPolis,
									StartDate,
									EndDate,
									PackageName,
									Benefit,
									JenisAsuransi,
									CreatedDate,
									Status
									) 
								VALUES 
									('" . $user_id . "', 
									'" . $id_master_asuransi . "',
									'" . $NoPolis . "',
									'" . $StartDate . "',
									'" . $EndDate . "',
									'" . $PackageName . "',
									'" . $Benefit . "',
									'" . $JenisAsuransi . "',
									'" . $this->get_current_time() . "',
									'1'
									) ");

        if ($insert)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function gantiBulan($bulan)
    {
        $bulan = substr($bulan, 3, 3);

        if ($bulan == "Jan") $bul = "01";
        elseif ($bulan == "Feb") $bul = "02";
        elseif ($bulan == "Mar") $bul = "03";
        elseif ($bulan == "Apr") $bul = "04";
        elseif ($bulan == "Mei") $bul = "05";
        elseif ($bulan == "Jun") $bul = "06";
        elseif ($bulan == "Jul") $bul = "07";
        elseif ($bulan == "Agt") $bul = "08";
        elseif ($bulan == "Sep") $bul = "09";
        elseif ($bulan == "Okt") $bul = "10";
        elseif ($bulan == "Nov") $bul = "11";
        elseif ($bulan == "Des") $bul = "12";

        return $bul;
    }

    public function updateDataAsuransi($id, $id_master_asuransi, $NoPolis, $StartDate, $EndDate, $PackageName, $Benefit, $JenisAsuransi)
    {
        if ($StartDate != "")
        {
            $StartDate = substr($StartDate, 7, 4) . '-' . $this->gantiBulan($StartDate) . '-' . substr($StartDate, 0, 2);
        }

        if ($EndDate != "")
        {
            $EndDate = substr($EndDate, 7, 4) . '-' . $this->gantiBulan($EndDate) . '-' . substr($EndDate, 0, 2);
        }

        $update = $this
            ->conn
            ->query("UPDATE master_users_data_asuransi SET 
									id_master_asuransi 		= '" . $id_master_asuransi . "',
									NoPolis 		= '" . $NoPolis . "',
									StartDate 		= '" . $StartDate . "',
									EndDate 		= '" . $EndDate . "',
									PackageName 		= '" . $PackageName . "',
									Benefit 		= '" . $Benefit . "',
									JenisAsuransi 		= '" . $JenisAsuransi . "',
									ModifiedDate 		= '" . $this->get_current_time() . "'
								WHERE 
									id = '" . $id . "' ");
        if ($update)
        {
            return true;

        }
        else
        {
            return false;
        }
    }

    public function getDetailDataAsuransi($id)
    {

        $update = $this
            ->conn
            ->query("SELECT
                master_users_data_asuransi.id,
                master_users_data_asuransi.UserID,
                master_users_data_asuransi.id_master_asuransi,
                master_users_data_asuransi.NoPolis,
                master_users_data_asuransi.PackageName,
                master_users_data_asuransi.Benefit,
                master_users_data_asuransi.JenisAsuransi,
                master_users_data_asuransi.CreatedDate,
                master_users_data_asuransi.ModifiedDate,
                master_users_data_asuransi.Status,
                CONCAT(
                DATE_FORMAT(master_users_data_asuransi.StartDate,'%d'),' ',
                CASE MONTHNAME(master_users_data_asuransi.StartDate)
                            WHEN 'January' THEN 'Jan'
                            WHEN 'February' THEN 'Feb'
                            WHEN 'March' THEN 'Mar'
                            WHEN 'April' THEN 'Apr'
                            WHEN 'May' THEN 'Mei'
                            WHEN 'June' THEN 'Jun'
                            WHEN 'July' THEN 'Jul'
                            WHEN 'August' THEN 'Agt'
                            WHEN 'September' THEN 'Sep'
                            WHEN 'October' THEN 'Okt'
                            WHEN 'November' THEN 'Nov'
                            WHEN 'December' THEN 'Des'
                END,' ',
                DATE_FORMAT(master_users_data_asuransi.StartDate,'%Y')) as StartDate,
                CONCAT(
                DATE_FORMAT(master_users_data_asuransi.EndDate,'%d'),' ',
                CASE MONTHNAME(master_users_data_asuransi.EndDate)
                            WHEN 'January' THEN 'Jan'
                            WHEN 'February' THEN 'Feb'
                            WHEN 'March' THEN 'Mar'
                            WHEN 'April' THEN 'Apr'
                            WHEN 'May' THEN 'Mei'
                            WHEN 'June' THEN 'Jun'
                            WHEN 'July' THEN 'Jul'
                            WHEN 'August' THEN 'Agt'
                            WHEN 'September' THEN 'Sep'
                            WHEN 'October' THEN 'Okt'
                            WHEN 'November' THEN 'Nov'
                            WHEN 'December' THEN 'Des'
                END,' ',
                DATE_FORMAT(master_users_data_asuransi.EndDate,'%Y')) as EndDate,
                master_asuransi.nama as nama_asuransi
            FROM
                master_users_data_asuransi
                LEFT JOIN master_asuransi ON master_asuransi.id = master_users_data_asuransi.id_master_asuransi 
            WHERE
                master_users_data_asuransi.id = '" . $id . "'");
        if ($update)
        {
            return $update;

        }
        else
        {
            return false;
        }
    }

    public function deleteCartDetailByUserID($user_id, $CartDetailID)
    {

        $update = $this
            ->conn
            ->query("delete from  cart_details
									WHERE 
										UserID = '" . $user_id . "' and CartDetailID = '" . $CartDetailID . "'");

        if ($update)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function selectContactDetailForBloodRequest($UserID)
    {
        $q = $this
            ->conn
            ->query("SELECT
                                    a.PatientName as EmergencyContactName,
                                    a.PatientID,
                                    a.Telp as EmergencyContactTelp,
                                    CONCAT(b.FirstName, ' ',b.LastName ) as NamaUser,
                                    b.UserID,
	                                b.Phone 
                                FROM
                                    master_patients a
                                    LEFT JOIN master_users b ON b.UserID = a.UserID 
								 WHERE 
								  a.UserID = " . $UserID . " 
								 AND 
								 a.Active = 1");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

    public function sendBloodRequest($UserID, $nama, $JenisKelamin, $Umur, $Tinggi, $Berat, $JenisGolonganDarah, $Rhesus, $Alamat, $Phone, $Keterangan, $Jumlahcc, $latitude, $longitude)
    {

        $insert = $this
            ->conn
            ->query("INSERT INTO blood_request 
									(Nama, 
									Gender, 
									Umur,
									Tinggi,
									Berat,
									JenisGolonganDarah,
									Rhesus,
									Alamat,
									Phone,
									Keterangan,
									jumlah_cc,
									Latitude,
									Longitude,
									CreatedDate,
									Status
									) 
								VALUES 
									('" . $nama . "', 
									'" . $JenisKelamin . "',
									'" . $Umur . "',
									'" . $Tinggi . "',
									'" . $Berat . "',
									'" . $JenisGolonganDarah . "',
									'" . $Rhesus . "',
									'" . $Alamat . "',
									'" . $Phone . "',
									'" . $Keterangan . "',
									'" . $Jumlahcc . "',
									'" . $latitude . "',
									'" . $longitude . "',
									'" . $this->get_current_time() . "',
									'1'
									) ");

        if ($insert)
        {

            //check apakah ada nama user yang sama dengan UserID tersebut
            $firstname = explode(' ', trim($nama));

            $check_master_user = $this
                ->conn
                ->query("SELECT UserID FROM master_users where UserID = '" . $UserID . "' AND FirstName LIKE '%" . $firstname[0] . "%'");
            if (mysqli_num_rows($check_master_user) > 0)
            {
                //update master_user yang UserID nya sana dengan value $UserID
                $update_master_user = $this
                    ->conn
                    ->query("UPDATE master_users SET 
                                    Weight 		        = '" . $Berat . "',
                                    Height 		        = '" . $Tinggi . "',
                                    JenisGolonganDarah 	= '" . $JenisGolonganDarah . "',
                                    Rhesus 		        = '" . $Rhesus . "',
                                    ModifiedDate 		= '" . $this->get_current_time() . "'
								WHERE 
									UserID              = '" . $UserID . "' ");
                return true;
            }
            else
            {
                //update master_patient atau buat baru pasien
                $check_master_patient = $this
                    ->conn
                    ->query("SELECT UserID FROM master_patients where UserID = '" . $UserID . "' AND PatientName LIKE '%" . $nama . "%'");
                if (mysqli_num_rows($check_master_patient) > 0)
                {
                    $update_master_user = $this
                        ->conn
                        ->query("UPDATE master_patients SET 
                                    Weight 		        = '" . $Berat . "',
                                    Height 		        = '" . $Tinggi . "',
                                    JenisGolonganDarah 	= '" . $JenisGolonganDarah . "',
                                    Rhesus 		        = '" . $Rhesus . "',
                                    Age 		        = '" . $Umur . "',
                                    ModifiedDate 		= '" . $this->get_current_time() . "'
								WHERE 
									UserID              = '" . $UserID . "'
									AND PatientName LIKE '%" . $nama . "%' ");
                    return true;
                }
                else
                {
                    //insert data master_patient baru
                    $insert_master_patient = $this
                        ->conn
                        ->query("INSERT INTO master_patients 
										(PatientName,
										Telp,
										UserID,
										Gender,
										Age,
										Height,
										Weight,
										JenisGolonganDarah,
										Rhesus,
										Address,
										CreatedDate
										) 
									VALUES 
										('" . $nama . "',
										'" . $Phone . "',
										'" . $UserID . "',
										'" . $JenisKelamin . "',
										'" . $Umur . "',
										'" . $Tinggi . "',
										'" . $Berat . "',
										'" . $JenisGolonganDarah . "',
										'" . $Rhesus . "',
										'" . $Alamat . "',
										'" . $this->get_current_time() . "'
										) ");
                    if ($insert_master_patient)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
            }
        }
        else
        {
            return false;
        }
    }

    public function getUserFamily($user_id)
    {
        $q = $this
            ->conn
            ->query("SELECT 
                    IFNULL( PatientName, '' ) AS PatientName,
                IFNULL( Gender, '' ) AS Gender,
                IFNULL( Age, '' ) AS Age,
                IFNULL( BirthDate, '' ) AS BirthDate,
                IFNULL( Height, '' ) AS Height,
                IFNULL( Weight, '' ) AS Weight,
                IFNULL( UserID, '' ) AS UserID,
                IFNULL( Active, '' ) AS Active,
                IFNULL( Emergency, '' ) AS Emergency,
                IFNULL( Telp, '' ) AS Telp,
                IFNULL( Email, '' ) AS Email,
                IFNULL( Address, '' ) AS Address,
                IFNULL( JenisGolonganDarah, '' ) AS JenisGolonganDarah,
                IFNULL( Rhesus, '' ) AS Rhesus
                FROM
                    master_patients 
                WHERE
                    UserID = " . $user_id . " ");

        if (mysqli_num_rows($q) > 0)
        {
            return $q;
        }
        else
        {
            return false;
        }
    }

}

?>
