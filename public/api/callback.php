<?php

/*

   This script demonstrates getting and validating SCI
   payment confirmation data from Perfect Money server

   !!! WARNING !!!
   This sample PHP-script is provided AS IS and you should
   use it at your own risk.
   The only purpose of this script is to demonstrate main
   principles of SCI-payment validation process.
   You MUST modify it before using with your particular
   Perfect Money account.

*/


/* Constant below contains md5-hashed alternate passphrase in upper case.
   You can generate it like this:
   strtoupper(md5('your_passphrase'));
   Where `your_passphrase' is Alternate Passphrase you entered
   in your Perfect Money account.

   !!! WARNING !!!
   We strongly recommend NOT to include plain Alternate Passphrase in
   this script and use its pre-generated hashed version instead (just
   like we did in this script below).
   This is the best way to keep it secure. */
define('ALTERNATE_PHRASE_HASH',  '80F632EBFE5295A9F8933E360EB382DF');

// Path to directory to save logs. Make sure it has write permissions.
define('PATH_TO_LOG',  '/somewhere/out/of/document_root/');

$string=
      $_POST['PAYMENT_ID'].':'.$_POST['PAYEE_ACCOUNT'].':'.
      $_POST['PAYMENT_AMOUNT'].':'.$_POST['PAYMENT_UNITS'].':'.
      $_POST['PAYMENT_BATCH_NUM'].':'.
      $_POST['PAYER_ACCOUNT'].':'.ALTERNATE_PHRASE_HASH.':'.
      $_POST['TIMESTAMPGMT'];

$hash=strtoupper(md5($string));


/* 
   Please use this tool to see how valid hash is generated: 
   https://perfectmoney.com/acct/md5check.html 
*/
if($hash==$_POST['V2_HASH']){ // processing payment if only hash is valid

   /* In section below you must implement comparing of data you received
   with data you sent. This means to check if $_POST['PAYMENT_AMOUNT'] is
   particular amount you billed to client and so on. */

   if($_POST['PAYMENT_AMOUNT']=='250' && $_POST['PAYEE_ACCOUNT']=='U14197965' && $_POST['PAYMENT_UNITS']=='USD'){

      /* ...insert some code to process valid payments here... */

      // uncomment code below if you want to log successfull payments
      /* $f=fopen(PATH_TO_LOG."good.log", "ab+");
      fwrite($f, date("d.m.Y H:i")."; POST: ".serialize($_POST)."; STRING: $string; HASH: $hash\n");
      fclose($f); */

   }else{ // you can also save invalid payments for debug purposes

      // uncomment code below if you want to log requests with fake data
      /* $f=fopen(PATH_TO_LOG."bad.log", "ab+");
      fwrite($f, date("d.m.Y H:i")."; REASON: fake data; POST: ".serialize($_POST)."; STRING: $string; HASH: $hash\n");
      fclose($f); */

		$return = array(
                            "status" => 200,
                            "message" => "Tidak Cocok"
                            );

        //
        echo json_encode($return);
		
   }


}else{ // you can also save invalid payments for debug purposes

   // uncomment code below if you want to log requests with bad hash
   /* $f=fopen(PATH_TO_LOG."bad.log", "ab+");
   fwrite($f, date("d.m.Y H:i")."; REASON: bad hash; POST: ".serialize($_POST)."; STRING: $string; HASH: $hash\n");
   fclose($f); */
     $return = array(
                            "status" => 200,
                            "message" => "Error",
							"data" => $hash
                            );

        //
        echo json_encode($return);

}

?>