<?php
namespace App\Traits;

trait DocumentAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create document')->only("store");
        $this->middleware('permission:edit document')->only("update");
        $this->middleware('permission:view document')->only("show", "index");
        $this->middleware('permission:delete document')->only("destroy");
    }
}
