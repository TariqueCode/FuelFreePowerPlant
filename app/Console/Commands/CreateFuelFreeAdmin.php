<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateFuelFreeAdmin extends Command
{
    protected $signature = 'fuel-free:admin
        {--name= : Administrator name}
        {--email= : Administrator email address}';

    protected $description = 'Create or promote a FuelFree PowerPlant Super Admin account.';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Administrator name');
        $email = $this->option('email') ?: $this->ask('Administrator email');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Please provide a valid email address.');
            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            if (! $this->confirm('An account already exists. Promote it to Super Admin?', true)) {
                return self::SUCCESS;
            }
            $user->name = $name;
        } else {
            $password = $this->secret('Password (minimum 12 characters)');
            $confirmation = $this->secret('Confirm password');

            if (strlen($password) < 12 || ! hash_equals($password, $confirmation)) {
                $this->error('Password must be at least 12 characters and both entries must match.');
                return self::FAILURE;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
        }

        $role = Role::where('slug', 'super-admin')->first();

        if (! $role) {
            $this->error('The super-admin role does not exist. Run RolePermissionSeeder first.');
            return self::FAILURE;
        }

        $user->roles()->syncWithoutDetaching([$role->id]);
        $this->info("Super Admin ready: {$user->email}");

        return self::SUCCESS;
    }
}
