<?php namespace App\Controllers;

class v1 extends BaseController
{


    protected $session;

	  function __construct()
    {

        $this->session = \Config\Services::session();
        $this->session->start();


    }

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


	public function users()

	{
		
		
	return view('v1/users');

		
	}


	//--------------------------------------------------------------------

}
