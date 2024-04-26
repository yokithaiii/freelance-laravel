<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobSelectedCategory;
use Illuminate\Http\JsonResponse;

class CategoryService
{

    public function getSelectedCategories($job): JsonResponse|array
    {
        if (!$job) {
            return response()->json(['error' => 'job not found'], 404);
        }

        $categories = $job->selectedCategory()->get();
        $selectedCategories = [];
        foreach ($categories as $categoryId) {
            $selectedCategories[] = JobCategory::find($categoryId->category_id);
        }
        return $selectedCategories;
    }

    public function updateCategories($request, Job $job): void
    {
        if ($request->has('category_id')) {
            JobSelectedCategory::where('job_id', $job->id)
                ->delete();

            JobSelectedCategory::updateOrCreate([
                'job_id' => $job->id,
                'category_id' => $request->category_id,
            ]);
        }

        if ($request->has('sub_category_id')) {
            JobSelectedCategory::updateOrCreate([
                'job_id' => $job->id,
                'category_id' => $request->sub_category_id,
            ]);
        }
    }

}