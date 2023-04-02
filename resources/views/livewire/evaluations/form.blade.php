<div>
    <div class=" w-screen p-52 pt-10">
        <div class=" w-full p-6 bg-white border border-gray-200 rounded-lg shadow ">
            <h1 class=" text-center font-bold text-2xl ">Extension Activity Evaluation Form</h1>
            <form wire:submit.prevent="submit">
                {{ $this->form }}
                <x-filament::button type="submit" form="submit" class="w-full my-4">
                    {{ 'Submit' }}
                </x-filament::button>
                {{-- <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 mt-10">Submit</button> --}}
            </form>
        </div>

    </div>
</div>
