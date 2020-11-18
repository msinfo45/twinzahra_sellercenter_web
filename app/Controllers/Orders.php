<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class Orders extends ResourceController
{

protected $modelName = 'App\Models\Orders_Model';

public function index()
	{
		return view('orders/index');
		//return $this->respond($this->model->findAll());

	}




public function load_pending()
	{
		
		
		return view('orders/view/load_pending');
		
	}

public function load_rts()
	{
		
		
		return view('orders/view/load_rts');
		
	}

	
	public function load_update()
{
	
	
	return view('orders/view/load_update');
	
}

	

public function load_ship()
{
	
	
	return view('orders/view/load_ship');
	
}



public function load_done()
	{
		
		
		return view('orders/view/load_done');
		
	}


	
public function load_filed()
{
	
	
	return view('orders/view/load_filed');
	
}




	//--------------------------------------------------------------------

}
