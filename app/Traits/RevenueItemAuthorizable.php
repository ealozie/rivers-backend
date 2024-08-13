<?php
namespace App\Traits;

trait RevenueItemAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create revenueitem')->only("store");
        $this->middleware('permission:edit revenueitem')->only("update");
        $this->middleware('permission:view revenueitem')->only("show", "index");
        $this->middleware('permission:delete revenueitem')->only("destroy");
    }
}
