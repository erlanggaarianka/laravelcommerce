<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\Outlet;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProductRegistrationForm extends Component
{
    use WithFileUploads;

    public $id;
    public $name;
    public $barcode;
    public $description;
    public $price;
    public $cost_price;
    public $image;
    public $tempImage;
    public $minimum_stock = 10;
    public $is_active = true;
    public $selectedOutlets = [];
    public $outlets = [];

    // Modal properties
    public $showConfirmationModal = false;
    public $confirmationMessage = '';

    public function mount($id = null)
    {
        $this->outlets = Outlet::all();
        $this->id = $id;

        if ($this->id) {
            $product = Product::with('outlets')->findOrFail($this->id);
            $this->name = $product->name;
            $this->barcode = $product->barcode;
            $this->description = $product->description;
            $this->price = $product->price;
            $this->cost_price = $product->cost_price;
            $this->minimum_stock = $product->minimum_stock;
            $this->is_active = $product->is_active;
            $this->selectedOutlets = $product->outlets->pluck('id')->toArray();
            $this->tempImage = $product->image;
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,'.$this->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048', // 2MB max
            'minimum_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'selectedOutlets' => 'required|array|min:1',
            'selectedOutlets.*' => 'exists:outlets,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'barcode.unique' => 'This barcode is already in use.',
            'price.required' => 'Price is required.',
            'selectedOutlets.required' => 'Please select at least one outlet.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image must not exceed 2MB.',
        ];
    }

    public function updatedImage()
    {
        $this->validateOnly('image');
    }

    public function confirmSave()
    {
        $this->validate();

        $this->confirmationMessage = $this->id
            ? "Are you sure you want to update this product?"
            : "Are you sure you want to create this new product?";

        $this->dispatch('showConfirmation');
    }

    public function save()
    {
        $validated = $this->validate();

        $productData = [
            'name' => $this->name,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'minimum_stock' => $this->minimum_stock,
            'is_active' => $this->is_active,
        ];

        // Handle image upload
        if ($this->image) {
            // Delete old image if exists
            if ($this->tempImage) {
                Storage::disk('public')->delete($this->tempImage);
            }
            $productData['image'] = $this->image->store('products', 'public');
        }

        if ($this->id) {
            $product = Product::findOrFail($this->id);
            $product->update($productData);
            $product->outlets()->sync($this->selectedOutlets);
            $message = 'Product updated successfully!';
        } else {
            $product = Product::create($productData);
            $product->outlets()->sync($this->selectedOutlets);
            $message = 'Product created successfully!';
        }

        $this->showConfirmationModal = false;
        session()->flash('message', $message);
        return redirect()->route('products.list');
    }

    public function render()
    {
        return view('livewire.products.product-registration-form');
    }
}
