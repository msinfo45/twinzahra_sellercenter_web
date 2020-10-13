<body onLoad="javascrip:window:print()">
 
<?php
include "config/barcode128.php";
include "config/db_connection.php";
   include "config/model.php";

    $db = new Model_user();
	
$kolom = 6;  // jumlah kolom
$copy = 6; // jumlah copy barcode
$counter = 1;


      $getData = $db->generateBarcode();
	  
                        if ($getData != null) {

                            while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
							//echo bar128(stripslashes($row['Barcode']));
							
							
							
						echo" <table cellpadding='4'  >";
						
						for ($ucopy=1; $ucopy<=$copy; $ucopy++) {
					
					if (($counter-1) % $kolom == '0') { echo "
					<tr>"; }
echo" <th class='merk' align='center' >".substr($row['ProductVariantDetailName'],0,20)."";
echo bar128(stripslashes($row['Barcode']));

echo "</th>
";
if ($counter % $kolom == '0') { echo "</tr>
"; }
$counter++;
}
echo "</table>
";


                            }
							
					
			
						
						
						

						}
?>