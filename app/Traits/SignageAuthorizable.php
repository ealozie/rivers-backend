<?php
namespace App\Traits;

trait SignageAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create signage')->only("store");
        $this->middleware('permission:edit signage')->only("update");
        $this->middleware('permission:view signage')->only("show", "index");
        $this->middleware('permission:delete signage')->only("destroy");
    }
}
