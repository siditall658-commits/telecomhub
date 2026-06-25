<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Signaler une panne : {{ $service->type_service }} ({{ $service->numero_ligne }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form action="{{ route('incidents.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="telecom_service_id" value="{{ $service->id }}">

                    <div>
                        <x-input-label for="titre" value="Titre de l'incident (ex: Plus d'internet)" />
                        <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" required />
                    </div>

                    <div>
                        <x-input-label for="priorite" value="Niveau de Priorité" />
                        <select name="priorite" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                            <option value="Basse">Basse</option>
                            <option value="Moyenne" selected>Moyenne</option>
                            <option value="Haute">Haute (Urgent)</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="description" value="Détails du problème" />
                        <textarea name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" rows="4" required></textarea>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <x-primary-button class="bg-red-600 hover:bg-red-700">Ouvrir le ticket</x-primary-button>
                        <a href="{{ route('clients.show', $service->client_id) }}" class="text-gray-600 underline">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>