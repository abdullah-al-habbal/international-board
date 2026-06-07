<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Trainer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixTrainerCredentials extends Command
{
    protected $signature = 'trainers:fix-credentials';

    protected $description = 'Generate missing emails and set default passwords for trainers.';

    public function handle(): int
    {
        $this->info('Fixing trainer credentials...');

        Trainer::whereNull('email')
            ->orWhereNull('password')
            ->chunk(100, function ($trainers): void {
                foreach ($trainers as $trainer) {
                    $needsSave = false;

                    if (empty($trainer->email)) {
                        $email = $this->generateEmail($trainer->name);
                        $trainer->email = $email;
                        $needsSave = true;
                        $this->line("Set email for trainer #{$trainer->id} ({$trainer->name}) \u{2192} {$email}");
                    }

                    if (empty($trainer->password)) {
                        $trainer->password = 'password123';
                        $needsSave = true;
                        $this->line("Set default password for trainer #{$trainer->id} ({$trainer->name})");
                    }

                    if ($needsSave) {
                        $trainer->save();
                    }
                }
            });

        $this->info('Done.');

        return self::SUCCESS;
    }

    private function generateEmail(string $name): string
    {
        $slug = Str::slug($name, '.');
        $email = $slug . '@trainers.com';
        $counter = 1;

        while (Trainer::where('email', $email)->exists()) {
            $email = $slug . $counter . '@trainers.com';
            $counter++;
        }

        return $email;
    }
}
