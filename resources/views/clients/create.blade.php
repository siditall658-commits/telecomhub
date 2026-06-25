<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un nouveau Client Telecom') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('clients.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <x-input-label for="nom" value="Nom complet" />
                        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="email" value="Adresse Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="telephone" value="Numéro de Téléphone" />
                        <x-text-input id="telephone" name="telephone" type="text" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="adresse" value="Adresse physique" />
                        <textarea name="adresse" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"></textarea>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <x-primary-button>{{ __('Enregistrer le client') }}</x-primary-button>
                        <a href="{{ route('clients.index') }}" class="text-gray-600 hover:underline text-sm">Annuler</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>