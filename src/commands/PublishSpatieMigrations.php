<?php

namespace LPuddu\LaravelTranslationsDashboard;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishSpatieMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations-dashboard:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the required migrations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"');
    }
}