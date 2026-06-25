<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dossier de : {{ $client->nom }}
            </h2>
            <a href="{{ route('clients.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded shadow font-bold text-sm transition">
                ← Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Message de succès -->
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow">
                    {{ session('success') }}
                </div>
            @endif

            <!-- INFORMATION CLIENT -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Informations Générales</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 uppercase tracking-wider text-xs font-bold">Nom complet</span>
                        <span class="text-base font-semibold text-gray-800">{{ $client->nom }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 uppercase tracking-wider text-xs font-bold">Adresse Email</span>
                        <span class="text-base font-semibold text-gray-800">{{ $client->email }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 uppercase tracking-wider text-xs font-bold">Téléphone</span>
                        <span class="text-base font-semibold text-gray-800">{{ $client->telephone }}</span>
                    </div>
                </div>
            </div>

            <!-- LISTE DES SERVICES ET FORMULAIRE D'INCIDENT -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Services Souscrits & Signalement</h3>
                
                <div class="space-y-6">
                    @forelse($client->services as $service)
                        <div class="p-4 border rounded-lg {{ $service->statut === 'EN PANNE' ? 'border-red-200 bg-red-50/30' : 'border-gray-200 bg-gray-50/30' }} flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <span class="inline-block px-2 py-0.5 text-xs font-bold rounded uppercase tracking-wide {{ $service->type_service === 'Fibre' ? 'bg-purple-100 text-purple-800' : ($service->type_service === 'ADSL' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $service->type_service }}
                                </span>
                                <h4 class="text-base font-bold text-gray-800 mt-1">Identifiant Ligne : <span class="font-mono text-blue-600">{{ $service->numero_ligne }}</span></h4>
                                <p class="text-sm text-gray-500 mt-0.5">Statut actuel : 
                                    <span class="font-bold {{ $service->statut === 'ACTIF' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $service->statut }}
                                    </span>
                                </p>
                            </div>

                            <!-- FORMULAIRE DE SIGNALEMENT -->
                            <div class="w-full md:w-2/5 bg-white p-4 border border-gray-200 rounded-md shadow-sm">
                                @if($service->statut === 'ACTIF')
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-red-600 mb-3 flex items-center gap-1">
                                        ⚠️ Déclarer un dysfonctionnement
                                    </h5>
                                    <form action="{{ route('incidents.store') }}" method="POST" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="telecom_service_id" value="{{ $service->id }}">
                                        
                                        <div>
                                            <input type="text" name="titre" placeholder="Description courte (ex: coupure totale...)" required
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs py-1.5">
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <select name="priorite" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs py-1.5">
                                                    <option value="Basse">Priorité : Basse</option>
                                                    <option value="Moyenne" selected>Priorité : Moyenne</option>
                                                    <option value="Haute">Priorité : Haute</option>
                                                </select>
                                            </div>
                                            <div>
                                                <input type="text" name="technicien" placeholder="Technicien (ex: Sarah)" required
                                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-xs py-1.5">
                                            </div>
                                        </div>

                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-1.5 px-3 rounded shadow transition">
                                            Signaler & Assigner la panne
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center py-4 text-red-600 font-medium text-xs flex flex-col items-center justify-center gap-1">
                                        <span>❌ Ce service est actuellement marqué EN PANNE.</span>
                                        <span class="text-gray-500 font-normal">Un ticket d'intervention est déjà ouvert.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm italic py-2 text-center">Aucun abonnement télécom trouvé pour ce client.</p>
                    @endforelse
                </div>

                <!-- FORMULAIRE D'ASSIGNATION -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col items-center">
                    <form action="{{ route('services.storeForClient', $client->id) }}" method="POST" class="flex gap-2 items-center bg-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm w-full max-w-md justify-between">
                        @csrf
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-600">🔌 Ajouter un service :</label>
                        <div class="flex gap-2">
                            <select name="type_service" required class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs py-1.5">
                                <option value="Fibre">🚀 Fibre FTTH</option>
                                <option value="ADSL">☎️ ADSL / Cuivre</option>
                                <option value="Mobile">📱 Mobile 4G / 5G</option>
                            </select>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-1.5 rounded shadow transition" style="background-color: #2563eb !important;">
                                Attribuer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- HISTORIQUE DES TICKETS D'INCIDENTS -->
            <div class="p-6 bg-white shadow sm:rounded-lg overflow-hidden">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Historique des Tickets d'Intervention</h3>
                
                <table class="min-w-full table-auto text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs font-bold">
                            <th class="px-6 py-3 text-left">Service</th>
                            <th class="px-6 py-3 text-left">Problème signalé</th>
                            <th class="px-6 py-3 text-center">Gravité</th>
                            <th class="px-6 py-3 text-center">Intervenant</th>
                            <th class="px-6 py-3 text-center">Statut du ticket</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $incidents = \App\Models\Incident::whereIn('telecom_service_id', $client->services->pluck('id'))->latest()->get();
                        @endphp

                        @forelse($incidents as $incident)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $incident->service->type_service }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $incident->description }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full 
                                        {{ in_array($incident->priorite, ['Haute', 'Critique']) ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $incident->priorite }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-gray-700">
                                    👤 {{ $incident->technicien ?? 'Non assigné' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $incident->statut === 'Ouvert' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $incident->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($incident->statut === 'Ouvert')
                                        <!-- Formulaire corrigé avec @method('PATCH') ici -->
                                        <form action="{{ route('incidents.resolve', $incident->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold text-xs py-1 px-2 rounded shadow transition">
                                                ✅ RÉSOUDRE
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Clos</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                    Aucun historique de ticket d'incident disponible pour ce client.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>