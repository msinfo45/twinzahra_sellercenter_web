<?php namespace App\Controllers;

class Products extends BaseController
{
	public function index()
	{
		return view('products/index');

	}

	public function load_products()
	{
		
		
		return view('products/view/load_products');
		
	}

	public function load_products_lazada()
	{
		
		
		return view('products/view/load_products_lazada');
		
	}


	
	//--------------------------------------------------------------------

}
