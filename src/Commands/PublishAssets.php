<?php

namespace LPuddu\LaravelTranslationsDashboard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations-dashboard:publish-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish compiled assets folder';

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
        $this->call('vendor:publish', [
            "--provider" => "LPuddu\LaravelTranslationsDashboard\LaravelTranslationsDashboardServiceProvider",
            "--tag" => "laravel-translations-dashboard.assets"
        ]);
    }
}