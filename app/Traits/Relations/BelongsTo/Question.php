<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\Question as QuestionModel;

trait Question
{

    /**
     * Belongs To Relation To Congrat
     *
     * @return Collection
     */
    public function question()
    {
        return $this->belongsTo(QuestionModel::class);
    }

}
