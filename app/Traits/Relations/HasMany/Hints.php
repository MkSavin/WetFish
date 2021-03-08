<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Hint;

trait Hints
{

    /**
     * HAS MANY Relation To Hint
     *
     * @return Collection
     */
    public function hints()
    {
        return $this->hasMany(Hint::class);
    }

}
