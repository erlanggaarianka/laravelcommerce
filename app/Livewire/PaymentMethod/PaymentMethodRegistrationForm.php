<?php

namespace App\Livewire\PaymentMethod;

use App\Models\PaymentMethod;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PaymentMethodRegistrationForm extends Component
{

    public $paymentMethodId;
    public $name;
    public $code;
    public $description;
    public $is_active = true;

    protected $listeners = ['editPaymentMethod' => 'edit'];

    public function mount($id = null)
    {
        if ($id) {
            $this->edit($id);
        }
    }

    public function edit($paymentMethodId)
    {
        $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);

        $this->paymentMethodId = $paymentMethod->id;
        $this->name = $paymentMethod->name;
        $this->code = $paymentMethod->code;
        $this->description = $paymentMethod->description;
        $this->is_active = $paymentMethod->is_active;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('payment_methods', 'code')->ignore($this->paymentMethodId)
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
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->paymentMethodId) {
            $paymentMethod = PaymentMethod::findOrFail($this->paymentMethodId);
            $paymentMethod->update($data);
            session()->flash('message', 'Payment method updated successfully!');
        } else {
            PaymentMethod::create($data);
            session()->flash('message', 'Payment method created successfully!');
        }

        $this->redirect(route('payment-methods.list'));
    }

    public function render()
    {
        return view('livewire.payment-method.payment-method-registration-form');
    }
}
