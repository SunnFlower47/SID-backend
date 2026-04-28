<?php

namespace App\Traits;

trait HasWilayahLabels
{
    /**
     * Accessors for wilayah labels from master tables
     */
    public function getRtLabelAttribute(): string
    {
        return $this->rtMaster ? $this->rtMaster->kode : '-';
    }

    public function getRwLabelAttribute(): string
    {
        return $this->rwMaster ? $this->rwMaster->kode : '-';
    }

    public function getDusunLabelAttribute(): string
    {
        return $this->dusunMaster ? $this->dusunMaster->nama : '-';
    }

    /**
     * Get combined RT/RW label
     */
    public function getRtRwLabelAttribute(): string
    {
        return "{$this->rt_label}/{$this->rw_label}";
    }
}
