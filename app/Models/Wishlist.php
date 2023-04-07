<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    /**
     * Summary of user
     * @return BelongsTo
     */
    public function product() :BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
