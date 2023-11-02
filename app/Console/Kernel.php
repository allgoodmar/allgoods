<?php

namespace App\Console;

use App\Console\Commands\AmoTokenUpdate;
use App\Console\Commands\AtmosTokenUpdate;
use App\Console\Commands\BillzAdrasProducts;
use App\Console\Commands\BillzDivaProducts;
use App\Console\Commands\BillzElisiumProducts;
use App\Console\Commands\BillzKocosProducts;
use App\Console\Commands\BillzMylifestyleProducts;
use App\Console\Commands\BillzOmiodiobrandProducts;
use App\Console\Commands\BillzProducts;
use App\Console\Commands\BillzRayyonProducts;
use App\Console\Commands\BillzToparProducts;
use App\Console\Commands\BillzWalhalaProducts;
use App\Console\Commands\ElmakonProducts;
use App\Console\Commands\ProcessProducts;
use App\Console\Commands\SitemapGenerate;
use App\Console\Commands\SynchroPhotos;
use App\Console\Commands\SynchroSmartup;
use App\Console\Commands\TrendyolProducts;
use App\Console\Commands\UpdateStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command(SynchroPhotos::class)->everyFifteenMinutes();
        $schedule->command(ProcessProducts::class, ['price'])->everyThreeHours();
        $schedule->command(ProcessProducts::class, ['rating'])->dailyAt('02:50');
        $schedule->command(ElmakonProducts::class)->twiceDaily();
        $schedule->command(BillzProducts::class)->dailyAt('03:10');
        $schedule->command(BillzAdrasProducts::class)->dailyAt('03:20');
        $schedule->command(BillzKocosProducts::class)->dailyAt('03:30');
        $schedule->command(BillzMylifestyleProducts::class)->dailyAt('03:40');
        $schedule->command(BillzOmiodiobrandProducts::class)->dailyAt('03:50');
        $schedule->command(BillzToparProducts::class)->dailyAt('04:00');
        $schedule->command(BillzDivaProducts::class)->dailyAt('04:10');
        $schedule->command(BillzRayyonProducts::class)->dailyAt('04:20');
        $schedule->command(BillzElisiumProducts::class)->dailyAt('04:30');
        $schedule->command(BillzWalhalaProducts::class)->dailyAt('04:40');
        $schedule->command(AmoTokenUpdate::class)->twiceDaily(1, 13);
        // $schedule->command(TrendyolProducts::class)->everyMinute();
        $schedule->command(AtmosTokenUpdate::class)->everyThirtyMinutes();
        $schedule->command(SitemapGenerate::class)->cron('0 0 */3 * *');
        $schedule->command(UpdateStatus::class)->everyTwoHours();
        // $schedule->command(ProcessProducts::class)->everyMinute();
        // $schedule->command(SynchroSmartup::class)->everyTwoHours();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
