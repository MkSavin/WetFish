<?php

namespace App\Traits\Relations\Has;

use App\Models\Question;

trait NextQuestion
{

    /**
     * HAS Relation To Next Question
     *
     * @return Collection
     */
    public function next()
    {
        return $this->hasOne(Question::class, 'next_id');
    }

}
