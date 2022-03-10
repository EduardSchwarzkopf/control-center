<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserRessource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        return UserRessource::collection(User::all());

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => [Rules\Password::defaults()],
            'is_admin' => 'required|boolean'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'is_admin' => $fields['is_admin'],
            'password' => bcrypt($fields['password'])
        ]);

        return UserRessource::make($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return new UserRessource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        
        $fields = $request->validate([
            'name' => 'string',
            'email' => 'email|unique:users,email,'.$user->id.',id',
            'password' => [Rules\Password::defaults()],
            'is_admin' => 'boolean'
        ]);
        $user->update($fields);

        return new UserRessource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->noContent();

    }
}
