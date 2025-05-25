<?php

namespace App\Livewire\Transaction;

use App\Models\Transaction;
use App\Models\Outlet;
use App\Models\User;
use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Auth;
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
    public $showCancelModal = false;
    public $selectedTransaction = null;
    public $cancelReason = '';

    public $outlets = [];
    public $users = [];
    public $paymentMethods = ['cash', 'credit_card', 'debit_card', 'e_wallet'];
    public $statuses = ['completed', 'pending', 'cancelled'];

    public function mount()
    {
        $this->outlets = Outlet::all();
        $this->users = User::whereHas('outlet')->get();
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

    public function confirmCancel($transactionId)
    {
        $this->selectedTransaction = Transaction::find($transactionId);
        $this->cancelReason = '';
        $this->dispatch('showCancelModal');
    }

    public function cancelTransaction()
    {
        $this->validate([
            'cancelReason' => 'required|string|max:255'
        ]);

        if ($this->selectedTransaction && $this->selectedTransaction->status === 'completed') {
            // Restore inventory for each item
            foreach ($this->selectedTransaction->items as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)
                    ->where('outlet_id', $this->selectedTransaction->outlet_id)
                    ->first();

                if ($inventory) {
                    // Update inventory quantity
                    $inventory->increment('quantity', $item->quantity);

                    // Create inventory log
                    $inventory->logs()->create([
                        'user_id' => Auth::id(),
                        'quantity' => $item->quantity,
                        'reason' => 'Transaction Cancellation: ' . $this->selectedTransaction->invoice_number,
                        'remaining_stock' => $inventory->quantity,
                        'action' => 'add'
                    ]);
                }
            }

            // Update transaction status
            $this->selectedTransaction->update([
                'status' => 'cancelled',
                'notes' => $this->selectedTransaction->notes
                    ? $this->selectedTransaction->notes . "\nCancellation Reason: " . $this->cancelReason
                    : "Cancellation Reason: " . $this->cancelReason
            ]);

            session()->flash('message', 'Transaction cancelled and inventory restored successfully!');
        }

        $this->reset(['showCancelModal', 'selectedTransaction', 'cancelReason']);
        $this->dispatch('hideCancelModal');
    }

    public function closeModal()
    {
        $this->reset(['showDetailModal', 'showCancelModal', 'selectedTransaction', 'cancelReason']);
        $this->dispatch('hideCancelModal');
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
