<x-app-layout>
   <x-slot name="header">
    <div class="flex justify-between items-center w-full">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestion des Clients
        </h2>
        <a href="{{ route('clients.create') }}" 
           class="inline-block bg-blue-600 hover:bg-blue-700 rounded px-4 py-2 text-sm font-bold shadow transition text-white"
           style="background-color: #2563eb !important; color: #ffffff !important;">
            + Nouveau Client
        </a>
    </div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Clients</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalClients }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-50 text-blue-600">👥</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Lignes Actives</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalServicesActifs }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-50 text-green-600">⚡</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Tickets Ouverts</p>
                        <p class="text-3xl font-bold text-red-600 mt-1">{{ $totalTicketsOuverts }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-50 text-red-600">⚠️</div>
                </div>
            </div>

            <div class="p-6 bg-white shadow sm:rounded-lg">
                <form action="{{ route('clients.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher un client</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Nom, prénom ou email..." 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>

                    <div class="flex items-center h-10 mb-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="en_panne" value="1" {{ request('en_panne') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm font-semibold text-red-600">⚠️ En panne uniquement</span>
                        </label>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded font-bold text-sm h-10 transition">
                            Filtrer
                        </button>
                        @if(request()->has('search') || request()->has('en_panne'))
                            <a href="{{ route('clients.index') }}" class="flex-1 md:flex-none bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded font-bold text-sm h-10 transition inline-flex items-center justify-center">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full table-auto text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs font-bold">
                            <th class="px-6 py-3 text-left">Nom du Client</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-center">Services souscrits</th>
                            <th class="px-6 py-3 text-center">État global</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($clients as $client)
                            @php
                                $hasCriticalIncident = false;
                                foreach($client->services as $service) {
                                    foreach($service->incidents as $incident) {
                                        if($incident->statut === 'Ouvert' && in_array(strtolower($incident->priorite), ['haute', 'critique', 'high'])) {
                                            $hasCriticalIncident = true;
                                        }
                                    }
                                }
                                $isEnPanne = $client->services()->where('statut', 'EN PANNE')->exists();
                            @endphp
                            <tr class="hover:bg-gray-50 transition {{ $hasCriticalIncident ? 'bg-red-50/50' : '' }}">
                                <td class="px-6 py-4 font-semibold text-gray-900 flex items-center gap-2">
                                    {{ $client->nom }}
                                    @if($hasCriticalIncident)
                                        <span class="flex h-2 w-2 relative">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $client->email }}</td>
                                <td class="px-6 py-4 text-center font-mono font-bold">{{ $client->services_count }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($hasCriticalIncident)
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-600 text-white animate-pulse">
                                            🚨 URGENCE CRITIQUE
                                        </span>
                                    @elseif($isEnPanne)
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            ⚠️ Incident en cours
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ Opérationnel
                                        </span>
                                    @endif
                                </td>
                               <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
    <a href="{{ route('clients.show', $client->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-900 underline uppercase tracking-wider transition">
        Voir le dossier →
    </a>

    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" 
          onsubmit="return confirm('⚠️ ATTENTION : Êtes-vous sûr de vouloir supprimer définitivement ce client ? Cela effacera toutes ses lignes réseau et son historique de pannes.');" 
          class="inline-block">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition" title="Supprimer le client">
            🗑️ <span class="text-xs font-bold uppercase underline">Supprimer</span>
        </button>
    </form>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    Aucun client ne correspond à votre recherche.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700 mb-4 flex items-center gap-2">
                    📊 État de santé des infrastructures réseau
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-lg border {{ $pannesFibre > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }} flex flex-col justify-between">
                        <span class="text-xs font-bold uppercase tracking-wide text-gray-500">Réseau Fibre FTTH</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl font-black {{ $pannesFibre > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $pannesFibre }}</span>
                            <span class="text-xs font-semibold text-gray-600">Panne(s) en cours</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg border {{ $pannesADSL > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }} flex flex-col justify-between">
                        <span class="text-xs font-bold uppercase tracking-wide text-gray-500">Réseau ADSL / Cuivre</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl font-black {{ $pannesADSL > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $pannesADSL }}</span>
                            <span class="text-xs font-semibold text-gray-600">Panne(s) en cours</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg border {{ $pannesMobile > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }} flex flex-col justify-between">
                        <span class="text-xs font-bold uppercase tracking-wide text-gray-500">Réseau Mobile 4G / 5G</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl font-black {{ $pannesMobile > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $pannesMobile }}</span>
                            <span class="text-xs font-semibold text-gray-600">Panne(s) en cours</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-lg shadow-lg" style="background-color: #111827 !important; color: #ffffff !important; border: 1px solid #374151;">
                <h3 class="text-sm font-bold uppercase tracking-wider mb-4 flex items-center gap-2" style="color: #9ca3af !important;">
                    🖥️ Journal d'activité de l'infrastructure
                </h3>
                <div class="space-y-2 font-mono text-xs max-h-60 overflow-y-auto">
                    @forelse($logs as $log)
                        <div class="flex items-start gap-2 p-2 rounded transition" style="border-bottom: 1px solid #1f2937;">
                            <span style="color: #6b7280 !important; font-weight: bold; shrink: 0;">[{{ $log->created_at->format('d/m/Y H:i:s') }}]</span>
                            
                            @if($log->type === 'INCIDENT')
                                <span style="color: #fbbf24 !important; font-weight: bold;">{{ $log->description }}</span>
                            @else
                                <span style="color: #34d399 !important; font-weight: bold;">{{ $log->description }}</span>
                            @endif
                        </div>
                    @empty
                        <p style="color: #6b7280 !important; font-style: italic; padding: 8px 0;">Aucune activité enregistrée pour le moment.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>