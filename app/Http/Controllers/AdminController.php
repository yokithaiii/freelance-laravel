<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Index');
    }

    public function createCategories()
    {
        return Inertia::render('Admin/CreateCategories');
    }

    public function storeCategories(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
        ]);

        $category = new JobCategory([
            'name' => $request->category_name,
            'parent_id' => null,
        ]);
        $category->save();

        if ($request->sub_category_name != '') {
            $sub_category = new JobCategory([
                'name' => $request->sub_category_name,
                'parent_id' => $category->id,
            ]);
            $sub_category->save();
        }

        return response()->json(['message' => 'category added.'], 201);
    }
}
