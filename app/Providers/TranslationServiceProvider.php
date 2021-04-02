<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    protected $langPath;

    public function __construct()
    {
        $this->langPath = resource_path('lang/en');
    }

    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Cache::rememberForever('translations', function () {
            return collect(File::allFiles($this->langPath))->flatMap(function ($file) {
                return [
                    ($translation = $file->getBasename('.php')) => trans($translation),
                ];
            })->toJson();
        });
    }
}
