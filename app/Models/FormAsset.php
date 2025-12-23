<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormAsset extends Model
{
    protected $fillable = [
        'form_id',
        'type',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}