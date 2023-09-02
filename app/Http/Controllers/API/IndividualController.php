<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndividualStoreRequest;
use App\Http\Requests\IndividualUpdateRequest;
use App\Models\Individual;
use App\Models\User;
use Hash;
use Illuminate\Support\Str;

/**
 * @tags Individual Resource Service
 */
class IndividualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IndividualStoreRequest $request)
    {
        $validatedData = $request->validated();
        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        //Generate a random password
        $password = Str::Password(8);
        $user->phone_number = $validatedData['phone_number'];
        $user->password = Hash::make($password);
        $user->save();
        $individual = Individual::create($validatedData);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IndividualUpdateRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
