<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Test03 extends BaseController
{
    public function index()
    {
        $data = array_merge($this->data, [
            'title'         => 'Test03'
        ]);
        return view('test03', $data);
    }
}
		