<?php

namespace App\Livewire\Outlet;

use App\Models\Outlet;
use Livewire\Component;

class OutletRegistrationForm extends Component
{
    public $id;
    public $name;
    public $address;
    public $phone;

    // Modal properties
    public $showConfirmationModal = false;
    public $confirmationMessage = '';

    public function mount($id = null)
    {
        $this->id = $id;

        if ($this->id) {
            $outlet = Outlet::findOrFail($this->id);
            $this->name = $outlet->name;
            $this->address = $outlet->address;
            $this->phone = $outlet->phone;
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Outlet name is required.',
            'address.required' => 'Address is required.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
        ];
    }

    public function confirmSave()
    {
        $this->validate();

        $this->confirmationMessage = $this->id
            ? "Are you sure you want to update this outlet?"
            : "Are you sure you want to create this new outlet?";

        $this->dispatch('showConfirmation');
    }

    public function save()
    {
        $validated = $this->validate();

        $outletData = [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
        ];

        if ($this->id) {
            $outlet = Outlet::findOrFail($this->id);
            $outlet->update($outletData);
            $message = 'Outlet updated successfully!';
        } else {
            Outlet::create($outletData);
            $message = 'Outlet created successfully!';
        }

        $this->showConfirmationModal = false;
        session()->flash('message', $message);
        return redirect()->route('outlet.list');
    }

    public function render()
    {
        return view('livewire.outlet.outlet-registration-form');
    }
}
