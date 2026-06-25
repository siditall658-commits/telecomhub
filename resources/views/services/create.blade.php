<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Attribuer un service à {{ $client->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="client_id" value="{{ $client->id }}">

                    <div>
                        <x-input-label value="Type de Service" />
                        <select name="type_service" class="block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="Fibre">Fibre Optique</option>
                            <option value="ADSL">ADSL</option>
                            <option value="Mobile">Forfait Mobile</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Numéro de ligne / Identifiant" />
                        <x-text-input name="numero_ligne" type="text" class="block w-full" required />
                    </div>

                    <x-primary-button>Activer le service</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>