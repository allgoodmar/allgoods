<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Product;
use App\Review;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $quantity = (int)$request->input('quantity', 30);
        if ($quantity > 120 || $quantity < 1) {
            $quantity = 30;
        }

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $reviews = Review::active()->where('reviewable_type', Product::class)->where('reviewable_id', $data['product_id'])->latest()->paginate($quantity)->appends($request->all());
        return ReviewResource::collection($reviews);
    }

    public function show(Request $request, Review $review)
    {
        return new ReviewResource($review);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|max:191',
            'email' => 'nullable|email|max:191',
            'body' => 'required|max:50000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $data['reviewable_type'] = Product::class;
        $data['reviewable_id'] = $data['product_id'];
        unset($data['product_id']);

        $data['user_id'] = auth()->user()->id ?? null;
        $data['status'] = Review::STATUS_PENDING;

        $data['ip_address'] = request()->ip();
        $data['user_agent'] = request()->header('User-Agent');

        $review = Review::create($data);

        // return new ReviewResource($review);

        return response()->json(['message' => __('main.review.success_created')], 201);
    }

    public function report(Request $request, Review $review)
    {
        $data = $request->validate([
            'message' => 'max:5000',
        ]);

        // send notification to telegram admin
        $chat_id = config('services.telegram.chat_id');
        $telegramService = new TelegramService();
        $telegramMessage = view('telegram.admin.review_report', ['review' => $review, 'message' => $data['message']])->render();
        $telegramService->sendMessage($chat_id, $telegramMessage, 'HTML');

        return response()->json(['message' => __('main.review.report_sent')], 200);
    }
}
