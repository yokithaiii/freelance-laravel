<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $offer = JobOffer::where('customer_id', Auth::id())
            ->with('user.detailInfo')
            ->with('job')
            ->get();

        return response()->json($offer, 200);
    }
}
