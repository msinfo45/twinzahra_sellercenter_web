<?php namespace App\Controllers;

class Kasir extends BaseController
{
	public function index()
	{
		return view('kasir/index');

	}

		public function load_item_products()
	{
		
		
		return view('kasir/view/load_item_products');
		
	}



	//--------------------------------------------------------------------

}
