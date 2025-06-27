<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $User = User::get();
        return view("dashbord.user.view",compact("User"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("dashbord.user.add");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        User::create($request->toArray());
        return to_route("user.index");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user=User::findOrfail($id);
        return view("dashbord.user.update",compact("user"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data=$request->except("_token","_method");
        User::where("id",$id)->update($data);
        return to_route("user.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::where("id",$id)->delete();
        return to_route("user.index");
    }
}
