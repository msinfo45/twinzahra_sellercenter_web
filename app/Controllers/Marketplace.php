<?php namespace App\Controllers;

class Marketplace extends BaseController
{
    public function index()
    {
       return view('marketplace/index');

    }


    public function load_data()
    {


        return view('marketplace/view/load_data');

    }

    //--------------------------------------------------------------------

}
