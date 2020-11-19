<?php namespace App\Controllers;

class Marketplace extends BaseController
{
    public function index()
    {
       return view('marketplace/index');

    }


    public function load_toko()
    {


        return view('marketplace/view/load_toko');

    }

    public function load_marketplace()
    {


        return view('marketplace/view/load_marketplace');

    }  
    public function add()
    {


        return view('marketplace/add');

    }

    public function create_token()
    {


        return view('marketplace/create_token');

    }

    public function copy()
    {


        return view('marketplace/copy');

    }

    public function load_data_copy()
    {


    return view('marketplace/view/load_data_copy');

    }

    public function sync()
    {


        return view('marketplace/sync');

    }
    public function load_data_sync()
    {


        return view('marketplace/view/load_data_sync');

    }


    //--------------------------------------------------------------------

}
