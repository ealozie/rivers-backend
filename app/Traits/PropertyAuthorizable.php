<?php
namespace App\Traits;

trait PropertyAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create property')->only("store");
        $this->middleware('permission:edit property')->only("update");
        $this->middleware('permission:view property')->only("show", "index");
        $this->middleware('permission:delete property')->only("destroy");
    }
}
