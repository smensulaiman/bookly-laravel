<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $queryBuilder, string $title): Builder
    {
        return $queryBuilder->where('title', 'LIKE', "%$title%");
    }

    /**
     * @param Builder $queryBuilder
     * @param $from
     * @param $to
     * @return Builder
     * @method withReviewsCount()
     */
    public function scopeWithReviewsCount(Builder $queryBuilder, $from = null, $to = null): Builder
    {
        return $queryBuilder->withCount(array(
            'reviews' => function (Builder $qb) use ($to, $from) {
                return $this->dateRangeFilter($qb, $from, $to);
            }
        ));
    }

    public function scopeWithReviewsAverageRating(Builder $queryBuilder, $from = null, $to = null): Builder
    {
        return $queryBuilder->withAvg(array(
            'reviews' => function (Builder $qb) use ($from, $to) {
                return $this->dateRangeFilter($qb, $from, $to);
            }
        ), 'rating');
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating')
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }

    private function dateRangeFilter(Builder $query, $from = null, $to = null): void
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $queryBuilder): Builder
    {
        return $queryBuilder->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLastSixMonths(Builder $queryBuilder): Builder
    {
        return $queryBuilder->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $queryBuilder): Builder
    {
        return $queryBuilder->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLastSixMonths(Builder $queryBuilder): Builder
    {
        return $queryBuilder->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(2);
    }

}
