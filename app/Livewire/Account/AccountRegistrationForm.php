<?php

namespace App\Livewire\Account;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AccountRegistrationForm extends Component
{
    public $id;
    public $name;
    public $email;
    public $role;
    public $password;
    public $password_confirmation;
    public $outlet_id;
    public $outlets = [];

    // Modal properties
    public $showConfirmationModal = false;
    public $confirmationMessage = '';

    public function mount($id = null)
    {
        $this->outlets = Outlet::all(); // Load all outlets

        $this->id = $id;

        if ($this->id) {
            $user = User::findOrFail($this->id);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->outlet_id = $user->outlet_id;
        }
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->id,
            'role' => 'required|in:Cashier,Owner',
        ];

        return $rules;
    }

    public function updatedRole($value)
    {
        // Reset outlet if role changes to Owner
        if ($value === 'Owner') {
            $this->outlet_id = null;
        }
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'role.required' => 'Role is required.',
            'role.in' => 'Role must be either Cashier or Owner.',
            'outlet_id.required' => 'Outlet is required for Cashiers.',
            'outlet_id.exists' => 'Selected outlet does not exist.',
        ];
    }

    public function confirmSave()
    {
        $this->validate();

        $this->confirmationMessage = $this->id
            ? "Are you sure you want to update this user account?"
            : "Are you sure you want to register this new user?";

        $this->dispatch('showConfirmation');
    }

    public function save()
    {
        $validated = $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'password' => Hash::make($this->password),
            'outlet_id' => $this->role === 'Cashier' ? $this->outlet_id : null,
        ];

        if ($this->id) {
            $user = User::findOrFail($this->id);
            $user->update($userData);
            $message = 'User updated successfully!';
        } else {
            User::create($userData);
            $message = 'User registered successfully!';
        }

        $this->showConfirmationModal = false;
        session()->flash('message', $message);
        return redirect()->route('account.list');
    }

    public function render()
    {
        return view('livewire.account.account-registration-form');
    }
}
