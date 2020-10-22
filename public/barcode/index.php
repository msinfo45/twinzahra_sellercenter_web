<body onLoad="javascrip:window:print()">
 
<?php
include "../config/barcode128.php";
include "../config/db_connection.php";
include "../config/model.php";
include "../include/head.php";
include "../include/slidebar.php";
    $db = new Model_user();
	
$kolom = 6;  // jumlah kolom
$copy = 6; // jumlah copy barcode
$counter = 1;


      $getData = $db->generateBarcode();
	  
                        if ($getData != null) {

						echo'<table id="table_barcode" class="table table-striped table-hover">';
						echo' <tbody>';
                            while ($row = $getData->fetch_assoc()) {										
						
                                $rows[] = $row;				
	
							//echo bar128(stripslashes($row['Barcode']));
							

						echo '<tr>'; 
						echo'<td>';
						 $row['ProductVariantName'] . $row['ProductVariantDetailName'];
						echo bar128(stripslashes($row['Barcode']));

						echo '</td>';
						
						

						}
						
					echo '</tbody></table>';	
					
						}
						
include "../config/footer.php";						
?>