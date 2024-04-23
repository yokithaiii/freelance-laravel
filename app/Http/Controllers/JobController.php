<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobUpdateRequest;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobImage;
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
                    'file_url' => $file_path,
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
                Storage::disk('public')->delete($image->url);
                $image->delete();
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $jobs = Job::with('user', 'category', 'images')
            ->orderBy('created_at', 'desc')
            ->get();

        Carbon::setLocale('ru');

        $jobs->each(function ($job) {
            $job->created_at_for_humans = Carbon::parse($job->created_at)->diffForHumans();
            $job->date_deadline = Carbon::createFromFormat('Y-m-d', $job->date_deadline)->format('d.m.Y');
            $job->auth_id = Auth::id();
            $job->count = $job->User->job_count;
            $job->detail_info = $job->User->detailInfo;
        });

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $categories = JobCategory::all();
        return Inertia::render('Jobs/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Получение категории, если она указана
        $category = null;
        if ($request->has('category_id')) {
            $category = JobCategory::find($request->category_id);
        }

        // Проверка, является ли цена диапазоном
        $price_in_hour_flag = false;
        if (isset($validatedData['price']) && strpos($validatedData['price'], '-') !== false) {
            $price_in_hour_flag = true;
        }

        // Создание нового заказа
        $job = Job::create(array_merge($validatedData, [
            'price_in_hour_flag' => $price_in_hour_flag,
            'user_id' => Auth::id(),
            'category_id' => $category ? $category->id : null,
        ]));

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
        $job = Job::with('user', 'category', 'images')->find($id);

        Carbon::setLocale('ru');

        $job->created_at_for_humans = Carbon::parse($job->created_at)->diffForHumans();
        $job->date_deadline = Carbon::createFromFormat('Y-m-d', $job->date_deadline)->format('d.m.Y');
        $job->auth_id = Auth::id();
        $job->detail_info = $job->User->detailInfo;

        return Inertia::render('Jobs/Detail', [
            'job' => $job,
        ]);
    }

    public function manage(): Response
    {
        $jobs = Job::query()
            ->where('user_id', Auth::id())
            ->with('category', 'images')
            ->orderBy('created_at', 'desc')
            ->get();

        Carbon::setLocale('ru');

        $jobs->each(function ($job) {
            $job->created_at_for_humans = Carbon::parse($job->created_at)->diffForHumans();
            $job->date_deadline = Carbon::createFromFormat('Y-m-d', $job->date_deadline)->format('d.m.Y');
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
        $job2 = Job::with('user', 'category', 'images')->find($id);
        $categories = JobCategory::all();

        Carbon::setLocale('ru');
        $job2->date = Carbon::parse($job2->date)->format('Y-m-d\TH:i:s');

        return Inertia::render('Jobs/Edit', [
            'job' => $job2,
            'categories' => $categories,
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
        $category = null;
        if ($request->has('category_id')) {
            $category = JobCategory::find($request->category_id);
        }

        // Проверка, является ли цена диапазоном
        $price_in_hour = false;
        if (isset($validatedData['price']) && strpos($validatedData['price'], '-') !== false) {
            $price_in_hour = true;
        }

        // Обновляем запись
        $job->update(array_merge($validatedData, [
            'price_in_hour' => $price_in_hour,
            'category_id' => $category ? $category->id : null,
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
            Storage::disk('public')->delete($image->url);
            $image->delete();
        }

        $job->delete();

        // Возвращаем ответ
        return Redirect::route('jobs.manage')->with('success', 'Заказ успешно удален!');
    }
}
