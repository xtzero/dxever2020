<?php


namespace App\Http\Controllers\Swipe;


use App\Http\Controllers\Base\AppController;
use App\Service\Swipe\SwipeService;
use Illuminate\Http\Request;

class SwipeController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * 获取轮播图
     * @return false|string
     */
    public function getSwipe()
    {
        return (new SwipeService())->getSwipe();
    }
}
