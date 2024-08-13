<?php
namespace App\Traits;

trait CooperateAuthorizable
{
    public function __construct()
    {
        $this->middleware('permission:create cooperate')->only("store");
        $this->middleware('permission:edit cooperate')->only("update");
        $this->middleware('permission:view cooperate')->only("show", "index");
        $this->middleware('permission:delete cooperate')->only("destroy");
    }
}
