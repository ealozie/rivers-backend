<?php
namespace App\Traits;

trait AssessmentAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create assessment')->only("store");
        $this->middleware('permission:edit assessment')->only("update");
        $this->middleware('permission:view assessment')->only("show", "index");
        $this->middleware('permission:delete assessment')->only("destroy");
    }
}
