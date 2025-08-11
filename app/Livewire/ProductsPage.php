<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\Enums\Position;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

#[Title('Products Page')]

class ProductsPage extends Component
{
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $sale;

    public $price_range = 300000;

    #[Url]
    public $sort = 'latest';

    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count : $total_count)->to(Navbar::class);

        LivewireAlert::title('Added to cart')
            ->position(Position::BottomEnd)
            ->success()
            ->toast()
            ->show();
    }
    public function render()
    {
        $productsWithQuery = Product::query()->where('is_active',1);

        if(!empty($this->selected_categories)){
            $productsWithQuery->whereIn('category_id',$this->selected_categories);
        }

        if(!empty($this->selected_brands)){
            $productsWithQuery->whereIn('brand_id',$this->selected_brands);
        }

        if($this->featured){
            $productsWithQuery->where('is_featured',1);
        }

        if($this->sale){
            $productsWithQuery->where('on_sale',1);
        }

        if($this->price_range){
            $productsWithQuery->whereBetween('price',[0, $this->price_range]);
        }

        if($this->sort == 'latest'){
            $productsWithQuery->latest();
        }
        if($this->sort == 'price'){
            $productsWithQuery->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $productsWithQuery->paginate(9),
            'brands' => Brand::where('is_active',1)->get(['id', 'name','slug']),
            'categories' => Category::where('is_active',1)->get(['id', 'name','slug']),
        ]);
    }
}
