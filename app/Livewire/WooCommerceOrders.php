<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component; // Assurez-vous d'inclure cette ligne

class WooCommerceOrders extends Component
{
    public $loading = false;

    public $orders = []; // Initialiser $orders comme un tableau vide

    public function render()
    {
        return view('livewire.woocommerce-orders', ['orders' => $this->orders]);
    }

    public function getWooCommerceOrders()
    {
        $wcDbConfig = [
            'driver' => 'mysql',
            'host' => '46.202.186.148',
            'port' => env('WC_DB_PORT', '3306'),
            'database' => env('WC_DB_DATABASE', 'u308997037_AromaLaundry'),
            'username' => env('WC_DB_USERNAME', 'u308997037_AromaLaundry'),
            'password' => env('WC_DB_PASSWORD', 'AromaLaundry@2023'),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => 'wp_',
        ];

        try {
            
            $wcConnection = DB::connection(DB::factory($wcDbConfig));

            $orders = $wcConnection->table('wp_posts')
                ->where('post_type', 'revision') // Filtrer uniquement les commandes WooCommerce
                ->select('ID as order_id', 'post_title', 'post_status', 'post_date', 'post_author') // Sélectionner les colonnes nécessaires
                ->orderBy('ID', 'DESC')
                ->get();
                Log::info('Commandes WooCommerce récupérées : ', ['orders' => $orders->toArray()]);


            foreach ($orders as &$order) {
                $order->order_data = @unserialize($order->order_data);
                if (!$order->order_data) {
                    $order->order_data = json_decode($order->order_data, true);
                }
            }

            return $orders;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function showWooCommerceOrders()
    {
        $this->loading = true; // Indiquer que le chargement a commencé

        $woocommerceOrders = $this->getWooCommerceOrders();
    
        if (isset($woocommerceOrders['error'])) {
            // Si une erreur s'est produite
            session()->flash('error', $woocommerceOrders['error']);
            $this->orders = [];
        } elseif (empty($woocommerceOrders)) {
            // Si aucune commande n'a été trouvée
            session()->flash('warning', 'Aucune commande WooCommerce disponible.');
            $this->orders = [];
        } else {
            // Stocker les commandes dans la propriété publique
            $this->orders = $woocommerceOrders;
        }
        $this->loading = false; // Indiquer que le chargement est terminé

    }
}