<?php

use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesDB;
use IlluminateSupportFacadesSchema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_links', function (Blueprint $table) {
            $table->string('platform', 40)->nullable()->after('id')->index();
        });

        $platforms = config('fuelfree.social.platforms', []);
        foreach (DB::table('social_links')->get() as $link) {
            $platform = 'website';
            foreach ($platforms as $key => $meta) {
                if (strcasecmp($link->label, $meta['label']) === 0) {
                    $platform = $key;
                    break;
                }
            }
            DB::table('social_links')->where('id', $link->id)->update(['platform' => $platform]);
        }

        Schema::table('social_links', function (Blueprint $table) {
            $table->string('platform', 40)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('social_links', function (Blueprint $table) {
            $table->dropColumn('platform');
        });
    }
};