<?php
namespace App\Traits;

trait DemandNoticeCategoryAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create demandnoticecategory')->only("store");
        $this->middleware('permission:edit demandnoticecategory')->only("update");
        $this->middleware('permission:view demandnoticecategory')->only("show", "index");
        $this->middleware('permission:delete demandnoticecategory')->only("destroy");
    }
}
