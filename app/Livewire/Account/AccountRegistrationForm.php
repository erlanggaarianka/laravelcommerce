<?php

namespace App\Livewire\Account;

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
    public $password_confirmation; // Added for confirmation

    // Modal properties
    public $showConfirmationModal = false;
    public $confirmationMessage = '';

    public function mount($id = null)
    {
        $this->id = $id;

        if ($this->id) {
            $user = User::findOrFail($this->id);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
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
        ];

        // Only update password if it's provided (for edit) or required (for create)
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

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
