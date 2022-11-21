<?php
namespace Modules\Agencies\Models;

use App\BaseModel;

class AgenciesAgent extends BaseModel
{
    protected $table = 'agencies_agent';
    protected $fillable = [
        'agencies_id',
        'agent_id',
    ];
    public $timestamps = true;
}
