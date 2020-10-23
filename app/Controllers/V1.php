<?php namespace App\Controllers;

class v1 extends BaseController
{
	public function index()
	{
		return view('products/index');

	}

	public function load_products()
	{
		return view('products/view/load_products');
		
	}

	public function products()

	{
		
	return view('v1/products');

		//return view('products/view/load_products');
		
	}


	//--------------------------------------------------------------------

}
