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
    public $is_tax_enabled = false; // <-- Add property, default to false
    public $tax_rate;

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
            $this->is_tax_enabled = $outlet->is_tax_enabled; // <-- Initialize
            $this->tax_rate = $outlet->tax_rate;
        } else {
            // Defaults for new outlets
            $this->is_tax_enabled = false; // Explicitly set default
            $this->tax_rate = 0.00;
        }
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_tax_enabled' => 'required|boolean', // <-- Add validation
        ];

        // Tax rate is only required if tax is enabled
        if ($this->is_tax_enabled) {
            $rules['tax_rate'] = 'required|numeric|min:0|max:100';
        } else {
            // If tax is not enabled, tax_rate can be nullable or not validated as strictly
            // For simplicity, we can just make it nullable or ensure it's reset
            $rules['tax_rate'] = 'nullable|numeric|min:0|max:100';
        }
        return $rules;
    }

    public function messages()
    {
        $messages = [
            'name.required' => 'Outlet name is required.',
            'address.required' => 'Address is required.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'is_tax_enabled.required' => 'Please specify if tax is enabled or disabled.',
        ];

        if ($this->is_tax_enabled) {
            $messages['tax_rate.required'] = 'Tax rate is required when tax is enabled.';
            $messages['tax_rate.numeric'] = 'Tax rate must be a number.';
            $messages['tax_rate.min'] = 'Tax rate must be at least 0.';
            $messages['tax_rate.max'] = 'Tax rate must not exceed 100.';
        }
        return $messages;
    }

    // This method is useful if you want to react instantly when is_tax_enabled changes
    // For example, to reset tax_rate if tax is disabled.
    public function updatedIsTaxEnabled($value)
    {
        if (!$value) {
            $this->tax_rate = 0.00; // Or null, or just leave it as is and rely on save logic
        }
        // $this->resetValidation('tax_rate'); // Optionally reset validation for tax_rate
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
            'is_tax_enabled' => $this->is_tax_enabled, // <-- Add to data
            // Only save tax_rate if tax is enabled, or save the current value (could be 0 if disabled)
            'tax_rate' => $this->is_tax_enabled ? $this->tax_rate : 0.00,
        ];

        // If tax is disabled, you might want to explicitly set tax_rate to 0 or null in the database
        if (!$this->is_tax_enabled) {
            $outletData['tax_rate'] = 0.00; // Or null, depending on your DB column definition and preference
        }


        if ($this->id) {
            $outlet = Outlet::findOrFail($this->id);
            $outlet->update($outletData);
            $message = 'Outlet updated successfully!';
        } else {
            Outlet::create($outletData);
            $message = 'Outlet created successfully!';
        }

        $this->dispatch('hideConfirmation');
        session()->flash('message', $message);
        return redirect()->route('outlet.list');
    }

    public function render()
    {
        return view('livewire.outlet.outlet-registration-form');
    }
}
