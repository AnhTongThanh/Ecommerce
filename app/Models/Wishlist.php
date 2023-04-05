<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Summary of Wishlist
 */
class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlists';


    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_id',
    ];
}
