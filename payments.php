 
<?php


                    $post = json_decode(file_get_contents("php://input"), true);

                
                      $fp_acc_request = $post['fp_acc'];
                      $fp_store_request = $post['fp_store'];
                      $fp_item_request = $post['fp_item'];
                       $fp_amnt_request = $post['fp_amnt'];
                       $fp_fee_mode = $post['fp_fee_mode'];
                       $fp_currency_request = $post['fp_currency'];
                       $fp_comments_request = $post['fp_comments'];
                       $fp_merchant_ref_request= $post['fp_merchant_ref'];
                       $fp_status_url_request = $post['fp_status_url'];
                       $fp_success_url_request = $post['fp_success_url'];
                       $fp_success_method_request= $post['fp_success_method'];
                       $fp_fail_url_request = $post['fp_fail_url'];
                       $fp_fail_method_request = $post['fp_fail_method'];
                       $security_word = $post['security_word'];
                        
    
                            
                        $fp_paidto = $fp_acc_request ;
                         $fp_fee_mode = $fp_fee_mode;
                         $fp_amnt = $fp_amnt_request;
                         $fp_currency= $fp_currency_request;
                        $fp_store = $fp_store_request;
                        $fp_merchant_ref = $fp_merchant_ref_request;

                        $fp_paidby = "Usdss";
                    
                       $fp_fee_amnt = $post['fp_fee_amnt'];
           
                       $fp_total = $post['fp_total'];
                       
                       $fp_batchnumber = $post['fp_batchnumber'];
                       
                       $fp_timestamp = $post['fp_timestamp'];
    
           $fp_hash= hash('sha256',   $fp_paidto . ':' . $fp_paidby . ':' . $fp_store . ':' . $fp_currency . ':' . $security_word );

            $fp_hash_2 = hash('sha256', $fp_paidto . ':' . $fp_paidby . ':' . $fp_store .  ':' . $fp_amnt . ':' . $fp_fee_amnt . ':' . $fp_fee_mode . ':' . $fp_total . ':' . $fp_batchnumber . ':' . $fp_currency . ':'. $security_word );

//                       <form method="POST" action=$fp_status_url_request>
//                      <input type="Text" name="fp_paidto" value=$fp_paidto><br>
//                      <input type="Text" name="fp_paidby" value=$fp_paidby><br>
//                      <input type="Text" name="fp_amnt" value=$fp_amnt><br>
//                     <input type="Text" name="fp_fee_amnt" value=$fp_fee_amnt><br>
//                      <input type="Text" name="fp_fee_mode" value=$fp_fee_mode><br>
//                     <input type="Text" name="fp_total" value=$fp_total><br>
//                     <input type="Text" name="fp_currency" value=$fp_currency><br>
//                    <input type="Text" name="fp_batchnumber" value=$fp_batchnumber /> <br>
//                    <input type="Text" name="fp_store" value=$fp_store /><br>
//                    <input type="Text" name="fp_timestamp" value=$fp_timestamp /><br>
//                    <input type="Text" name="fp_merchant_ref" value=$fp_merchant_ref /><br>
//                    <input type="Text" name="fp_hash" value=$fp_hash /><br>
//                     <input type="Text" name="fp_hash_2" value=$fp_hash_2/><br>
//                    <input name="" type="submit">
//                    </form>
    
//                            $create = $db->createPaymentFasapay($fp_paidto, $fp_paidby , $fp_amnt ,$fp_fee_amnt , $fp_fee_mode ,  $fp_total ,$fp_currency , $fp_batchnumber ,
//                                                              $fp_store , $fp_timestamp , $fp_merchant_ref ,$fp_hash , $fp_hash_2 );
////                            if ($create) {
//

                                $return = array(
                                                "status" => 200,
                                                "message" => "Warna berhasil ditambahkan",
                                                "fp_paidto" => $fp_paidto ,
                                                 "fp_paidby" => $fp_paidby ,
                                                 "fp_amnt" => $fp_amnt ,
                                                 "fp_fee_amnt" => $fp_fee_amnt ,
                                                 "fp_fee_mode" => $fp_fee_mode ,
                                                 "fp_total" => $fp_total ,
                                                 "fp_currency" => $fp_currency ,
                                                 "fp_batchnumber" => $fp_batchnumber ,
                                                 "fp_store" => $fp_store ,
                                                 "fp_timestamp" => $fp_timestamp ,
                                                 "fp_merchant_ref" => $fp_merchant_ref ,
                                                 "fp_hash" => $fp_hash ,
                                                 "fp_hash_2" => $fp_hash_2 ,
                                                );
//
//                            } else {
//                                $return = array(
//                                                "status" => 404,
//                                                "message" => "Gagal, mohon coba beberapa saat lagi!"
//                                                );
//                            }
//
//
//
                    echo json_encode($return);
//                }


?>
