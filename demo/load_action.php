<?php
include_once 'config/Database.php';
include_once 'class/Products.php';

$database = new Database();
$db = $database->getConnection();

$product = new Products($db);

if(!empty($_POST['action']) && $_POST['action'] == 'load_products') {
	$product->productList();
}
?>