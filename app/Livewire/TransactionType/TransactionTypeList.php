<?php

namespace App\Livewire\TransactionType;

use App\Models\TransactionType;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionTypeList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeleteModal = false;
    public $selectedTransactionType = null;

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function confirmDelete($transactionTypeId)
    {
        $this->selectedTransactionType = TransactionType::find($transactionTypeId);
        $this->dispatch('showDeleteModal');
    }

    public function deleteTransactionType()
    {
        if ($this->selectedTransactionType) {
            $this->selectedTransactionType->delete();
            session()->flash('message', 'Transaction type deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->selectedTransactionType = null;
    }

    public function render()
    {
        $transactionTypes = TransactionType::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('code', 'like', '%'.$this->searchTerm.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.transaction-type.transaction-type-list', [
            'transactionTypes' => $transactionTypes,
        ]);
    }
}
