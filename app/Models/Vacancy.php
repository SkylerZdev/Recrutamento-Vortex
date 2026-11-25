<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'contract', 'paused'];

    protected $attributes = ['paused' => false];

    protected function casts(): array
    {
        return ['paused' => 'boolean'];
    }

    public function acceptsApplications(): bool
    {
        return ! $this->paused;
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class);
    }
}
