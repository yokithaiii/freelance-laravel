<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobUpdateRequest;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobImage;
use App\Models\JobSelectedCategory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{

    protected function saveFiles(Request $request, Job $job): void
    {
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $file_name = $file->getClientOriginalName();
                $file_path = $file->store('uploads', 'public');
                $jobImage = new JobImage([
                    'file_name' => $file_name,
                    'file_path' => $file_path,
                    'job_id' => $job->id,
                ]);
                $jobImage->save();
            }
        }
    }

    protected function deleteFiles(Request $request): void
    {
        if ($request->has('delete_files')) {
            $imageIdsToDelete = $request->input('delete_files');
            $imagesToDelete = JobImage::whereIn('id', $imageIdsToDelete)->get();
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->file_path);
                $image->delete();
            }
        }
    }

    protected function buildCategoryTree($categories, $parentId = null): array
    {
        $branch = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildCategoryTree($categories, $category->id);
                if ($children) {
                    $category->children = $children;
                }
                $branch[] = $category;
            }
        }

        return $branch;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $jobs = Job::with('user', 'images')
            ->orderBy('created_at', 'desc')
            ->get();

        Carbon::setLocale('ru');

        $jobs->each(function ($job) {
            $categories = $job->selectedCategory()->get();
            $selectedCategories = [];
            foreach ($categories as $categoryId) {
                $selectedCategories[] = JobCategory::find($categoryId->category_id);
            }
            $job->created_at_for_humans = Carbon::parse($job->created_at)->diffForHumans();
            $job->date_deadline = Carbon::createFromFormat('Y-m-d', $job->date_deadline)->format('d.m.Y');
            $job->auth_id = Auth::id();
            $job->user->detail_info = $job->User->detailInfo;
            $job->selected_categories = $selectedCategories;
        });

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $categories = JobCategory::all();
        $categoryTree = $this->buildCategoryTree($categories);
        return Inertia::render('Jobs/Create', [
            'categories' => $categoryTree
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Проверка, является ли цена диапазоном
        $price_in_hour_flag = false;
        if (isset($validatedData['price']) && strpos($validatedData['price'], '-') !== false) {
            $price_in_hour_flag = true;
        }

        // Создание нового заказа
        $job = Job::create(array_merge($validatedData, [
            'price_in_hour_flag' => $price_in_hour_flag,
            'user_id' => Auth::id(),
            // 'category_id' => $category ? $category->id : null,
        ]));

        // Получение категории, если она указана
        if ($request->has('category_id')) {
            JobSelectedCategory::where('job_id', $job->id)
                ->delete();

            $jobSelectedCategory = JobSelectedCategory::updateOrCreate([
                'job_id' => $job->id,
                'category_id' => $request->category_id,
            ]);
        }

        if ($request->has('sub_category_id')) {
            $jobSelectedCategory = JobSelectedCategory::updateOrCreate([
                'job_id' => $job->id,
                'category_id' => $request->sub_category_id,
            ]);
        }

        // Увеличиваем job_count для пользователя
        $user = Auth::user();
        if ($user) {
            $user->detailInfo()->increment('jobs_count');
        }

        // Сохраняем файлы
        $this->saveFiles($request, $job);

        return Redirect::route('jobs.index')->with('success', 'Новый заказ успешно добавлен!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $job = Job::with('user', 'images')->find($id);

        Carbon::setLocale('ru');

        $categories = $job->selectedCategory()->get();
        $selectedCategories = [];
        foreach ($categories as $categoryId) {
            $selectedCategories[] = JobCategory::find($categoryId->category_id);
        }

        $job->created_at_for_humans = Carbon::parse($job->created_at)->diffForHumans();
        $job->date_deadline = Carbon::createFromFormat('Y-m-d', $job->date_deadline)->format('d.m.Y');
        $job->auth_id = Auth::id();
        $job->detail_info = $job->User->detailInfo;
        $job->selected_categories = $selectedCategories;

        return Inertia::render('Jobs/Detail', [
            'job' => $job,
        ]);
    }

    public function manage(): Response
    {
        $jobs = Job::query()
            ->where('user_id', Auth::id())
            ->with('images')
            ->orderBy('created_at', 'desc')
            ->get();

        Carbon::setLocale('ru');

        $jobs->each(function ($job) {
            $categories = $job->selectedCategory()->get();
            $selectedCategories = [];
            foreach ($categories as $categoryId) {
                $selectedCategories[] = JobCategory::find($categoryId->category_id);
            }

            $job->created_at_for_humans = Carbon::parse($job->created_at)->diffForHumans();
            $job->date_deadline = Carbon::createFromFormat('Y-m-d', $job->date_deadline)->format('d.m.Y');
            $job->selected_categories = $selectedCategories;
        });

        return Inertia::render('Jobs/Manage', [
            'jobs' => $jobs
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        $job2 = Job::with('user', 'images')->find($id);
        $categories = JobCategory::all();

        $categories2 = $job2->selectedCategory()->get();
        $selectedCategories = [];
        foreach ($categories2 as $categoryId) {
            $selectedCategories[] = JobCategory::find($categoryId->category_id);
        }
        Carbon::setLocale('ru');
        $job2->date = Carbon::parse($job2->date)->format('Y-m-d\TH:i:s');
        $job2->category_id = array_key_exists(0, $selectedCategories) ? $selectedCategories[0]['id'] : null;
        $job2->sub_category_id = array_key_exists(1, $selectedCategories) ? $selectedCategories[1]['id'] : null;

        $categoryTree = $this->buildCategoryTree($categories);

        return Inertia::render('Jobs/Edit', [
            'job' => $job2,
            'categories' => $categoryTree,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobUpdateRequest $request, string $id): JsonResponse|RedirectResponse
    {
        // Проверяем, существует ли запись с указанным id
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $validatedData = $request->validated();

        // Получение категории, если она указана
        if ($request->has('category_id')) {
            JobSelectedCategory::where('job_id', $job->id)
                ->delete();

            $jobSelectedCategory = JobSelectedCategory::updateOrCreate([
                'job_id' => $job->id,
                'category_id' => $request->category_id,
            ]);
        }

        if ($request->has('sub_category_id')) {
            $jobSelectedCategory = JobSelectedCategory::updateOrCreate([
                'job_id' => $job->id,
                'category_id' => $request->sub_category_id,
            ]);
        }

        // Проверка, является ли цена диапазоном
        $price_in_hour_flag = false;
        if (isset($validatedData['price']) && strpos($validatedData['price'], '-') !== false) {
            $price_in_hour_flag = true;
        }

        // Обновляем запись
        $job->update(array_merge($validatedData, [
            'price_in_hour_flag' => $price_in_hour_flag,
            'categories' => $job->selectedCategory
        ]));

        // Сохраняем файлы
        $this->saveFiles($request, $job);

        // Удаление фотографий
        $this->deleteFiles($request);

        // Возвращаем ответ
        return Redirect::route('jobs.manage')->with('success', 'Заказ успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id): JsonResponse|RedirectResponse
    {

        $job = Job::find($id);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        $jobImages = JobImage::where('job_id', $id)->get();
        foreach ($jobImages as $image) {
            Storage::disk('public')->delete($image->file_path);
            $image->delete();
        }

        $job->delete();

        // Возвращаем ответ
        return Redirect::route('jobs.manage')->with('success', 'Заказ успешно удален!');
    }
}
