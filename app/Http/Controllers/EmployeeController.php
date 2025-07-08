<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:company');
    }

    public function index()
    {
        $company = Auth::user();
        $employees = $company->employees()->get();
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $company = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_salary' => 'required|numeric|min:0',
            'position' => 'required|string|max:255',
        ]);

        $employee = $company->employees()->create($validated);

        return response()->json($employee, 201);
    }

    public function show($id)
    {
        $company = Auth::user();

        $employee = $company->employees()->find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $company = Auth::user();

        $employee = $company->employees()->find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'monthly_salary' => 'sometimes|required|numeric|min:0',
            'position' => 'sometimes|required|string|max:255',
        ]);

        $employee->update($validated);

        return response()->json($employee);
    }

    public function destroy($id)
    {
        $company = Auth::user();

        $employee = $company->employees()->find($id);
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
