<?php

namespace App\Http\Controllers\Api\V2;

use App\Publication;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PublicationResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|in:' . implode(',', array_keys(Publication::types())),
        ]);

        $quantity = (int)$request->input('quantity', 30);
        if ($quantity > 120 || $quantity < 1) {
            $quantity = 30;
        }

        $type = !empty($data['type']) ? $data['type'] : Publication::TYPE_ARTICLE;
        $locale = app()->getLocale();
        $publications = Publication::active()->where('type', $type)->withTranslation($locale)->latest()->paginate($quantity)->appends($request->all());
        return PublicationResource::collection($publications);
    }

    public function show(Request $request, Publication $publication)
    {
        // $locale = app()->getLocale();
        $publication->load('translations');
        return new PublicationResource($publication);
    }
}
