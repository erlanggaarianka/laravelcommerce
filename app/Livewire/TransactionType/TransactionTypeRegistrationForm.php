<?php

namespace App\Livewire\TransactionType;

use App\Models\TransactionType;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class TransactionTypeRegistrationForm extends Component
{
    public $transactionTypeId;
    public $name;
    public $code;
    public $description;
    public $is_active = true;

    protected $listeners = ['editTransactionType' => 'edit'];

    public function mount($transactionType = null)
    {
        if ($transactionType) {
            $this->edit($transactionType);
        }
    }

    public function edit($transactionTypeId)
    {
        $transactionType = TransactionType::findOrFail($transactionTypeId);

        $this->transactionTypeId = $transactionType->id;
        $this->name = $transactionType->name;
        $this->code = $transactionType->code;
        $this->description = $transactionType->description;
        $this->is_active = $transactionType->is_active;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('transaction_types', 'code')->ignore($this->transactionTypeId)
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => Str::slug($this->code),
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->transactionTypeId) {
            $transactionType = TransactionType::findOrFail($this->transactionTypeId);
            $transactionType->update($data);
            session()->flash('message', 'Transaction type updated successfully!');
        } else {
            TransactionType::create($data);
            session()->flash('message', 'Transaction type created successfully!');
        }

        $this->redirect(route('transaction-types.list'));
    }

    public function render()
    {
        return view('livewire.transaction-type.transaction-type-registration-form');
    }
}
