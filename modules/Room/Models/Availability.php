<?php
namespace Modules\Room\Models;

use App\BaseModel;

use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends Room
{
    use SoftDeletes;
 
    protected $table = 'room_availability';
    protected $fillable = [
        'room_id',
        'available_room',
        'start_date',
        'create_user',
        'update_user'
        
    ];
    

   
}