<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */    
    public function register()
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // $user = \App\Models\User::where('id', $id)->firstOrFail();
        // return view('users.edit', ['book' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // \App\Models\user::findOrFail($id)->update($request->all());
        // return redirect()->route('users.index')
        //     ->with('success','User updated successfully');
    }

    /**
     * 
     */
    public function login(string $email, string $password)
    {
        // $user = \App\Models\User::where('email',$email)->findOrFail();
        // $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // // for when creating
        // // $user->password = Hash::make($password);
        // // $user->save();

        // if (Hash::check($password, $user->password)) {
        //     return view('user.login', compact('user'));
        // }

        // return back()->withErrors(['email' => 'Email or password incorrect.', ]);
    }

    /**
     * 
     */
    public function logout()
    {
        // Auth::logout();

        // request()->session()->invalidate();
        // request()->session()->regenerateToken();

        // return redirect('/login');
    }

}
