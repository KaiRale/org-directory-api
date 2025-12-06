<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_activities');
    }

    public function getSelfAndDescendantIds(): Collection
    {
        return $this->children()->pluck('id')->push($this->id);
    }

    public static function getSelfAndDescendantIdsForActivities(Collection $activities): Collection
    {
        return $activities->flatMap(function ($activity) {
            return $activity->getSelfAndDescendantIds();
        })->unique();
    }

    public static function findByTitleWithChildren(string $title): Collection
    {
        return static::with('children')
            ->where('title', 'like', "%{$title}%")
            ->get();
    }
}
