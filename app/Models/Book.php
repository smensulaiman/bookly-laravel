<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $language
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Database\Factories\BookFactory factory($count = null, $state = [])
 * @method static Builder|Book highestRated()
 * @method static Builder|Book newModelQuery()
 * @method static Builder|Book newQuery()
 * @method static Builder|Book popular()
 * @method static Builder|Book query()
 * @method static Builder|Book title(string $title)
 * @method static Builder|Book whereAuthor($value)
 * @method static Builder|Book whereCreatedAt($value)
 * @method static Builder|Book whereId($value)
 * @method static Builder|Book whereLanguage($value)
 * @method static Builder|Book wherePublishedAt($value)
 * @method static Builder|Book whereTitle($value)
 * @method static Builder|Book whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Book extends Model
{
    use HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $queryBuilder, string $title): Builder
    {
        return $queryBuilder->where('title', 'LIKE', "%$title%");
    }

    public function scopePopular(Builder $queryBuilder): Builder
    {
        return $queryBuilder->withCount('reviews')
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $queryBuilder): Builder
    {
        return $queryBuilder->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating','desc');
    }

}
