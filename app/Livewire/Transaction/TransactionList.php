<?php

namespace App\Livewire\Transaction;

use App\Models\Transaction;
use App\Models\Outlet;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $outletFilter = '';
    public $userFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $paymentMethodFilter = '';
    public $statusFilter = '';

    // For modals
    public $showDetailModal = false;
    public $selectedTransaction = null;

    public $outlets = [];
    public $users = [];
    public $paymentMethods = ['cash', 'credit_card', 'debit_card', 'e_wallet'];
    public $statuses = ['completed', 'pending', 'cancelled'];

    public function mount()
    {
        $this->outlets = Outlet::all();
        $this->users = User::whereHas('outlet')->get(); // Assuming cashiers have outlet relationship
    }

    public function render()
    {
        $transactions = Transaction::with(['outlet', 'user', 'items.product'])
            ->when($this->searchTerm, function ($query) {
                return $query->where('invoice_number', 'like', '%'.$this->searchTerm.'%');
            })
            ->when($this->outletFilter, function ($query) {
                return $query->where('outlet_id', $this->outletFilter);
            })
            ->when($this->userFilter, function ($query) {
                return $query->where('user_id', $this->userFilter);
            })
            ->when($this->paymentMethodFilter, function ($query) {
                return $query->where('payment_method', $this->paymentMethodFilter);
            })
            ->when($this->statusFilter, function ($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFrom && $this->dateTo, function ($query) {
                return $query->whereBetween('created_at', [
                    $this->dateFrom . ' 00:00:00',
                    $this->dateTo . ' 23:59:59'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('livewire.transaction.transaction-list', compact('transactions'));
    }

    public function openDetailModal($transactionId)
    {
        $this->selectedTransaction = Transaction::with(['outlet', 'user', 'items.product'])
            ->find($transactionId);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->reset(['showDetailModal', 'selectedTransaction']);
    }

    public function resetFilters()
    {
        $this->reset([
            'searchTerm',
            'outletFilter',
            'userFilter',
            'dateFrom',
            'dateTo',
            'paymentMethodFilter',
            'statusFilter'
        ]);
        $this->resetPage();
    }
}
