@extends('layouts.app')

@section('seo_title', $poll->question)

@section('content')

<section class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="order-lg-2 col-lg-9 col-xl-9 main-block">

                <section class="content-block">
                    <x-top-search></x-top-search>
                </section>

                @include('partials.breadcrumbs')

                <h1 class="main-header mt-3">{{ $poll->question }}</h1>

                <form action="{{ route('polls.vote', $poll->id) }}" method="post">

                    @csrf

                    @php
                        $votesCount = $pollAnswers->sum('votes');
                    @endphp

                    @if(!$voted)
                        @foreach($pollAnswers as $pollAnswer)
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="poll_answer_{{ $pollAnswer->id }}" name="poll_answer" value="{{ $pollAnswer->id }}" class="custom-control-input" required>
                                    <label class="custom-control-label" for="poll_answer_{{ $pollAnswer->id }}">{{ $pollAnswer->answer }}</label>
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">{{ __('main.to_vote') }}</button>
                        </div>
                    @else
                        <div class="row mb-2">
                            <div class="col-lg-8 col-xl-6">
                                @foreach($pollAnswers as $pollAnswer)
                                    @php
                                        $percent = ($votesCount > 0) ? round($pollAnswer->votes / $votesCount * 100, 1) : 0;
                                    @endphp
                                    <div class="mb-3">
                                        <label class="d-block mb-1">{{ $pollAnswer->answer }}</label>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">{{ $percent }}%</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <strong>{{ __('main.all_votes2') }}: {{ $votesCount }}</strong>
                        </div>
                    @endif
                </form>

                <div class="pb-5"></div>

            </div>
            <div class="order-lg-1 col-lg-3 col-xl-3 side-block">

                @include('partials.sidebar')

            </div>
        </div>




    </div>
</section>

<x-principles></x-principles>


@endsection
