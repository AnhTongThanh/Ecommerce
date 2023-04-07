<?php

namespace App\Http\Livewire\Frontend;

use Livewire\Component;
use App\Models\Wishlist;

class WishlistShow extends Component
{
    public function removeWishlistItem(int $wishlishId)
    {
        Wishlist::where('user_id', auth()->user()->id)->where('id', $wishlishId)->delete();
        $this->emit('wishlistAddedUpdated');
        session()->flash('message', 'Wishlist Item Removed Successfully');
        // $this->dispatchBrowserEvent('message', [
        //     'text' => 'Wishlist Item Removed Successfully',
        //     'type' => 'success',
        //     'status' => 200
        // ]);
    }

    public function render()
    {
        $wishlist = Wishlist::where('user_id', auth()->user()->id)->get();
        return view('livewire.frontend.wishlist-show', [
            'wishlist' => $wishlist
        ]);
    }
}
