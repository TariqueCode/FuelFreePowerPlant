<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class NavigationMenuItem extends Model {
 protected $fillable=['menu','group','parent_id','label','url','route_name','target','icon','is_visible','sort_order'];
 protected $casts=['is_visible'=>'boolean','sort_order'=>'integer'];
 public function parent(): BelongsTo{return $this->belongsTo(self::class,'parent_id');}
 public function children(): HasMany{return $this->hasMany(self::class,'parent_id')->orderBy('sort_order')->orderBy('id');}
}