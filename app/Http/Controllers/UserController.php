<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function userLogged(Request $request)
    {
        return array_merge([
            'auth_user' => $request->user(),
            'auth_roles' => $request->user() ? $request->user()->getRoleNames() : null,
            'auth_permission' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : null,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $decode_filter = json_decode($request->lazyEvent);
        $filters = $decode_filter->filters;
        $itemsPerPage = 15;

        if($decode_filter)
        {
            $itemsPerPage = $decode_filter->rows;
        }

        return User::with('roles')->filter($filters)->orderByFilters($decode_filter)->latest()->paginate($itemsPerPage);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
