<?php

namespace App\Livewire\PaymentMethod;

use App\Models\PaymentMethod;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentMethodList extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeleteModal = false;
    public $selectedPaymentMethod = null;

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

    public function confirmDelete($paymentMethodId)
    {
        $this->selectedPaymentMethod = PaymentMethod::find($paymentMethodId);
        $this->dispatch('showDeleteModal');
    }

    public function deletePaymentMethod()
    {
        if ($this->selectedPaymentMethod) {
            $this->selectedPaymentMethod->delete();
            session()->flash('message', 'Payment method deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->selectedPaymentMethod = null;
    }

    public function render()
    {
        $paymentMethods = PaymentMethod::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                      ->orWhere('code', 'like', '%'.$this->searchTerm.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.payment-method.payment-method-list', [
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
