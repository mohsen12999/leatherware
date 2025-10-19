<?php

use Livewire\Volt\Component;
use App\Models\Butcher;
use App\Models\Leather;

new class extends Component {
    //
    public $leathers;
    public $butchers;

    public $cow;
    public $sheep;
    public $goat;
    public $loading_date;
    public $butcherId;

    $this->leatherId = null;
    public $isEdit = false;

    
    public function mount()
    {
        $this->butchers = Butcher::all();
        $this->loadLeathers();
    }

    public function loadLeathers()
    {
        $this->leathers = Leather::with('leather')->latest()->get();
    }

    public function resetForm()
    {
        $this->cow = '';
        $this->sheep = '';
        $this->goat = '';
        // $this->loading = '';
        // $this->loading_date = '';
        $this->butcherId = '';
        
        $this->leatherId = null;
        $this->isEdit = false;
    }

    public function validateData()
    {
        $data = [
            'cow' => $this->cow == ""? null:$this->cow,
            'sheep' => $this->sheep == ""? null:$this->sheep,
            'goat' => $this->goat == ""? null:$this->goat,
            // 'loading' => $this->loading,
            'loading_date' => $this->loading_date,
            'butcherId' => $this->butcherId,
        ];

        validator($data, [
            'butcher_id' => 'required|exists:butchers,id',
            'loading_date' => 'required|string|max:255',

            'cow' => [
                'nullable', 'cow',
                function ($attribute, $value, $fail) use ($data) {
                    // If cow is filled, others must be null
                    if (!empty($value) && (!empty($data['sheep']) || !empty($data['goat']))) {
                        $fail('If cow is provided, sheep and goat must be empty.');
                    }
                },
            ],
            'sheep' => [
                'nullable', 'string',
                function ($attribute, $value, $fail) use ($data) {
                    // If sheep or goat is filled, cow must be null
                    if (!empty($value) && !empty($data['cow'])) {
                        $fail('If sheep is provided, cow must be empty.');
                    }
                },
            ],
            'goat' => [
                'nullable', 'string',
                function ($attribute, $value, $fail) use ($data) {
                    if (!empty($value) && !empty($data['cow'])) {
                        $fail('If goat is provided, cow must be empty.');
                    }
                },
            ],
        ])->validate();

        return $data;
    }

    public function save()
    {
        $leather = $this->validateData();

        Leather::create($leather);

        session()->flash('message', 'leather created successfully.');
        $this->resetForm();
        $this->loadLeathers();
    }

    public function edit($id)
    {
        $leather = Leather::findOrFail($id);

        $this->cow = $leather->cow;
        $this->sheep = $leather->sheep;
        $this->goat = $leather->goat;
        // $this->loading = $leather->loading;
        $this->loading_date = $leather->loading_date;
        $this->butcherId = $leather->butcherId;

        $this->leatherId = $leather->id;
        $this->isEdit = true;
    }

    public function update()
    {
        $leather = $this->validateData();

        $record = Butcher::findOrFail($this->butcherId);
        $record->update($leather);

        session()->flash('message', 'Leather updated successfully.');
        $this->resetForm();
        $this->loadLeathers();
    }

    public function delete($id)
    {
        Leather::findOrFail($id)->delete();
        session()->flash('message', 'Leather deleted successfully.');
        $this->loadLeathers();
    }

}; ?>

<div>
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="mb-6">
        <input type="text" wire:model="loading_date" placeholder="14040404" class="border p-2 w-full mb-2 rounded">


        <select wire:model="butcherId" class="border p-2 w-full mb-2 rounded">
            <option value="">-- Select Butcher --</option>
            @foreach ($butchers as $butcher)
                <option value="{{ $butcher->id }}">{{ $butcher->name }}</option>
            @endforeach
        </select>

        <input type="text" wire:model="cow" placeholder="گاو" class="border p-2 w-full mb-2 rounded">
        <input type="text" wire:model="sheep" placeholder="گوسفند" class="border p-2 w-full mb-2 rounded">
        <input type="text" wire:model="goat" placeholder="بز" class="border p-2 w-full mb-2 rounded">


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
                <th class="p-2 border">Loading date</th>
                <th class="p-2 border">Butcher</th>
                <th class="p-2 border">Cow</th>
                <th class="p-2 border">Sheep</th>
                <th class="p-2 border">Goat</th>
                <th class="p-2 border">Load</th>
                <th class="p-2 border w-32">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
                <tr>
                    <td class="border p-2">{{ $post->title }}</td>
                    <td class="border p-2">{{ $post->butcher->name ?? '—' }}</td>
                    <td class="border p-2">{{ $post->cow }}</td>
                    <td class="border p-2">{{ $post->sheep }}</td>
                    <td class="border p-2">{{ $post->goat }}</td>
                    <td class="border p-2">{{ ($post->cow != null)?$post->cow:($post->sheep."++".$post->goat) }}</td>
                    <td class="border p-2 text-center">
                        <button wire:click="edit({{ $post->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $post->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
