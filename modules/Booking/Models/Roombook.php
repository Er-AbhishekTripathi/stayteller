<?php
namespace Modules\Booking\Models;

use App\BaseModel;
use Illuminate\Support\Facades\DB;
use Modules\Tour\Models\Tour;

class Roombook extends BaseModel
{
    protected $table = 'room_booking';

    protected $fillable = [
        'user_id',
        'room_id',
        'booking_date',
        'booking_amount',
        'payment_type',
        'payment_status',
        'code',
        'create_user',
        'update_user'

    ];
} 