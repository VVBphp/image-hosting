<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function index()
    {
        $users = QueryBuilder::for(User::class)
            ->with('telegram')
            ->latest()
            ->paginate()->withQueryString();
        return Inertia::render('User/Listing', compact('users'));
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show(User $user)
    {
    }

    public function edit(User $user)
    {
    }

    public function update(Request $request, User $user)
    {
        if ($request->has('active')) {
            $user->is_active = !$user->is_active;
        }
        if ($request->has('admin')) {
            $user->is_admin = !$user->is_admin;
        }
        if ($request->has('password')) {
            $user->password = \Hash::make($request->input('password'));
        }
        return $user->save();
    }

    public function destroy(User $user)
    {
    }
}
