<?php
namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    protected $table = "rotation";
    protected $primaryKey = "rotation_id";
    protected $hidden = [];
}
