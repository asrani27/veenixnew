<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ApiSettingsController extends Controller
{
    /**
     * Display the API settings page.
     */
    public function index()
    {
        return view('admin.api-settings', [
            'tmdb_api_key' => config('services.tmdb.api_key'),
            'was_access_key_id' => config('services.wasabi.access_key_id'),
            'was_secret_access_key' => config('services.wasabi.secret_access_key'),
            'was_default_region' => config('services.wasabi.default_region'),
            'was_bucket' => config('services.wasabi.bucket'),
            'was_url' => config('services.wasabi.url'),
            'turnstile_site_key' => config('services.turnstile.site_key'),
            'turnstile_secret_key' => config('services.turnstile.secret_key'),
        ]);
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

        $this->updateEnvFile([
            'TMDB_API_KEY' => $request->tmdb_api_key,
            'WAS_ACCESS_KEY_ID' => $request->was_access_key_id,
            'WAS_SECRET_ACCESS_KEY' => $request->was_secret_access_key,
            'WAS_DEFAULT_REGION' => $request->was_default_region,
            'WAS_BUCKET' => $request->was_bucket,
            'WAS_URL' => $request->was_url,
            'TURNSTILE_SITE_KEY' => $request->turnstile_site_key,
            'TURNSTILE_SECRET_KEY' => $request->turnstile_secret_key,
        ]);

        return redirect()->route('admin.api-settings')->with('success', 'API keys updated successfully!');
    }

    /**
     * Update the .env file with new values.
     */
    private function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            // Escape any special characters in the value
            $value = addslashes($value);
            
            // Check if the key exists in the file
            if (preg_match("/^{$key}=/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                // Add new key if it doesn't exist
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);
    }
}
