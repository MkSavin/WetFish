<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\Question;

trait NextQuestion
{

    /**
     * Belongs To Relation To Next Question
     *
     * @return Collection
     */
    public function next()
    {
        return $this->belongsTo(Question::class, 'next_id');
    }

}
