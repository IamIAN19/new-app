<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function list()
    {
        $users = User::with('permissions')->get();
        return view('users.partials.list', compact('users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'raw_password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $rawPassword = $request->raw_password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($rawPassword),
            'raw_password' => $rawPassword,
            'status' => 1,
        ]);

        // Sync permissions
        $permissions = $request->input('permissions', []);
        $permIds = Permission::whereIn('name', $permissions)->pluck('id');
        $user->permissions()->sync($permIds);

        return response()->json(['success' => true, 'message' => 'User created successfully']);
    }

    public function edit($id)
    {
        $user = User::with('permissions')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'raw_password' => $user->raw_password,
            'permissions' => $user->permissions->pluck('name') // returns ['add', 'edit', ...]
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'raw_password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('raw_password')) {
            $updateData['password'] = Hash::make($request->raw_password);
            $updateData['raw_password'] = $request->raw_password;
        }

        $user->update($updateData);

        // Sync permissions
        $permissions = $request->input('permissions', []);
        $permIds = Permission::whereIn('name', $permissions)->pluck('id');
        $user->permissions()->sync($permIds);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        return response()->json(['success' => true]);
    }
}
