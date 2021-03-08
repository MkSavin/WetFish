<?php

namespace App\Traits\Relations\Has;

use App\Models\Question as QuestionModel;

trait Question
{

    /**
     * HAS Relation To Congrat
     *
     * @return Collection
     */
    public function question()
    {
        return $this->hasOne(QuestionModel::class);
    }

}
