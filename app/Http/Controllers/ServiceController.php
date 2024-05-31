<?php

namespace App\Http\Controllers;

use App\Actions\BuildCategoryTreeAction;
use App\Http\Requests\ServiceOrderRequest;
use App\Http\Requests\ServiceStoreRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ServiceOrderResource;
use App\Http\Resources\ServiceResource;
use App\Models\JobCategory;
use App\Models\Service;
use App\Models\ServiceCover;
use App\Models\ServiceImage;
use App\Models\ServiceOrder;
use App\Models\ServiceSelectedCategory;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function __construct(BuildCategoryTreeAction $categoryTreeAction)
    {
        $this->categoryTreeAction = $categoryTreeAction;
    }

    public function index()
    {
        $services = Service::with('cover', 'images', 'categories', 'user.detailInfo')
            ->get();

        return Inertia::render('Service/Index', [
            'services' => ServiceResource::collection($services),
        ]);
    }

    public function create() {
        $categories = JobCategory::all()->toArray();

        $categoryTree = $this->categoryTreeAction->buildTree($categories);

        return Inertia::render('Service/Create', [
            'categories' => $categoryTree
        ]);
    }

    public function store(ServiceStoreRequest $request)
    {
        $validatedData = $request->validated();

        $service = Service::create(array_merge($validatedData, [
            'user_id' => Auth::id(),
        ]));

        if ($request->has('service_category_id')) {
            ServiceSelectedCategory::where('service_id', $service->id)
                ->delete();

            ServiceSelectedCategory::updateOrCreate([
                'service_id' => $service->id,
                'category_id' => $request->service_category_id,
            ]);
        }

        if ($request->has('service_sub_category_id')) {
            ServiceSelectedCategory::updateOrCreate([
                'service_id' => $service->id,
                'category_id' => $request->service_sub_category_id,
            ]);
        }

        if ($request->hasFile('service_cover_file')) {
            $file = $request->file('service_cover_file');

            $file_name = $file->getClientOriginalName();
            $file_path = $file->store('uploads', 'public');
            $cover = new ServiceCover([
                'service_id' => $service->id,
                'file_name' => $file_name,
                'file_path' => $file_path,
            ]);
            $cover->save();
        }

        if ($request->hasFile('service_files')) {
            $files = $request->file('service_files');
            foreach ($files as $file) {
                if ($file) {
                    $file_name = $file->getClientOriginalName();
                    $file_path = $file->store('uploads', 'public');
                    $jobImage = new ServiceImage([
                        'service_id' => $service->id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                    ]);
                    $jobImage->save();
                }
            }
        }

        return response()->json($service, 201);
    }

    public function show($id)
    {
        $service = Service::where('id', $id)
            ->with('cover', 'images', 'user.detailInfo')
            ->first();

        return Inertia::render('Service/Detail', [
            'service' => ServiceResource::make($service),
        ]);
    }

    public function order(Int $serviceId, ServiceOrderRequest $request): JsonResponse
    {
        $service = Service::find($serviceId);
        if (!$service) {
            return response()->json(['error' => 'service not found.'], 404);
        }

        $validatedDate = $request->validated();

        if (!isset($validatedDate['message'])) {
            return response()->json(['error' => 'message not found.'], 404);
        }

        $order = ServiceOrder::firstOrCreate([
             'message' => $validatedDate['message'],
             'user_id' => Auth::id(),
             'service_id' => $service->id,
        ]);

        return response()->json([
            'message' => 'order created successfully.',
            'data' => ServiceOrderResource::make($order),
        ], 201);
    }
}
