<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return "<h1>Users</h1>";
    }

    public function show($id)
    {
        return view('users.show', ['name' => 'Abdusattor']);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        dd($request);
    }
     public function edit($user_id)
    {
        return $user_id . ' ni o\'zgartirish';
    }
}
