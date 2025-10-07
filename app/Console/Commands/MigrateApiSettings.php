<?php

namespace App\Console\Commands;

use App\Models\ApiSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateApiSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-settings:migrate {--force : Overwrite existing database settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate API settings from .env file to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating API settings from .env to database...');

        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('.env file not found!');
            return Command::FAILURE;
        }

        $envContent = File::get($envPath);
        $settings = [
            'TMDB_API_KEY' => 'TMDB API Key',
            'WAS_ACCESS_KEY_ID' => 'Wasabi Access Key ID',
            'WAS_SECRET_ACCESS_KEY' => 'Wasabi Secret Access Key',
            'WAS_DEFAULT_REGION' => 'Wasabi Default Region',
            'WAS_BUCKET' => 'Wasabi Bucket Name',
            'WAS_URL' => 'Wasabi S3 URL',
            'TURNSTILE_SITE_KEY' => 'Cloudflare Turnstile Site Key',
            'TURNSTILE_SECRET_KEY' => 'Cloudflare Turnstile Secret Key',
        ];

        $migrated = 0;
        $skipped = 0;

        foreach ($settings as $key => $description) {
            // Extract value from .env file
            if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
                $value = trim($matches[1]);
                
                if (!empty($value)) {
                    // Check if setting already exists in database
                    $existing = ApiSetting::where('key', $key)->first();
                    
                    if ($existing && !$this->option('force')) {
                        $this->line("✓ Skipping {$key} - already exists in database (use --force to overwrite)");
                        $skipped++;
                    } else {
                        ApiSetting::setValue($key, $value, $description);
                        $this->info("✓ Migrated {$key}");
                        $migrated++;
                    }
                } else {
                    $this->line("- Skipping {$key} - empty value in .env");
                    $skipped++;
                }
            } else {
                $this->line("- Skipping {$key} - not found in .env");
                $skipped++;
            }
        }

        $this->info("\nMigration complete!");
        $this->info("Migrated: {$migrated} settings");
        $this->info("Skipped: {$skipped} settings");

        if ($migrated > 0) {
            $this->info("\nNote: After migration, you can now update API settings through the admin interface without file permission issues.");
        }

        return Command::SUCCESS;
    }
}
