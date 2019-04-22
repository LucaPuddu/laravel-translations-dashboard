<?php

namespace LPuddu\LaravelTranslationsDashboard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Init extends Command
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
    protected $description = 'Publish all required files';

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
        $this->call('translations-dashboard:publish-assets');
        $this->call('translations-dashboard:publish-spatie');
    }
}