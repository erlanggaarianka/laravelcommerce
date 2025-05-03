<?php

namespace App\Livewire\Account;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AccountList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $deleteId;
    public $deleteName;
    public $deleteEmail;
    public $deleteRole;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function render()
    {
        $accounts = User::when($this->searchTerm, function ($query) {
            return $query->where('name', 'like', '%'.$this->searchTerm.'%')
                         ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
        })
        ->latest()
        ->paginate(10);

        return view('livewire.account.account-list', compact('accounts'));
    }

    public function confirmDelete($id)
    {
        $user = User::findOrFail($id);
        $this->deleteId = $user->id;
        $this->deleteName = $user->name;
        $this->deleteEmail = $user->email;
        $this->deleteRole = $user->role;

        $this->dispatch('showDeleteModal');
        $this->dispatch('initializeDataTable');
    }

    public function deleteUser()
    {
        try {
            $user = User::findOrFail($this->deleteId);

            // Prevent deleting own account
            if ($user->id === Auth::user()->id) {
                throw new \Exception("You cannot delete your own account!");
            }

            $user->delete();

            $this->dispatch('hideDeleteModal');
            $this->dispatch('accountsUpdated');
            session()->flash('message', 'User account deleted successfully!');
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
