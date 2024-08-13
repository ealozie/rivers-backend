<?php
namespace App\Traits;

trait DemandNoticeCategoryItemAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create demandnoticecategoryitem')->only("store");
        $this->middleware('permission:edit demandnoticecategoryitem')->only("update");
        $this->middleware('permission:view demandnoticecategoryitem')->only("show", "index");
        $this->middleware('permission:delete demandnoticecategoryitem')->only("destroy");
    }
}
