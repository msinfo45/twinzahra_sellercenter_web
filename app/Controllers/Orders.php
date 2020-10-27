<?php namespace App\Controllers;

class Orders extends BaseController
{
	public function index()
	{
		return view('orders/index');

	}

public function load_pending()
	{
		
		
		return view('orders/view/load_pending');
		
	}

public function load_rts()
	{
		
		
		return view('orders/view/load_rts');
		
	}


	//--------------------------------------------------------------------

}
