<?php

namespace App\Models\ReviewsRating;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class ReviewsRating extends Model
{
    use HasFactory,
        SoftDeletes,
        ReviewsRatingAccessories,
        ReviewsRatingRelationShip,
        ReviewsRatingScope;

    protected $table = 'reviews_rating';
    protected $guarded = [];
}
