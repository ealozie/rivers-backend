<?php
namespace App\Traits;

trait VehicleAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create vehicle')->only("store");
        $this->middleware('permission:edit vehicle')->only("update");
        $this->middleware('permission:view vehicle')->only("show", "index");
        $this->middleware('permission:delete vehicle')->only("destroy");
    }
}
