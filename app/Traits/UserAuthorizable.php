<?php
namespace App\Traits;

trait UserAuthorizable
{

	public function __construct()
    {
        $this->middleware('permission:create user')->only("store");
        $this->middleware('permission:edit user')->only("update");
        $this->middleware('permission:view user')->only("show", "index");
        $this->middleware('permission:delete user')->only("destroy");
    }
    
}
