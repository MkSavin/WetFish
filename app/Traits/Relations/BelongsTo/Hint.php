<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\Hint as HintModel;

trait Hint
{

    /**
     * Belongs To Relation To Hint
     *
     * @return Collection
     */
    public function hint()
    {
        return $this->belongsTo(HintModel::class);
    }

}
