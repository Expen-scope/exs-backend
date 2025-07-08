<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:company');
    }

    public function index()
    {
        $company = Auth::user();
        $warehouses = $company->warehouses()->get();
        return response()->json($warehouses);
    }

    public function store(Request $request)
    {
        $company = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'location' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'last_updated' => 'nullable|date',
        ]);

        $warehouse = $company->warehouses()->create($validated);

        return response()->json($warehouse, 201);
    }

    public function show($id)
    {
        $company = Auth::user();

        $warehouse = $company->warehouses()->find($id);
        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        return response()->json($warehouse);
    }

    public function update(Request $request, $id)
    {
        $company = Auth::user();

        $warehouse = $company->warehouses()->find($id);
        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|integer|min:0',
            'location' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|max:255',
            'last_updated' => 'nullable|date',
        ]);

        $warehouse->update($validated);

        return response()->json($warehouse);
    }

    public function destroy($id)
    {
        $company = Auth::user();

        $warehouse = $company->warehouses()->find($id);
        if (!$warehouse) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }

        $warehouse->delete();

        return response()->json(['message' => 'Warehouse deleted successfully']);
    }
}
