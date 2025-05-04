<?php

namespace App\Livewire\Account;

use App\Models\User;
use App\Models\Outlet;
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
    public $deleteOutlet;

    protected $listeners = ['refreshComponent' => '$refresh', 'accountsUpdated' => '$refresh'];

    public function render()
    {
        $accounts = User::with('outlet')
            ->when($this->searchTerm, function ($query) {
                return $query->where(function($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('email', 'like', '%'.$this->searchTerm.'%')
                      ->orWhereHas('outlet', function($outletQuery) {
                          $outletQuery->where('name', 'like', '%'.$this->searchTerm.'%');
                      });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.account.account-list', compact('accounts'));
    }

    public function confirmDelete($id)
    {
        $user = User::with('outlet')->findOrFail($id);
        $this->deleteId = $user->id;
        $this->deleteName = $user->name;
        $this->deleteEmail = $user->email;
        $this->deleteRole = $user->role;
        $this->deleteOutlet = $user->outlet ? $user->outlet->name : 'N/A';

        $this->dispatch('showDeleteModal');
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
