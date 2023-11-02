<?php

namespace App\Http\Controllers;

use App\Page;
use App\Product;
use App\Publication;
use App\Review;
use App\University;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $captchaKey = $request->input('captcha_key', '');
        $data = $request->validate([
            // 'captcha_key' => 'required',
            // 'captcha' => 'required|captcha_api:' . $captchaKey . ',flat',
            'reviewable_id' => 'required',
            'reviewable_type' => 'required',
            'name' => 'required|max:191',
            'phone_number' => 'required',
            'body' => 'required|max:50000',
            'rating' => 'required',
        ]);

        // unset($data['captcha']);
        // unset($data['captcha_key']);

        $data['user_id'] = auth()->user()->id ?? null;
        $data['status'] = Review::STATUS_PENDING;

        $data['ip_address'] = request()->ip();
        $data['user_agent'] = request()->header('User-Agent');

        $data['rating'] = (int)$data['rating'];
        if ($data['rating'] > 5) {
            $data['rating'] = 5;
        } elseif ($data['rating'] < 1) {
            $data['rating'] = 1;
        }

        $modelName = $this->getModelName($data['reviewable_type']);
        if (!$modelName) {
            abort(400);
        }
        $model = $modelName::findOrFail($data['reviewable_id']);

        unset($data['reviewable_type']);
        unset($data['reviewable_id']);

        $model->reviews()->create($data);

        return response()->json(['message' => __('main.review.success_created')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function getModelName($name)
    {
        $models = [
            'page' => Page::class,
            'product' => Product::class,
            'publication' => Publication::class,
            'university' => University::class,
        ];
        return $models[$name] ?? null;
    }
}
