<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;

class InsurancePolicyContent extends Model
{
    use HasNeighbors;

    protected $fillable = [
        'insurance_policy_id',
        'content',
        'embedding_model',
        'page_from',
        'page_to',
        'sort_order',
        'source_metadata',
    ];

    protected $hidden = [
        'embedding',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => Vector::class,
            'source_metadata' => 'array',
            'page_from' => 'integer',
            'page_to' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class, 'insurance_policy_id');
    }
}
