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
        {--email= : Administrator email address}
        {--reset-password : Reset the password of an existing administrator account}';

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
            if ($this->option('reset-password')) {
                $password = $this->secret('New password (minimum 12 characters)');
                $confirmation = $this->secret('Confirm new password');

                if (strlen($password) < 12 || ! hash_equals($password, $confirmation)) {
                    $this->error('Password must be at least 12 characters and both entries must match.');
                    return self::FAILURE;
                }

                $user->password = Hash::make($password);
                $user->name = $name;
                $user->save();
            } elseif (! $this->confirm('An account already exists. Promote it to Super Admin?', true)) {
                return self::SUCCESS;
            } else {
                $user->name = $name;
            }
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

        $role = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full platform access.',
                'is_system' => true,
            ]
        );

        $user->roles()->syncWithoutDetaching([$role->id]);
        $user->save();
        $this->info("Super Admin ready: {$user->email}");

        return self::SUCCESS;
    }
}
