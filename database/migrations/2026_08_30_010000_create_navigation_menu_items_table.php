<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{Schema::create('navigation_menu_items',function(Blueprint $table){$table->id();$table->string('menu',60)->default('main')->index();$table->string('group',100)->nullable()->index();$table->foreignId('parent_id')->nullable()->constrained('navigation_menu_items')->nullOnDelete();$table->string('label',160);$table->string('url',500)->nullable();$table->string('route_name',160)->nullable();$table->string('target',20)->default('_self');$table->string('icon',100)->nullable();$table->boolean('is_visible')->default(true)->index();$table->unsignedInteger('sort_order')->default(0)->index();$table->timestamps();$table->index(['menu','parent_id','sort_order']);});}
 public function down():void{Schema::dropIfExists('navigation_menu_items');}
};