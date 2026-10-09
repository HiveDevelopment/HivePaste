<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
class Paste extends Model
{
    use HasUuids;
    protected $fillable = ['slug','title','content','language','visibility','expires_at','api_token_id','management_token_hash'];
    protected $hidden = ['api_token_id', 'management_token_hash'];
    protected function casts(): array { return ['expires_at' => 'datetime']; }
    public function getRouteKeyName(): string { return 'slug'; }
    public static function newSlug(): string
    {
        do { $slug = (string) Str::uuid(); } while (static::where('slug', $slug)->exists());
        return $slug;
    }
    public function isExpired(): bool { return $this->expires_at !== null && $this->expires_at->isPast(); }
}
