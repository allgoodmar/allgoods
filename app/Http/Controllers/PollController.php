<?php

namespace App\Http\Controllers;

use App\Helpers\Breadcrumbs;
use App\Helpers\LinkItem;
use App\Poll;
use App\PollAnswer;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Page;
use Illuminate\Support\Facades\Cookie;

class PollController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();

        $breadcrumbs = new Breadcrumbs();
        $page = Page::findOrFail(18);
        $page = Helper::translation($page);
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url, LinkItem::STATUS_INACTIVE));

        $polls = Poll::active()->latest()->take(10)->withTranslation($locale)->get()->translate();

        return view('poll.index', compact('breadcrumbs', 'page', 'polls'));
    }

    public function show(Poll $poll)
    {
        Helper::checkModelActive($poll);
        $breadcrumbs = new Breadcrumbs();

        $page = Page::findOrFail(18);
        $page = Helper::translation($page);
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

        $voted = false;
        $votedPoll = Cookie::get('polls_' . $poll->id);
        if (!empty($votedPoll) && $votedPoll == 'voted') {
            $voted = true;
        }

        $pollAnswers = $poll->pollAnswers;
        if ($pollAnswers) {
            $pollAnswers = $pollAnswers->translate();
        }

        $poll = Helper::translation($poll);


        return view('poll.show', compact('breadcrumbs', 'poll', 'pollAnswers', 'voted'));
    }

    public function vote(Request $request, Poll $poll)
    {
        $request->validate([
            'poll_answer' => 'required',
        ]);

        $pollAnswerID = $request->poll_answer;
        $pollAnswer = PollAnswer::findOrFail($pollAnswerID);
        $pollAnswer->increment('votes');

        Cookie::queue(Cookie::make('polls_' . $pollAnswer->poll_id, 'voted', 1440 * 30));

        return redirect()->route('polls.show', $poll->id);
    }
}
