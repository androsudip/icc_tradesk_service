<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = "bill";

    protected $fillable = [
        'ticket_id',
        'bill_cost',
        'created_by'
    ];

    public function billServices(){
        return $this->hasMany(BillTicketServices::class,'bill_id','id');
    }

    public function ticket(){
        return $this->hasOne(Ticket::class,'id','ticket_id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','created_by');
    }
}
