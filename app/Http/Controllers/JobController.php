<?php

namespace App\Http\Controllers;

use App\Actions\BuildCategoryTreeAction;
use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobUpdateRequest;
use App\Models\Job;
use App\Models\JobCategory;
use App\Services\CategoryService;
use App\Services\FileService;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    protected JobService $jobService;
    protected BuildCategoryTreeAction $categoryTreeAction;
    protected FileService $fileService;
    protected CategoryService $categoryService;

    public function __construct(
        JobService $jobService,
        BuildCategoryTreeAction $categoryTreeAction,
        FileService $fileService,
        CategoryService $categoryService
    )
    {
        $this->jobService = $jobService;
        $this->categoryTreeAction = $categoryTreeAction;
        $this->categoryService = $categoryService;
        $this->fileService = $fileService;
    }

    public function index(): Response
    {
        $jobs = $this->jobService->getJobs();

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
        ]);
    }

    public function create(): Response
    {
        $categories = JobCategory::all()->toArray();

        $categoryTree = $this->categoryTreeAction->buildTree($categories);

        return Inertia::render('Jobs/Create', [
            'categories' => $categoryTree
        ]);
    }

    public function store(JobStoreRequest $request): RedirectResponse
    {
        $job = $this->jobService->createJob($request);

        $this->categoryService->updateCategories($request, $job);

        if ($request->hasFile('files')) {
            $this->fileService->saveFiles($request, $job);
        }

        return Redirect::route('jobs.index')->with('success', 'Новый заказ успешно добавлен!');
    }

    public function show(string $id): Response
    {
        $job = $this->jobService->getJob($id);

        return Inertia::render('Jobs/Detail', [
            'job' => $job,
        ]);
    }

    public function manage(): Response
    {
        $jobs = $this->jobService->getMyJobs();

        return Inertia::render('Jobs/Manage', [
            'jobs' => $jobs
        ]);
    }

    public function edit(string $id): Response
    {
        $job = $this->jobService->getJob($id);

        $categories = JobCategory::all()->toArray();
        $categoryTree = $this->categoryTreeAction->buildTree($categories);

        return Inertia::render('Jobs/Edit', [
            'job' => $job,
            'categories' => $categoryTree,
        ]);
    }

    public function update(JobUpdateRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['error' => 'job not found'], 404);
        }

        $job = $this->jobService->updateJob($request, $job);

        $this->categoryService->updateCategories($request, $job);

        if ($request->hasFile('files')) {
            $this->fileService->saveFiles($request, $job);
        }

        if ($request->hasFile('delete_files')) {
            $this->fileService->deleteFiles($request, $job);
        }

        return Redirect::route('jobs.manage')->with('success', 'Заказ успешно обновлен!');
    }

    public function destroy(String $id): RedirectResponse
    {
        $job = $this->jobService->getJob($id);

        $this->jobService->deleteJob($job);

        return Redirect::route('jobs.manage')->with('success', 'Заказ успешно удален!');
    }

}
