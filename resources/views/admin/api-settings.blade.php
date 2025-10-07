@extends('layouts.app')

@section('title', 'API Settings')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">API Settings</h1>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    @if (session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('admin.api-settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('POST')

                        <!-- TMDB API Key -->
                        <div>
                            <label for="tmdb_api_key" class="block text-sm font-medium text-gray-700">
                                TMDB API Key
                            </label>
                            <div class="mt-1">
                                <input type="text" name="tmdb_api_key" id="tmdb_api_key"
                                    value="{{ config('services.tmdb.api_key') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="Enter your TMDB API key" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Your API key for The Movie Database (TMDB). Get it from <a
                                    href="https://www.themoviedb.org/settings/api" target="_blank"
                                    class="text-indigo-600 hover:text-indigo-500">TMDB Settings</a>.
                            </p>
                        </div>

                        <!-- Wasabi Access Key ID -->
                        <div>
                            <label for="was_access_key_id" class="block text-sm font-medium text-gray-700">
                                Wasabi Access Key ID
                            </label>
                            <div class="mt-1">
                                <input type="text" name="was_access_key_id" id="was_access_key_id"
                                    value="{{ config('services.wasabi.access_key_id') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="Enter your Wasabi Access Key ID" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Your Wasabi storage access key ID for file uploads.
                            </p>
                        </div>

                        <!-- Wasabi Secret Access Key -->
                        <div>
                            <label for="was_secret_access_key" class="block text-sm font-medium text-gray-700">
                                Wasabi Secret Access Key
                            </label>
                            <div class="mt-1 relative">
                                <input type="password" name="was_secret_access_key" id="was_secret_access_key"
                                    value="{{ config('services.wasabi.secret_access_key') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 pr-10 border"
                                    placeholder="Enter your Wasabi Secret Access Key" required>
                                <button type="button" onclick="togglePassword('was_secret_access_key')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg id="was_secret_access_key_toggle" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Your Wasabi storage secret access key. This field is masked for security.
                            </p>
                        </div>

                        <!-- Wasabi Default Region -->
                        <div>
                            <label for="was_default_region" class="block text-sm font-medium text-gray-700">
                                Wasabi Default Region
                            </label>
                            <div class="mt-1">
                                <input type="text" name="was_default_region" id="was_default_region"
                                    value="{{ config('services.wasabi.default_region') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="e.g., ap-southeast-1" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                The default region for your Wasabi storage bucket (e.g., ap-southeast-1, us-east-1, eu-central-1).
                            </p>
                        </div>

                        <!-- Wasabi Bucket -->
                        <div>
                            <label for="was_bucket" class="block text-sm font-medium text-gray-700">
                                Wasabi Bucket Name
                            </label>
                            <div class="mt-1">
                                <input type="text" name="was_bucket" id="was_bucket"
                                    value="{{ config('services.wasabi.bucket') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="Enter your Wasabi bucket name" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                The name of your Wasabi storage bucket where files will be stored.
                            </p>
                        </div>

                        <!-- Wasabi URL -->
                        <div>
                            <label for="was_url" class="block text-sm font-medium text-gray-700">
                                Wasabi URL
                            </label>
                            <div class="mt-1">
                                <input type="url" name="was_url" id="was_url"
                                    value="{{ config('services.wasabi.url') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="e.g., https://s3.ap-southeast-1.wasabisys.com" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                The endpoint URL for your Wasabi storage region (e.g., https://s3.ap-southeast-1.wasabisys.com).
                            </p>
                        </div>

                        <!-- Cloudflare Turnstile Site Key -->
                        <div>
                            <label for="turnstile_site_key" class="block text-sm font-medium text-gray-700">
                                Cloudflare Turnstile Site Key
                            </label>
                            <div class="mt-1">
                                <input type="text" name="turnstile_site_key" id="turnstile_site_key"
                                    value="{{ config('services.turnstile.site_key') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                    placeholder="Enter your Cloudflare Turnstile Site Key" required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Your Cloudflare Turnstile site key for the login form captcha. Get it from your <a
                                    href="https://dash.cloudflare.com/" target="_blank"
                                    class="text-indigo-600 hover:text-indigo-500">Cloudflare Dashboard</a> under Turnstile.
                            </p>
                        </div>

                        <!-- Cloudflare Turnstile Secret Key -->
                        <div>
                            <label for="turnstile_secret_key" class="block text-sm font-medium text-gray-700">
                                Cloudflare Turnstile Secret Key
                            </label>
                            <div class="mt-1 relative">
                                <input type="password" name="turnstile_secret_key" id="turnstile_secret_key"
                                    value="{{ config('services.turnstile.secret_key') }}"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 pr-10 border"
                                    placeholder="Enter your Cloudflare Turnstile Secret Key" required>
                                <button type="button" onclick="togglePassword('turnstile_secret_key')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg id="turnstile_secret_key_toggle" class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Your Cloudflare Turnstile secret key for server-side verification. This field is masked for security.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save API Keys
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-blue-900">
                        API Information
                    </h3>
                    <div class="mt-2 max-w-xl text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>TMDB API:</strong> Used for fetching movie and TV show metadata, posters, and
                                other information from The Movie Database.</li>
                            <li><strong>Wasabi Storage:</strong> Used for storing and serving media files like posters,
                                videos, and other uploaded content.</li>
                            <li><strong>Cloudflare Turnstile:</strong> Used for security verification on the login form to prevent automated bots and spam.</li>
                            <li>Changes to API keys will take effect immediately after saving.</li>
                            <li>Make sure your API keys are valid and have the necessary permissions.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + '_toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
        `;
    } else {
        passwordInput.type = 'password';
        toggleIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        `;
    }
}
</script>

@endsection
