<?php

namespace App;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Auditable;

    protected $table = 'services';

    protected $appends = ['attachments'];

    protected $fillable = [
        'name',
        'description',
        'service_type',
        'category_id',
        'cost',
        'contact_info',
        'status',
    ];

    public function category(){
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('attachments');
    }

}
