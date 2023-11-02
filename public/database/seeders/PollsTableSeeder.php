<?php

namespace Database\Seeders;

use App\Poll;
use App\PollAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PollsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('polls')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $poll = Poll::create([
            'question' => 'Какие материалы на сайте нужно больше освещать?',
            'status' => 1,
        ]);

        $poll->pollAnswers()->save(PollAnswer::create([
            'answer' => 'Информация по отрасли',
        ]));
        $poll->pollAnswers()->save(PollAnswer::create([
            'answer' => 'Информация по услугам',
        ]));
        $poll->pollAnswers()->save(PollAnswer::create([
            'answer' => 'Международные новости',
        ]));
        $poll->pollAnswers()->save(PollAnswer::create([
            'answer' => 'Новости по республике',
        ]));

    }
}
