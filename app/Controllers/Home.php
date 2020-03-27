<?php

class Home extends BaseController
{
    public function index()
    {
        return view('home_index');
    }

    public function data()
    {
        return view('home_data', [
            'greeting' => 'Hello there'
        ]);
    }
}