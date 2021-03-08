<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Answer;

trait Answers
{

    /**
     * HAS MANY Relation To Answer
     *
     * @return Collection
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

}
