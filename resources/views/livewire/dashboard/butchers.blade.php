<?php

use Livewire\Volt\Component;
use App\Models\Butcher;

new class extends Component {
    //
    public $butchers;
    public $name;
    // public $phone;
    public $isEdit = false;


    public function mount()
    {
        $this->loadButchers();
    }

    public function loadButchers()
    {
        $this->butchers = Butcher::latest()->get();
    }

    public function resetForm()
    {
        $this->name = '';
        //$this->phone = '';
        $this->isEdit = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            //'phone' => 'required|string',
        ]);

        Butcher::create([
            'name' => $this->name,
            //'phone' => $this->phone,
        ]);

        session()->flash('message', 'Butcher created successfully.');
        $this->resetForm();
        $this->loadButchers();
    }

    public function edit($id)
    {
        $butcher = Butcher::findOrFail($id);
        $this->name = $butcher->name;
        // $this->phone = $butcher->phone;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            // 'phone' => 'required|string',
        ]);

        $butcher = Butcher::findOrFail($this->butcherId);
        $butcher->update([
            'name' => $this->name,
            // 'phone' => $this->phone,
        ]);

        session()->flash('message', 'Butcher updated successfully.');
        $this->resetForm();
        $this->loadButchers();
    }

    public function delete($id)
    {
        Butcher::findOrFail($id)->delete();
        session()->flash('message', 'Butcher deleted successfully.');
        $this->loadButchers();
    }

}; ?>

<div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Posts</h1>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="mb-6">
        <input type="text" wire:model="name" placeholder="Title" class="border p-2 w-full mb-2 rounded">
        {{-- <input type="text" wire:model="phone" placeholder="Title" class="border p-2 w-full mb-2 rounded"> --}}

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                {{ $isEdit ? 'Update' : 'Save' }}
            </button>
            @if ($isEdit)
                <button type="button" wire:click="resetForm" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
            @endif
        </div>
    </form>

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">Title</th>
                {{-- <th class="p-2 border">phone</th> --}}
                <th class="p-2 border w-32">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($butchers as $butcher)
                <tr>
                    <td class="border p-2">{{ $butcher->name }}</td>
                    {{-- <td class="border p-2">{{ $butcher->phone }}</td> --}}
                    <td class="border p-2 text-center">
                        <button wire:click="edit({{ $butcher->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $butcher->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
