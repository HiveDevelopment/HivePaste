<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ApiToken extends Model
{
    protected $fillable = ['name','token_hash','last_used_at','revoked_at'];
    protected $hidden = ['token_hash'];
    protected function casts(): array { return ['last_used_at'=>'datetime','revoked_at'=>'datetime']; }
}
