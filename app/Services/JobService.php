<?php

namespace App\Services;

use App\Actions\FormatDateAction;
use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobUpdateRequest;
use App\Models\Job;
use App\Models\JobImage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobService
{
    protected CategoryService $categoryService;
    protected FormatDateAction $dateService;
    protected UserService $userService;

    public function __construct(CategoryService $categoryService, FormatDateAction $dateService, UserService $userService)
    {
        $this->categoryService = $categoryService;
        $this->dateService = $dateService;
        $this->userService = $userService;
    }

    public function getJobs(): Collection|JsonResponse|array
    {
        $jobs = Job::with('user', 'images')
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$jobs) {
            return response()->json(['error' => 'job not found'], 404);
        }

        $this->dateService->setLocale('ru');

        $jobs->each(function ($job) {
            $job->selected_categories = $this->categoryService->getSelectedCategories($job);
            $job->created_at_for_humans = $this->dateService->formatDateForHumans($job->created_at);
            $job->date_deadline = $this->dateService->formatDate($job->date_deadline, 'd.m.Y');
            $job->auth_id = $this->userService->getAuthId();
            $job->user->detail_info = $this->userService->getUserDetailInfo($job->user);
        });

        return $jobs;
    }

    public function createJob(JobStoreRequest $request): Job
    {
        $validatedData = $request->validated();

        $price_in_hour_flag = false;
        if (isset($validatedData['price']) && str_contains($validatedData['price'], '-')) {
            $price_in_hour_flag = true;
        }

        $job = Job::create(array_merge($validatedData, [
            'price_in_hour_flag' => $price_in_hour_flag,
            'user_id' => $this->userService->getAuthId(),
        ]));

        $user = Auth::user();

        $this->userService->incrementUserJobCount($user);

        return $job;
    }

    public function getJob(string $id): array|Collection|Model|JsonResponse
    {
        $job = Job::with('user', 'images')->find($id);
        if (!$job) {
            return response()->json(['error' => 'job not found'], 404);
        }

        Carbon::setLocale('ru');

        $job->selected_categories = $this->categoryService->getSelectedCategories($job);
        $job->created_at_for_humans = $this->dateService->formatDateForHumans($job->created_at);
        $job->date_deadline = $this->dateService->formatDate($job->date_deadline, 'd.m.Y');
        $job->auth_id = $this->userService->getAuthId();
        $job->user->detail_info = $this->userService->getUserDetailInfo($job->user);

        return $job;
    }

    public function getMyJobs(): Collection|JsonResponse|array
    {
        $jobs = Job::query()
            ->where('user_id', $this->userService->getAuthId())
            ->with('images')
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$jobs) {
            return response()->json(['error' => 'job not found'], 404);
        }

        Carbon::setLocale('ru');

        $jobs->each(function ($job) {
            $job->created_at_for_humans = $this->dateService->formatDateForHumans($job->created_at);
            $job->date_deadline = $this->dateService->formatDate($job->date_deadline, 'd.m.Y');
            $job->selected_categories = $this->categoryService->getSelectedCategories($job);
        });

        return $jobs;
    }

    public function updateJob(JobUpdateRequest $request, $job): Job
    {
        $validatedData = $request->validated();

        $price_in_hour_flag = false;
        if (isset($validatedData['price']) && str_contains($validatedData['price'], '-')) {
            $price_in_hour_flag = true;
        }

        $job->update(array_merge($validatedData, [
            'price_in_hour_flag' => $price_in_hour_flag,
        ]));

        return $job;
    }

    public function deleteJob($job): void
    {
        $jobImages = JobImage::where('job_id', $job->id)->get();
        foreach ($jobImages as $image) {
            Storage::disk('public')->delete($image->file_path);
            $image->delete();
        }

        $job->delete();
    }

}