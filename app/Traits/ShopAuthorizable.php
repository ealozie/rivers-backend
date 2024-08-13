<?php
namespace App\Traits;

trait ShopAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create shop')->only("store");
        $this->middleware('permission:edit shop')->only("update");
        $this->middleware('permission:view shop')->only("show", "index");
        $this->middleware('permission:delete shop')->only("destroy");
    }
}
