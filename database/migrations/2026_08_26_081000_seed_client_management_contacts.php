<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $members = [
            ['name' => 'Nazim Uddin Alam', 'designation' => 'Chairman', 'phone' => '+880 1711-336465', 'email' => 'chairman@fuelfreepowerplant.com', 'sort_order' => 1],
            ['name' => 'Mahbubur Rahman', 'designation' => 'Vice Chairman', 'phone' => '+880 1612-123033', 'email' => 'vc@fuelfreepowerplant.com', 'sort_order' => 2],
            ['name' => 'Principal MR Karim', 'designation' => 'Managing Director', 'phone' => '+880 1712-251892', 'email' => 'md@fuelfreepowerplant.com', 'sort_order' => 3],
            ['name' => 'Md. Rezaul Alam Moshi', 'designation' => 'Deputy Managing Director', 'phone' => '+880 1644-449755', 'email' => 'dmd@fuelfreepowerplant.com', 'sort_order' => 4],
            ['name' => 'Lt. Col. (Retd.) SK. Akram Ali, Ph.D', 'designation' => 'Director (Admin)', 'phone' => '+880 1770-865178', 'email' => 'admin@fuelfreepowerplant.com', 'sort_order' => 5],
            ['name' => 'Engr. A.K.M. Rezaul Karim', 'designation' => 'Director (Finance)', 'phone' => '+880 1322-560091', 'email' => 'df@fuelfreepowerplant.com', 'sort_order' => 6],
            ['name' => 'SK. Kamrul Islam', 'designation' => 'Director (Foreign Affairs)', 'phone' => '+44 7424 932087', 'email' => 'dfa@fuelfreepowerplant.com', 'sort_order' => 7],
            ['name' => 'K. M. Sultan Mahmud', 'designation' => 'Director (HR)', 'phone' => '+880 1923-794092', 'email' => 'dhr@fuelfreepowerplant.com', 'sort_order' => 8],
        ];

        foreach ($members as $member) {
            $existing = DB::table('site_content_items')->where('type', 'management')->where('email', $member['email'])->first();
            $payload = [
                'type' => 'management',
                'title' => $member['name'],
                'slug' => Str::slug($member['name']),
                'excerpt' => $member['designation'],
                'designation' => $member['designation'],
                'phone' => $member['phone'],
                'email' => $member['email'],
                'status' => $existing?->status ?? 'published',
                'sort_order' => $existing?->sort_order ?: $member['sort_order'],
                'updated_at' => now(),
            ];
            if ($existing) {
                DB::table('site_content_items')->where('id', $existing->id)->update($payload);
            } else {
                $payload['created_at'] = now();
                DB::table('site_content_items')->insert($payload);
            }
        }
    }

    public function down(): void
    {
        DB::table('site_content_items')->where('type', 'management')->whereIn('email', [
            'chairman@fuelfreepowerplant.com', 'vc@fuelfreepowerplant.com', 'md@fuelfreepowerplant.com',
            'dmd@fuelfreepowerplant.com', 'admin@fuelfreepowerplant.com', 'df@fuelfreepowerplant.com',
            'dfa@fuelfreepowerplant.com', 'dhr@fuelfreepowerplant.com',
        ])->delete();
    }
};
