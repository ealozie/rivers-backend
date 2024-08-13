<?php
namespace App\Traits;

trait AgencyAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create agency')->only("store");
        $this->middleware('permission:edit agency')->only("update");
        $this->middleware('permission:view agency')->only("show", "index");
        $this->middleware('permission:delete agency')->only("destroy");
    }
}
