<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('admin')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'admin' => auth('admin')->user(),
            'token' => $token,
        ]);
    }

    public function profile()
    {
        return response()->json(auth('admin')->user());
    }

    public function logout()
    {
        auth('admin')->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function createUser(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json(['message' => 'User created', 'user' => $user]);
    }

    public function createCompany(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:companies',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);

        $company = Company::create($data);

        return response()->json(['message' => 'Company created', 'company' => $company]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->only(['name', 'email']));

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    public function updateCompany(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $company->update($request->only(['name', 'email']));

        return response()->json(['message' => 'Company updated', 'company' => $company]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    public function deleteCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json(['message' => 'Company deleted']);
    }
}
