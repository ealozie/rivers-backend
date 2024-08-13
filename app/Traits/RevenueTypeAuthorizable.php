<?php
namespace App\Traits;

trait RevenueTypeAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create revenuetype')->only("store");
        $this->middleware('permission:edit revenuetype')->only("update");
        $this->middleware('permission:view revenuetype')->only("show", "index");
        $this->middleware('permission:delete revenuetype')->only("destroy");
    }
}
