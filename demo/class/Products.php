<?php
class Products {	
   
	private $productsTable = 'products';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	 
	
	public function productList() {
		if(isset($_POST['limit'])){
			$sqlQuery = "SELECT * FROM ".$this->productsTable." 
			ORDER BY id DESC LIMIT ".$_POST["limit"]."";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->execute();
			$result = $stmt->get_result();	
			$productHTML = '';
			while ($product = $result->fetch_assoc()) {		 
				$productHTML .= '
					<div class="row">
					<div class="col-md-4">
					<img src="images/'.$product["image"].'" class="img-thumbnail" />
					</div>
					<div class="col-md-8">
					<h2><a href="#">'.$product["product_name"].'</a></h2>
					<br />			
					</div>
					</div>
					<hr />
					';
			}
			echo $productHTML;
		}
	}
}
?>