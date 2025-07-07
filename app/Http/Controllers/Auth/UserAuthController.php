<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (!$token = auth('user')->attempt($credentials)) {
    //         return response()->json(['error' => 'Invalid credentials'], 401);
    //     }

    //     return response()->json([
    //         'user'  => auth('user')->user(),
    //         'token' => $token,
    //     ]);
    // }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$jwtToken = auth('user')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth('user')->user();

        ChatSession::where('sessionable_id', $user->id)
            ->where('sessionable_type', get_class($user))
            ->delete();

        $n8nSession = ChatSession::create([
            'sessionable_id'   => $user->id,
            'sessionable_type' => get_class($user),
            'token'            => Str::random(40),
            'expires_at'       => now()->addDays(7),
        ]);

        return response()->json([
            'message'           => 'Login successful',
            'user'           => $user,
            'access_token'      => $jwtToken,
            'n8n_session_token' => $n8nSession->token
        ]);
    }

    public function logout()
    {
        try {
            auth('user')->logout();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }

    public function profile()
    {
        return response()->json(auth('user')->user());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed', // requires new_password_confirmation
        ]);

        $user = auth('user')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }
    public function financialuser(Request $request)
    {


        $user = $request->user()->load('transactions', 'goals');

        $transactions = $user->transactions;

        // $expenses = $transactions->where('type_transaction', 'expense')->sum('price');
        // $income = $transactions->where('type_transaction', 'income')->sum('price');

        $transactionsDetailed = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'source' => $transaction->source,
                'type' => $transaction->type_transaction,
                'price' => $transaction->price,
                'category' => $transaction->category,
                'date' => $transaction->created_at->toDateTimeString(),
            ];
        });


        $goals = $user->goals->map(function ($goal) {
            return [
                'id' => $goal->id,
                'name' => $goal->name,
                'target_amount' => $goal->target_amount,
                'saved_amount' => $goal->saved_amount,
                'date' => $goal->created_at->toDateTimeString(),
            ];
        });

        return response()->json([

            'type' => 'user',
            'transactions' => $transactionsDetailed,
            'goals' => $goals,
        ]);
    }

    public function getDetails(Request $request, $id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'ai_api_key' => $user->ai_api_key
        ]);
    }
}
