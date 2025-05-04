<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $deleteId;
    public $deleteName;
    public $deletePrice;
    public $deleteImage;

    protected $listeners = ['refreshComponent' => '$refresh', 'productsUpdated' => '$refresh'];

    public function render()
    {
        $products = Product::with('outlets')
            ->when($this->searchTerm, function ($query) {
                return $query->where(function($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('barcode', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('description', 'like', '%'.$this->searchTerm.'%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.products.product-list', compact('products'));
    }

    public function confirmDelete($id)
    {
        $product = Product::findOrFail($id);
        $this->deleteId = $product->id;
        $this->deleteName = $product->name;
        $this->deletePrice = $product->price;
        $this->deleteImage = $product->image;

        $this->dispatch('showDeleteModal');
    }

    public function deleteProduct()
    {
        try {
            $product = Product::findOrFail($this->deleteId);

            // Delete associated image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            $this->dispatch('hideDeleteModal');
            $this->dispatch('productsUpdated');
            session()->flash('message', 'Product deleted successfully!');
        } catch (\Exception $e) {
            $this->dispatch('hideDeleteModal');
            session()->flash('error', $e->getMessage());
        }
    }

    public function hydrate()
    {
        $this->dispatch('initializeDataTable');
    }

    public function dehydrate()
    {
        $this->dispatch('initializeDataTable');
    }
}
