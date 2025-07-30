<?php

namespace App\Console\Commands;

use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class Reset extends Command
{
    protected $signature = 'app:reset';
    protected $description = 'Fully reset database, seed core data, run Shield seeds and assign super admin to user #1';

    public function handle(): int
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('Refusing to run in production without --force.');
            return self::FAILURE;
        }


        // 1. fresh migrate
        $this->info('Running migrate:fresh ...');
        Artisan::call('migrate:fresh');
        $this->line(Artisan::output());

        // 2. default seeders
        $this->info('Seeding default database seeders...');
        Artisan::call('db:seed');
        $this->line(Artisan::output());

        // 3. Shield specific seeder
        $this->info('Seeding ShieldSeeder...');
        Artisan::call('db:seed', [
            '--class' => 'ShieldSeeder',
        ]);
        $this->line(Artisan::output());

        // 4. Assign super_admin role to user #1
        $this->info('Assigning super_admin role to user #1 (if exists)...');

        /** Adjust this depending on how Shield assigns roles/permissions:
         *  - If using Spatie roles: $user->assignRole('super_admin');
         *  - If using Shield's facade: FilamentShield::superAdmin($user);
         */

        $user = User::find(1);
        if ($user) {
            try {
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('super_admin');
                    $this->info('Role super_admin assigned via Spatie.');
                } elseif (class_exists(FilamentShield::class)) {
                    FilamentShield::superAdmin($user);
                    $this->info('Marked user #1 as Filament Shield super admin.');
                } else {
                    $this->warn('No role assignment method found. Implement manually.');
                }
            } catch (Throwable $e) {
                $this->error('Failed assigning role: ' . $e->getMessage());
            }
        } else {
            $this->warn('User #1 not found. Skipped role assignment.');
        }

        // 5. Generate all Shield permissions/resources
        $this->info('Generating Shield permissions (--all)...');
        $this->line(Artisan::output());

        $this->info('Reset complete.');
        return self::SUCCESS;
    }
}
