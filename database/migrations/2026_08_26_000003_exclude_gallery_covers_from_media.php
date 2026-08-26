<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $galleries=DB::table('site_content_items')->where('type','gallery')->whereNotNull('image_path')->get(['id','image_path']);
        foreach($galleries as $gallery){DB::table('gallery_media')->where('gallery_id',$gallery->id)->where('path',$gallery->image_path)->delete();}
    }

    public function down(): void
    {
        // Cover media remains excluded by design.
    }
};
