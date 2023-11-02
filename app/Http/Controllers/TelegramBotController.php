<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Exception\TelegramLogException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Monolog\Logger;

class TelegramBotController extends Controller
{
    protected $botname;
    protected $token;
    protected $hook_url;

    public function __construct()
    {
        $this->botname = config('services.telegram.bot_name');
        $this->token = config('services.telegram.bot_token');
        $this->hook_url = config('services.telegram.hook_url');
    }

    public function index()
    {
        // Define all IDs of admin users in this array (leave as empty array if not used)
        $admin_users = [
            11120017,
        ];

        // Define all paths for your custom commands in this array (leave as empty array if not used)
        $commands_paths = [
            app_path('TelegramBot/Commands'),
        ];

        // Enter your MySQL database credentials
        $mysql_credentials = [
            'host'     => env('DB_HOST_2'),
            'user'     => env('DB_USERNAME_2'),
            'password' => env('DB_PASSWORD_2'),
            'database' => env('DB_DATABASE_2'),
        ];

        try {
            // Create Telegram API object
            $telegram = new Telegram($this->token, $this->botname);
            // Add commands paths containing your custom commands
            $telegram->addCommandsPaths($commands_paths);
            // Enable admin users
            $telegram->enableAdmins($admin_users);
            // Enable MySQL
            $telegram->enableMySql($mysql_credentials);
            // Logging (Error, Debug and Raw Updates)
//            $logger = new Logger($this->botname);
//            TelegramLog::initialize($logger);
            // Set custom Upload and Download paths
            //$telegram->setDownloadPath(__DIR__ . '/Download');
            //$telegram->setUploadPath(__DIR__ . '/Upload');
            // Here you can set some command specific parameters
            // e.g. Google geocode/timezone api key for /date command
            //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);
            // Requests Limiter (tries to prevent reaching Telegram API limits)
            $telegram->enableLimiter();
            // Handle telegram webhook request
            $telegram->handle();
        } catch (TelegramLogException $e) {
            // Silence is golden!
            // Uncomment this to catch log initialisation errors
//            echo $e;
        } catch (TelegramException $e) {
            // Silence is golden!
//            echo $e;
            // Log telegram errors
            TelegramLog::error($e);
        }
    }

    public function sethook()
    {
        try {
            // Create Telegram API object
            $telegram = new Telegram($this->token, $this->botname);

            // Set webhook
            $result = $telegram->setWebhook($this->hook_url);
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (TelegramException $e) {
            // log telegram errors
            echo $e->getMessage();
        }
    }

    public function deletehook()
    {
        try {
            // Create Telegram API object
            $telegram = new Telegram($this->token, $this->botname);

            // Set webhook
            $result = $telegram->deleteWebhook();
            if ($result->isOk()) {
                echo $result->getDescription();
            }
        } catch (TelegramException $e) {
            // log telegram errors
            echo $e->getMessage();
        }
    }
}
