<?php
namespace App\Http\Controllers\Account;

use App\Http\Controllers\Base\AppController;
use App\Service\Account\AccountService;
use App\Support\AjaxSupport;
use Illuminate\Http\Request;

class AccountController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
}
