<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferStoreRequest;
use App\Models\Job;
use App\Models\JobOffer;
use App\Models\JobCategory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OfferController extends Controller
{

    public function index(): Response
    {
        $offers = JobOffer::where('executor_id', Auth::id())
            ->with('job', 'job.user', 'job.user.detailInfo')
            ->get();

        return Inertia::render('Offer/Index', [
            'offers' => $offers,
        ]);
    }

    public function create(string $id): Response
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

        return Inertia::render('Offer/Create', [
            'job' => $job,
        ]);
    }

    public function store(OfferStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Создание нового предложения
        $offer = JobOffer::create(array_merge($validatedData, [
            'offer_status' => 'Ожидается',
            'executor_id' => Auth::id(),
        ]));

        //status:
        //Ожидается
        //Принят
        //Отклонено

        return Redirect::route('offer.index')->with('success', 'Ваше предложение отправлено!');
    }

    public function offerNotifications(): Response
    {
        $offers = JobOffer::where('customer_id', Auth::id())
            ->where('offer_status', '!=', 'Принят')
            ->where('offer_status', '!=', 'Отклонено')
            ->with('job', 'user', 'user.detailInfo')
            ->get();

        return Inertia::render('Offer/offerNotifications', [
            'offers' => $offers,
        ]);
    }

    public function offerAccept(Request $request)
    {
        $offer = JobOffer::find($request->offer_id);
        if (!$offer) {
            return response()->json(['message' => 'Offer not found.'], 404);
        }

        $offer->update([
            'offer_status' => 'Принят',
        ]);

        return response()->json(['message' => 'Offer accepted succesfully.'], 200);
    }

    public function offerDecline(Request $request)
    {
        $offer = JobOffer::find($request->offer_id);
        if (!$offer) {
            return response()->json(['message' => 'Offer not found.'], 404);
        }

        $offer->update([
            'offer_status' => 'Отклонено',
        ]);

        return response()->json(['message' => 'Offer declined succesfully.'], 200);
    }
    
}
