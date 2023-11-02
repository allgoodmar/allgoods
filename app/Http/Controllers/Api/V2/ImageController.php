<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $createData = [
            'path' => Storage::disk('public')->putFile('images/' . date('FY'), $data['image']),
            'original_name' => $data['image']->getClientOriginalName(),
            'mime_type' => $data['image']->getClientMimeType(),
            'size' => $data['image']->getSize(),
            'user_id' => $user->id,
        ];

        $image = Image::create($createData);

        return new ImageResource($image);
    }

    public function show(Image $image)
    {
        if ($image->user_id != auth()->user()->id) {
            abort(403);
        }
        return new ImageResource($image);
    }
}

