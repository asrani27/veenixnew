<?php

namespace App\Providers;

use App\Models\ApiSetting;
use Illuminate\Support\ServiceProvider;

class ApiSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Override config values with database settings
        try {
            $settings = ApiSetting::getAllSettings();
            
            if (isset($settings['TMDB_API_KEY'])) {
                config(['services.tmdb.api_key' => $settings['TMDB_API_KEY']]);
            }
            
            if (isset($settings['WAS_ACCESS_KEY_ID'])) {
                config(['services.wasabi.access_key_id' => $settings['WAS_ACCESS_KEY_ID']]);
            }
            
            if (isset($settings['WAS_SECRET_ACCESS_KEY'])) {
                config(['services.wasabi.secret_access_key' => $settings['WAS_SECRET_ACCESS_KEY']]);
            }
            
            if (isset($settings['WAS_DEFAULT_REGION'])) {
                config(['services.wasabi.default_region' => $settings['WAS_DEFAULT_REGION']]);
            }
            
            if (isset($settings['WAS_BUCKET'])) {
                config(['services.wasabi.bucket' => $settings['WAS_BUCKET']]);
            }
            
            if (isset($settings['WAS_URL'])) {
                config(['services.wasabi.url' => $settings['WAS_URL']]);
            }
            
            if (isset($settings['TURNSTILE_SITE_KEY'])) {
                config(['services.turnstile.site_key' => $settings['TURNSTILE_SITE_KEY']]);
            }
            
            if (isset($settings['TURNSTILE_SECRET_KEY'])) {
                config(['services.turnstile.secret_key' => $settings['TURNSTILE_SECRET_KEY']]);
            }
        } catch (\Exception $e) {
            // If database is not available or table doesn't exist, skip
            // This prevents errors during installation or migration
        }
    }
}
