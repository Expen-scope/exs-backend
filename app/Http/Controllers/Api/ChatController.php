<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use App\Models\ChatSession;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatController extends Controller
{

    public function startSession(Request $request)
    {
        try {
            if (! $entity = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token is absent'], 401);
        }


        ChatSession::where('sessionable_id', $entity->id)
            ->where('sessionable_type', get_class($entity))
            ->delete();

        $session = ChatSession::create([
            'sessionable_id'   => $entity->id,
            'sessionable_type' => get_class($entity),
            'token'            => Str::random(40),
            'expires_at'       => now()->addHours(2),
        ]);

        return response()->json([
            'n8n_session_token' => $session->token,
        ]);
    }


    public function getContext(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided.'], 401);
        }

        $session = ChatSession::with('sessionable')->where('token', $token)->where('expires_at', '>', now())->first();

        if (!$session || !$session->sessionable) {
            return response()->json(['message' => 'Invalid or expired session token.'], 401);
        }

        $entity = $session->sessionable;

        $financialData = $this->getFinancialDataFor($entity);

        $chatHistory = ChatHistory::where('chattable_id', 'like', $entity->id)
            ->where('chattable_type', get_class($entity))
            ->orderBy('created_at', 'asc')
            ->limit(20)
            ->get(['role', 'content']);

        return response()->json([
            'userId'         => $entity->id,
            'userType'       => $entity instanceof User ? 'user' : 'company',
            'geminiApiKey'   => $entity->gemini_api_key,

            'financialData'  => $financialData,
            'chatHistory'    => $chatHistory,
        ]);
    }



private function getFinancialDataFor($entity)
{
    $totalIncome = $entity->transactions()
        ->where('type_transaction', 'income')
        ->where('created_at', '>=', now()->subDays(30))
        ->sum('price');

    $totalExpenses = $entity->transactions()
        ->where('type_transaction', 'expense')
        ->where('created_at', '>=', now()->subDays(30))
        ->sum('price');

    $recentTransactions = $entity->transactions()
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get()
        ->map(function ($transaction) {
            return [
                'source' => $transaction->source,
                'type' => $transaction->type_transaction,
                'price' => $transaction->price,
                'category' => $transaction->category,
                'date' => $transaction->created_at->toDateString(),
            ];
        });

    // --- Initialize all possible data arrays ---
    $activeGoals = [];
    $employees = [];
    $warehouses = [];
    $events = [];

    // --- CHECK THE ENTITY TYPE BEFORE CALLING RELATIONSHIPS ---
    if ($entity instanceof \App\Models\User) {
        // If it's a User, ONLY get user-specific data (goals)
        $activeGoals = $entity->goals()
            ->limit(5)
            ->get()
            ->map(function ($goal) {
                return [
                    'name' => $goal->name,
                    'target_amount' => $goal->target_amount,
                    'saved_amount' => $goal->saved_amount,
                    'progress_percentage' => $goal->target_amount > 0 ? round(($goal->saved_amount / $goal->target_amount) * 100) : 0,
                ];
            });

    } elseif ($entity instanceof \App\Models\Company) {
        // If it's a Company, ONLY get company-specific data
        $employees = $entity->employees()
            ->limit(20)
            ->get()
            ->map(function ($employee) {
                return ['name' => $employee->name, 'position' => $employee->position, 'monthly_salary' => $employee->monthly_salary];
            });

        $warehouses = $entity->warehouses()
            ->limit(20)
            ->get()
            ->map(function ($warehouse) {
                return ['name' => $warehouse->name, 'category' => $warehouse->category, 'quantity' => $warehouse->quantity, 'status' => $warehouse->status, 'location' => $warehouse->location];
            });

        $events = $entity->events()
            ->limit(20)
            ->get()
            ->map(function ($event) {
                return ['name' => $event->name, 'description' => $event->description, 'scheduled_date' => $event->scheduled_date, 'status' => $event->status];
            });
    }

    // --- Return everything together. The arrays will be either empty or filled based on the logic above. ---
    return [
        'financial_summary' => [
            'period' => 'Last 30 days',
            'total_income' => (float) $totalIncome,
            'total_expenses' => (float) $totalExpenses,
            'net_saving' => (float) ($totalIncome - $totalExpenses),
        ],
        'recent_transactions' => $recentTransactions,
        'active_goals' => $activeGoals,
        'employees' => $employees,
        'warehouses' => $warehouses,
        'events' => $events,
    ];
}
}
