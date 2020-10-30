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

	


	//--------------------------------------------------------------------

}
