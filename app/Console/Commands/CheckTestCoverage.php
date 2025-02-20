<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckTestCoverage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:test-coverage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check test coverage for critical folders like Requests, Controllers, Actions, and Services';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $directories = [
            'app/Http/Requests',
            'app/Http/Controllers',
            'app/Actions',
            'app/Services'
        ];

        $missingTests = 0;

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*.php');

                foreach ($files as $file) {
                    $filename = basename($file, '.php');
                    $testFiles = glob(base_path('tests/**/*' . $filename . 'Test.php'));

                    if (empty($testFiles)) {
                        $this->error("❌ Aucun test trouvé pour $filename ($file)");
                        $missingTests++;
                    } else {
                        $this->info("✅ $filename est testé");
                    }
                }
            }
        }

        if ($missingTests > 0) {
            $this->error('❌ Des fichiers ne sont pas testés. Veuillez ajouter des tests.');
            exit(1);
        } else {
            $this->info('✅ Tous les fichiers dans Requests, Controllers, Actions et Services sont testés.');
        }
    }
}
