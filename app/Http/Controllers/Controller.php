<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $limit;
    protected $lang;

    public function __construct()
    {
        $this->limit = \request()->get('per_page', config('egwk.api.pagination_limit', 25));
        $this->lang = \request()->get('language');
    }

    public function apiAuthCheck()
    {
        return auth()->guard('api')->user() !== null;
    }

    public function help($module = null)
    {
        if (!$module) {
            return config('egwk.api_help');
        }
        return config("egwk.api_help.entries.$module");
    }


}
