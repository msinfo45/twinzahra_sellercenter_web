<?php

include "../config/model.php";

$db = new Model_user();


		$chSkus = curl_init("https://twinzahra.masuk.id/public/api/shopee.php?request=update_variant_stock");
        curl_setopt($chSkus, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($chSkus, CURLOPT_RETURNTRANSFER, true);
        $resultSkus = curl_exec($chSkus);
        curl_close($chSkus);
        $jsonDecodeSkus = json_decode($resultSkus);
		

		
	 
		
		
		 $subject  = "Syncron otomatis produk shopee berhasil" ;
          $message  = '

<div>
<table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
   <tbody><tr><td>
<table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
 <tbody>
 <tr>
  <td width="100%">
<table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                              <tbody>
                                 <tr>
                                    <td>
                                       <table width="560" cellspacing="0" cellpadding="0" border="0" align="center">
                                          <tbody>
                                             <tr>
                                                <td style="font-size:1px;line-height:1px" height="10">&nbsp;</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
      <tbody>
         <tr>
            <td>
               <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                  <tbody>
                     <tr>
                        <td width="100%">
                           <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                              <tbody>
                                 <tr>
                                    <td style="font-size:1px;line-height:1px" height="10">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <table width="560" cellspacing="0" cellpadding="0" border="0" align="center">
                                          <tbody>
                                             <tr>
                                                <td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
                                                   Hai Seller,
                                                </td>
                                             </tr>
                                             <tr>
                                                <td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td>
                                             </tr>
                                             <tr>
                                                <td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
										Produk di shopee berhasil di syncron otomatis
                                                </td>
                                             </tr>
                                             <tr>
                                                <td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="font-size:1px;line-height:1px" height="10">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td style="font-size:1px;line-height:1px" width="100%" height="1" bgcolor="#ffffff">&nbsp;</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <div style="width:100%;height:1px;display:block" align="center">
      <div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">
         &nbsp;
      </div>
   </div>
   <table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
      <tbody>
         <tr>
            <td>
               <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                  <tbody>
                     <tr>
                        <td width="100%">
                           <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                              <tbody>
                                 <tr>
                                    <td style="font-size:1px;line-height:1px" height="0">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <table style="table-layout:fixed" width="560" cellspacing="0" cellpadding="0" border="0" align="center">
                                          <tbody>
                                             <tr>
                                                <td colspan="2" style="text-align:left;font-family:Helvetica,arial,sans-serif;color:#1f1f1f;font-size:16px;font-weight:bold;height:10px"> </td>
                                             </tr>
                                             <tr>
                                                <td colspan="2" style="text-align:left;font-family:Helveticƒa,arial,sans-serif;color:#1f1f1f;font-size:13px;font-weight:bold">Rincian Skus </td>
                                             </tr>
                                             <tr>
                                                <td style="font-size:1px;line-height:1px" width="" height="10">&nbsp;</td>
                                             </tr> ';
											 
									foreach($jsonDecodeSkus->data as $data) {
			
		$item_id = $data ->item_id;
		$variation_id  = $data ->variation_id;
		$variation_sku = $data ->variation_sku;
		$status = $data ->Status;
			
		$message .= '
				<tr>
                   <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">Item Id: </td>
                    <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">'. $item_id  .'</td>
                    </tr>
					<tr>
                     <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">Variation Id: </td>
                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;vertical-align:top">'. $variation_id .'</td>
                      </tr>
                      <tr>
                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">Variation Sku: </td>
                      <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">'. $variation_sku  .'</td>
                      </tr>
											 
			
											 
					<tr>
                  <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">Status: </td>
                    <td style="word-break:break-word;text-align:left;font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;white-space:nowrap;vertical-align:top" width="280">'. $status  .'</td>
                    </tr><br>
					';
											 
	}
											 
                  $message .=  '<tr>
                                                <td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="font-size:1px;line-height:1px" height="10">&nbsp;</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>

   <div style="width:100%;height:1px;display:block" align="center">
      <div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">
         &nbsp;
      </div>
   </div>
   <div style="width:100%;height:1px;display:block" align="center">
      <div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">
         &nbsp;
      </div>
   </div>
   
   
   <table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
      <tbody>
         <tr>
            <td>
               <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                  <tbody>
                     <tr>
                        <td width="100%">
                           <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                              <tbody>
                                 <tr>
                                    <td style="font-size:1px;line-height:1px" height="20">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <table width="560" cellspacing="0" cellpadding="0" border="0" align="center">
                                          <tbody>
                                      
                                             <tr>
                                                <td style="font-size:1px;line-height:1px" width="100%" height="10">&nbsp;</td>
                                             </tr>
                                             <tr>
                                                <td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#000000;text-align:left;line-height:18px">
                                           
                                                   Hormat Kami,
                                                   <br>
                                                   Twinzahra Shop
                                                  
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <table id="m_-8886884629272111825backgroundTable" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
      <tbody>
         <tr>
            <td>
               <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                  <tbody>
                     <tr>
                        <td width="100%">
                           <table width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" align="center">
                              <tbody>
                                 <tr>
                                    <td style="font-size:1px;line-height:1px" height="10">&nbsp;</td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <table width="560" cellspacing="0" cellpadding="0" border="0" align="center">
                                          <tbody>
                                             <tr>
                                                <td style="font-family:Helvetica,arial,sans-serif;font-size:13px;color:#747474;text-align:center;line-height:18px">
                                                  Download Aplikasi Twinzahra Shop
                                                </td>
                                             </tr>
                                             <tr>
                                                <td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td>
                                             </tr>
                                             <tr>
                                                <td>
                                                   <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                      <tbody>
                                                         <tr>
                                                        
                                                            <center><a href="https://play.google.com/store/apps/details?id=com.project.msinfo.twinzahra" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://play.google.com/store/apps/details?id%3Dcom.shopee.id&amp;source=gmail&amp;ust=1605079754589000&amp;usg=AFQjCNF3Xjc9cfaH08Hol1C6CfYeVZUMTA"><img src="https://ci3.googleusercontent.com/proxy/wMyUGP_9zlO1kmTJ1wI6w5tG3QYq6dXydCJg0ePOV7p6DUBeZlw99BuZZlU0LOW8jD20PqkxMfCK8ZAGJ7m0OnXAWokK0I08RWyEqio=s0-d-e1-ft#https://cf.shopee.sg/file/cacc3e27277d02501b0989fdcbaf18e9" style="width:130px" class="CToWUd" width="130"></a></center>
                                                         
                                                         
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td style="font-size:1px;line-height:1px" width="100%" height="5">&nbsp;</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <div style="width:100%;height:5px;display:block" align="center">
      <div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">
      </div>
   </div>

 
   
   <div class="yj6qo"></div>
   <div class="adL">
   </div>
</div>';


              $sendEmail = $db->send_email($subject , $message);

echo json_encode($jsonDecodeSkus);
?>