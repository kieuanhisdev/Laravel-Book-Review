<?php

namespace App\Models;

use Closure;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    use HasFactory;

     public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, ?string $title): Builder{
        return $query->where("title","like","%".$title."%");
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder{
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ])->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder{
        return $query->withAvg([
            'reviews'=> fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating')->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder{
        return $query->having('reviews_count','>', $minReviews);
    }

    private function dateRangeFilter(Builder $query, int $from =null, int $to = null){
        if($from && ! $to){
            $query->where('created_at','>=', $from);
        }
        elseif (!$from && $to) {
            # code...
            $query->where('created_at','<=', $to);
        }
        elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopeWithRecentReviews(Builder $query, Closure $interval ) : Builder{
        return $query->whereHas('reviews', function(Builder $q) use ($interval) {
            $q->whereBetween('created_at', [$interval(now()), now()]);
        }
        );
    }

     public function scopeWithLastWeekReviews(Builder $query): Builder
    {
        return $query->withRecentReviews(fn($date) => $date->subWeek());
    }


}
