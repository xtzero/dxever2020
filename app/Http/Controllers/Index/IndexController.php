<?php
namespace App\Http\Controllers\Index;

use App\Http\Controllers\Base\AppController;
use App\Service\Index\IndexService;

class IndexController extends AppController
{
    public function index()
    {
        return (new IndexService())->index();
    }

    public function homepage()
    {
        return (new IndexService())->homepage();
    }
}
