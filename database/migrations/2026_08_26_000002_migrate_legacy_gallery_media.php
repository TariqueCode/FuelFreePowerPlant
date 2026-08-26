<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $galleries = DB::table('site_content_items')->where('type', 'gallery')->whereNotNull('content')->get(['id','content']);
        foreach ($galleries as $gallery) {
            preg_match_all('/<(img|video)[^>]+(?:src|poster)=["\']([^"\']+)["\'][^>]*>/i', $gallery->content, $matches);
            $order = 0;
            foreach ($matches[2] ?? [] as $url) {
                $path = parse_url($url, PHP_URL_PATH) ?: $url;
                $path = preg_replace('#^/storage/#', '', $path);
                if (!$path || DB::table('gallery_media')->where('gallery_id',$gallery->id)->where('path',$path)->exists()) continue;
                $type = preg_match('/\.(mp4|webm|mov)(\?.*)?$/i', $path) ? 'video' : 'image';
                DB::table('gallery_media')->insert(['gallery_id'=>$gallery->id,'type'=>$type,'path'=>$path,'original_name'=>basename($path),'sort_order'=>$order++,'created_at'=>now(),'updated_at'=>now()]);
            }
        }
    }

    public function down(): void
    {
        // Media created by this migration is indistinguishable from later uploads; keep it intact on rollback.
    }
};
