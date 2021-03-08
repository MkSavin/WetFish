<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\Congrat as CongratModel;

trait Congrat
{

    /**
     * Belongs To Relation To Congrat
     *
     * @return Collection
     */
    public function congrat()
    {
        return $this->belongsTo(CongratModel::class);
    }

}
