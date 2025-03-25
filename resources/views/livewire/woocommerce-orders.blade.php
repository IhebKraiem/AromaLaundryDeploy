<div>
    <h1>Commandes WooCommerce</h1>

    <!-- Indicateur de chargement -->
    @if ($loading)
        <p>Chargement en cours...</p>
    @endif

    <!-- Bouton pour charger les commandes -->
    <button wire:click="showWooCommerceOrders">Charger les Commandes</button>

    <!-- Afficher les commandes -->
    <div>
        @if (session('error'))
            <p style="color: red;">{{ session('error') }}</p>
        @elseif (session('warning'))
            <p style="color: orange;">{{ session('warning') }}</p>
        @elseif (!empty($orders))
            <ul>
                @foreach ($orders as $order)
                    <li>
                        <!-- Afficher uniquement l'ID de la commande -->
                        {{ $order->order_id ?? 'Inconnu' }}
                    </li>
                @endforeach
            </ul>
        @else
            <p>Aucune commande disponible.</p>
        @endif
    </div>
</div>