<?php

namespace App\Models;

use \App\Traits\Relations\BelongsTo;
use \App\Traits\Relations\Has;
use \App\Traits\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory, BelongsTo\Congrat, BelongsTo\NextQuestion, HasMany\Answers, HasMany\Hints;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text',
        'next_id',
        'type',
        'checkpoint',
        'congrat_id',
    ];

}
