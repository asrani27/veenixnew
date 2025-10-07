<?php

namespace App\Http\Controllers;

use App\Models\ApiSetting;
use Illuminate\Http\Request;

class ApiSettingsController extends Controller
{
    /**
     * Display the API settings page.
     */
    public function index()
    {
        // Get settings from database first, fallback to config
        $settings = [
            'tmdb_api_key' => ApiSetting::getValue('TMDB_API_KEY') ?? config('services.tmdb.api_key'),
            'was_access_key_id' => ApiSetting::getValue('WAS_ACCESS_KEY_ID') ?? config('services.wasabi.access_key_id'),
            'was_secret_access_key' => ApiSetting::getValue('WAS_SECRET_ACCESS_KEY') ?? config('services.wasabi.secret_access_key'),
            'was_default_region' => ApiSetting::getValue('WAS_DEFAULT_REGION') ?? config('services.wasabi.default_region'),
            'was_bucket' => ApiSetting::getValue('WAS_BUCKET') ?? config('services.wasabi.bucket'),
            'was_url' => ApiSetting::getValue('WAS_URL') ?? config('services.wasabi.url'),
            'turnstile_site_key' => ApiSetting::getValue('TURNSTILE_SITE_KEY') ?? config('services.turnstile.site_key'),
            'turnstile_secret_key' => ApiSetting::getValue('TURNSTILE_SECRET_KEY') ?? config('services.turnstile.secret_key'),
        ];

        return view('admin.api-settings', $settings);
    }

    /**
     * Update the API keys.
     */
    public function update(Request $request)
    {
        $request->validate([
            'tmdb_api_key' => 'required|string|min:10',
            'was_access_key_id' => 'required|string|min:10',
            'was_secret_access_key' => 'required|string|min:10',
            'was_default_region' => 'required|string',
            'was_bucket' => 'required|string',
            'was_url' => 'required|string|url',
            'turnstile_site_key' => 'required|string|min:10',
            'turnstile_secret_key' => 'required|string|min:10',
        ]);

        // Update settings in database
        ApiSetting::setValue('TMDB_API_KEY', $request->tmdb_api_key, 'TMDB API Key');
        ApiSetting::setValue('WAS_ACCESS_KEY_ID', $request->was_access_key_id, 'Wasabi Access Key ID');
        ApiSetting::setValue('WAS_SECRET_ACCESS_KEY', $request->was_secret_access_key, 'Wasabi Secret Access Key');
        ApiSetting::setValue('WAS_DEFAULT_REGION', $request->was_default_region, 'Wasabi Default Region');
        ApiSetting::setValue('WAS_BUCKET', $request->was_bucket, 'Wasabi Bucket Name');
        ApiSetting::setValue('WAS_URL', $request->was_url, 'Wasabi S3 URL');
        ApiSetting::setValue('TURNSTILE_SITE_KEY', $request->turnstile_site_key, 'Cloudflare Turnstile Site Key');
        ApiSetting::setValue('TURNSTILE_SECRET_KEY', $request->turnstile_secret_key, 'Cloudflare Turnstile Secret Key');

        return redirect()->route('admin.api-settings')->with('success', 'API keys updated successfully!');
    }
}
