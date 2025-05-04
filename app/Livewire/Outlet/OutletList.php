<?php

namespace App\Livewire\Outlet;

use App\Models\Outlet;
use Livewire\Component;
use Livewire\WithPagination;

class OutletList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $deleteId;
    public $deleteName;
    public $deleteAddress;

    protected $listeners = ['refreshComponent' => '$refresh', 'outletsUpdated' => '$refresh'];

    public function render()
    {
        $outlets = Outlet::when($this->searchTerm, function ($query) {
            return $query->where('name', 'like', '%'.$this->searchTerm.'%')
                         ->orWhere('address', 'like', '%'.$this->searchTerm.'%')
                         ->orWhere('phone', 'like', '%'.$this->searchTerm.'%');
        })
        ->latest()
        ->paginate(10);

        return view('livewire.outlet.outlet-list', compact('outlets'));
    }

    public function confirmDelete($id)
    {
        $outlet = Outlet::findOrFail($id);
        $this->deleteId = $outlet->id;
        $this->deleteName = $outlet->name;
        $this->deleteAddress = $outlet->address;

        $this->dispatch('showDeleteModal');
    }

    public function deleteOutlet()
    {
        try {
            $outlet = Outlet::findOrFail($this->deleteId);

            // Prevent deletion if outlet has users assigned
            if ($outlet->users()->exists()) {
                throw new \Exception("Cannot delete outlet with assigned users!");
            }

            $outlet->delete();

            $this->dispatch('hideDeleteModal');
            $this->dispatch('outletsUpdated');
            session()->flash('message', 'Outlet deleted successfully!');
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
