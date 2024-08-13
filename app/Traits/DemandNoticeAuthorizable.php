<?php
namespace App\Traits;

trait DemandNoticeAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create demandnotice')->only("store");
        $this->middleware('permission:edit demandnotice')->only("update");
        $this->middleware('permission:view demandnotice')->only("show", "index");
        $this->middleware('permission:delete demandnotice')->only("destroy");
    }
}
