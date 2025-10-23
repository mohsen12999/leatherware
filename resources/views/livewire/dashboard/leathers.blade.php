<?php

use Illuminate\Validation\ValidationException;
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
    public $loading_date="14040402";
    public $butcher_id;

    public $leatherId = null;
    public $isEdit = false;

    
    public function mount()
    {
        $this->butchers = Butcher::all();
        $this->loadLeathers();
    }

    public function loadLeathers()
    {
        $this->leathers = Leather::where('loading_date', $this->loading_date)->with('butcher')->latest()->get();
    }

    public function resetForm()
    {
        $this->cow = '';
        $this->sheep = '';
        $this->goat = '';
        // $this->loading = '';
        // $this->loading_date = '';
        $this->butcher_id = '';
        
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
            'butcher_id' => $this->butcher_id,
        ];

         if (empty($data['cow']) && empty($data['sheep']) && empty($data['goat'])) {
            throw ValidationException::withMessages([
                'cow' => 'You must provide at least one contact method (cow, sheep, or goat).',
            ]);
        }

        validator($data, [
            'butcher_id' => 'required|exists:butchers,id',
            'loading_date' => 'required|string|max:255',

            'cow' => [
                'nullable', 'string',
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
        $this->butcher_id = $leather->butcher_id;

        $this->leatherId = $leather->id;
        $this->isEdit = true;
    }

    public function update()
    {
        $leather = $this->validateData();

        $record = Leather::findOrFail($this->leatherId);
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

<div class="p-3 max-w-3xl mx-auto flex gap-4">
  <div class="flex-[2]">
    
    <h1 class="text-2xl font-bold mb-4">Leather</h1>

    @if (session()->has('message'))

        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('message') }}
        </div>

    @elseif ($errors->any())

        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        
    @endif

    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}" class="mb-6">
        <input type="text" wire:model="loading_date" placeholder="14040404" onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="border p-2 w-full mb-2 rounded">

        <select id="butcher_id" wire:model="butcher_id" class="border p-2 w-full mb-2 rounded">
            <option value="">-- انتخاب قصاب --</option>
            @foreach ($butchers as $butcher)
                <option value="{{ $butcher->id }}">{{ $butcher->name }}</option>
            @endforeach
        </select>

        <input type="text" wire:model="cow" placeholder="گاو" onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" class="border p-2 w-full mb-2 rounded">
        <input type="text" wire:model="sheep" placeholder="گوسفند" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');" class="border p-2 w-full mb-2 rounded">
        <input type="text" wire:model="goat" placeholder="بز" onkeyup="this.value = this.value.replace(/[^0-9]/g, '');" class="border p-2 w-full mb-2 rounded">

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
                <th class="p-2 border">id</th>
                <th class="p-2 border">Loading date</th>
                <th class="p-2 border">Butcher</th>
                {{-- <th class="p-2 border">Cow</th>
                <th class="p-2 border">Sheep</th>
                <th class="p-2 border">Goat</th> --}}
                <th class="p-2 border">Load</th>
                <th class="p-2 border w-32">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leathers as $leather)
                <tr>
                    <td class="border p-2">{{ $leather->id }}</td>
                    <td class="border p-2">{{ $leather->loading_date }}</td>
                    <td class="border p-2">{{ $leather->butcher->name ?? '—' }}</td>
                    {{-- <td class="border p-2">{{ $leather->cow }}</td>
                    <td class="border p-2">{{ $leather->sheep }}</td>
                    <td class="border p-2">{{ $leather->goat }}</td> --}}
                    <td class="border p-2">{{ ($leather->cow != null)?($leather->cow." kg"):($leather->sheep."++".$leather->goat) }}</td>
                    <td class="border p-2 text-center">
                        <button wire:click="edit({{ $leather->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
                        <button wire:click="delete({{ $leather->id }})" class="bg-red-500 text-white px-2 py-1 rounded">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

  </div>
  <div class="flex-[1]">
    <h1 class="text-2xl font-bold mb-4">Butchers</h1>
    <input 
        id="search" 
        class="p-3 mb-4 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400"
        placeholder="Search names..."
        autocomplete="off"
        onkeyup="filtering_the_list()"
         />


    <ul id="list" class="grid gap-2 max-h-72 overflow-auto">
        <!-- items injected by JS -->
        @foreach ($butchers as $butcher)
        <li 
            class="p-1 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer flex items-center justify-between"
            onclick="set_butcher({{ $butcher->id }})"
            >
                {{ $butcher->name }}
        </li>
        @endforeach
      </ul>

       <script>
            function filtering_the_list() {
                let butchers = @json($butchers);
                let filter = document.getElementById('search').value;
                let filtered_list = butchers.filter((ele)=>ele.name.includes(filter));

                const list= document.getElementById('list');
                list.innerHTML = ''
                filtered_list.forEach(ele => {
                    const li = document.createElement('li');
                    li.className = "p-1 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer flex items-center justify-between";
                    li.addEventListener('click', () => {set_butcher(ele.id)});
                    li.innerHTML = ele.name;

                    list.appendChild(li);
                });
            }

            function set_butcher(butcher_id) {
                Livewire.first().set('butcher_id', butcher_id);

                // $wire.set('butcher_id', butcher_id);

                // const el = document.getElementById('butcher_id');
                // el.value = butcher_id;
                // el.dispatchEvent(new Event('input', { bubbles: true })); // tells Livewire the value changed
            }
            
        </script>

  </div>
</div>

