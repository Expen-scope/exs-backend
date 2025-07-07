<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyChatController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id'  => 'required|integer|exists:companies,id',
            'messages'    => 'required|array|min:1',
            'messages.*.role' => 'required|in:user,assistant',
            'messages.*.content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $company_id = $validatedData['company_id'];

        try {
            DB::transaction(function () use ($company_id, $validatedData) {
                foreach ($validatedData['messages'] as $message) {
                    CompanyChatHistory::create([
                        'company_id' => $company_id,
                        'role'       => $message['role'],
                        'content'    => $message['content'],
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save company chat history.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'Company chat history saved successfully.'], 201);
    }
}
