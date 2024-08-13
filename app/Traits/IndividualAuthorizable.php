<?php
namespace App\Traits;

trait IndividualAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create individual')->only("store");
        $this->middleware('permission:edit individual')->only("update");
        $this->middleware('permission:view individual')->only("show", "index");
        $this->middleware('permission:delete individual')->only("destroy");
    }
}
