<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileService
{

    public function saveFiles(Request $request, Job $job): JsonResponse
    {
        $files = $request->file('files');
        foreach ($files as $file) {
            if ($file) {
                $file_name = $file->getClientOriginalName();
                $file_path = $file->store('uploads', 'public');
                $jobImage = new JobImage([
                    'file_name' => $file_name,
                    'file_path' => $file_path,
                    'job_id' => $job->id,
                ]);
                $jobImage->save();
            } else {
                return response()->json(['error' => 'Invalid file provided.'], 422);
            }
        }

        return response()->json(['message' => 'files saved successfully.'], 201);
    }

    public function deleteFiles(Request $request): JsonResponse
    {
        $imageIdsToDelete = $request->input('delete_files');
        $imagesToDelete = JobImage::whereIn('id', $imageIdsToDelete)->get();
        foreach ($imagesToDelete as $image) {
            Storage::disk('public')->delete($image->file_path);
            $image->delete();
        }

        return response()->json(['message' => 'files deleted successfully.'], 201);
    }

}