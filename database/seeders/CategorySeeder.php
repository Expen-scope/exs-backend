<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $incomeCategories = [
            'Salary', 'Business Income', 'Freelance/Side Hustles', 'Investments', 'Rental Income',
            'Dividends', 'Interest Income', 'Gifts', 'Refunds/Reimbursements', 'Bonuses'
        ];

        $expenseCategories = [
            'Housing', 'Utilities', 'Transportation', 'Groceries', 'Dining Out',
            'Healthcare', 'Insurance', 'Debt Payments', 'Entertainment', 'Personal Care'
        ];

        foreach ($incomeCategories as $cat) {
            Category::create(['name' => $cat, 'type' => 'income']);
        }

        foreach ($expenseCategories as $cat) {
            Category::create(['name' => $cat, 'type' => 'expense']);
        }
    }
}
