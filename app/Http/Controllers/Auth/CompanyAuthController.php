<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CompanyAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:companies',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $company = Company::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($company);

        return response()->json([
            'company' => $company,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('company')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'company' => auth('company')->user(),
            'token'   => $token,
        ]);
    }

    public function logout()
    {
        try {
            auth('company')->logout();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }

    public function profile()
    {
        return response()->json(auth('company')->user());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $company = auth('company')->user();

        if (!Hash::check($request->current_password, $company->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 403);
        }

        $company->password = Hash::make($request->new_password);
        $company->save();

        return response()->json(['message' => 'Password updated successfully']);
    }
    public function financialData(Request $request)
    {
        $user = $request->user()->load('transactions', 'goals');

        $transactions = $user->transactions;

        $expenses = $transactions->where('type_transaction', 'expense')->sum('price');
        $income = $transactions->where('type_transaction', 'income')->sum('price');

        $goals = $user->goals->map(function ($goal) {
            return [
                'name' => $goal->name,
                'amount' => $goal->amount,
                'progress' => $goal->progress,
            ];
        });

        return response()->json([
            'expenses' => $expenses,
            'income' => $income,
            'goals' => $goals,
        ]);
    }
}
