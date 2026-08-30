<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{
  if(!Schema::hasTable('navigation_menu_items')){
    Schema::create('navigation_menu_items',function(Blueprint $table){$table->id();$table->string('menu',60)->default('main')->index();$table->string('group',100)->nullable()->index();$table->foreignId('parent_id')->nullable()->constrained('navigation_menu_items')->nullOnDelete();$table->string('label',160);$table->string('url',500)->nullable();$table->string('route_name',160)->nullable();$table->string('target',20)->default('_self');$table->string('icon',100)->nullable();$table->boolean('is_visible')->default(true)->index();$table->unsignedInteger('sort_order')->default(0)->index();$table->timestamps();$table->index(['menu','parent_id','sort_order']);});
    return;
  }
  Schema::table('navigation_menu_items',function(Blueprint $table){
    if(!Schema::hasColumn('navigation_menu_items','menu'))$table->string('menu',60)->default('main')->index();
    if(!Schema::hasColumn('navigation_menu_items','group'))$table->string('group',100)->nullable()->index();
    if(!Schema::hasColumn('navigation_menu_items','parent_id'))$table->unsignedBigInteger('parent_id')->nullable()->index();
    if(!Schema::hasColumn('navigation_menu_items','label'))$table->string('label',160)->default('');
    if(!Schema::hasColumn('navigation_menu_items','url'))$table->string('url',500)->nullable();
    if(!Schema::hasColumn('navigation_menu_items','route_name'))$table->string('route_name',160)->nullable();
    if(!Schema::hasColumn('navigation_menu_items','target'))$table->string('target',20)->default('_self');
    if(!Schema::hasColumn('navigation_menu_items','icon'))$table->string('icon',100)->nullable();
    if(!Schema::hasColumn('navigation_menu_items','is_visible'))$table->boolean('is_visible')->default(true)->index();
    if(!Schema::hasColumn('navigation_menu_items','sort_order'))$table->unsignedInteger('sort_order')->default(0)->index();
    if(!Schema::hasColumn('navigation_menu_items','created_at'))$table->timestamp('created_at')->nullable();
    if(!Schema::hasColumn('navigation_menu_items','updated_at'))$table->timestamp('updated_at')->nullable();
  });
}
 public function down():void{Schema::dropIfExists('navigation_menu_items');}
};