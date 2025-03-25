<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportOrderDetailsFromJson extends Command
{
    protected $signature = 'import:order-details';
    protected $description = 'Import order details from JSON file into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = storage_path('app/orders.json');

        if (!file_exists($filePath)) {
            $this->error('JSON file not found.');
            return;
        }

        // Read and decode the JSON file
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format: ' . json_last_error_msg());
            return;
        }

        // Ensure the JSON structure contains entries
        if (empty($data['Sheet1'])) {
            $this->error('JSON file does not contain any data under "Sheet1".');
            return;
        }

        // Define service mappings
        $serviceMappings = [
            'Laundry & Ironing' => 1,
            'Dry Cleaning' => 2,
            'Shoe Revival' => 3,
            'Carpet Rejuvenation' => 4,
            'Tent Refresh' => 5,
        ];

        foreach ($data['Sheet1'] as $order) {
            try {
                // Validate required fields
                if (empty($order['ID']) || empty($order['Service'])) {
                    $this->error("Skipping order detail due to missing fields. Details: " . json_encode($order));
                    continue;
                }

                // Map service name to service ID
                $serviceId = $serviceMappings[$order['Service']] ?? null;
                if (!$serviceId) {
                    $this->error("Skipping order detail with unknown service. Service: {$order['Service']}, Order ID: {$order['ID']}");
                    continue;
                }

                // Assign default values for missing fields
                $servicePrice = $order['Service Price'] ?? 0.00; // Default price
                $serviceQuantity = $order['Service Quantity'] ?? 1; // Default quantity
                $serviceDetailTotal = $servicePrice * $serviceQuantity; // Calculate total

                // Insert into order_details table
                DB::table('order_details')->insert([
                    'order_id' => $order['ID'],
                    'service_id' => $serviceId,
                    'service_name' => $order['Service'] ?? null,
                    'service_price' => $servicePrice,
                    'service_quantity' => $serviceQuantity,
                    'service_detail_total' => $serviceDetailTotal,
                    'created_at' => Carbon::parse($order['Submission Create Date'])->format('Y-m-d H:i:s') ?? Carbon::now(),
                    'updated_at' => Carbon::parse($order['Submission Create Date'])->format('Y-m-d H:i:s') ?? Carbon::now(),
                ]);

                $this->info("Order detail #{$order['ID']} imported successfully.");
            } catch (\Exception $e) {
                // Log the error with the order ID if available
                $orderId = $order['ID'] ?? 'unknown';
                $this->error("Error importing order detail #$orderId: " . $e->getMessage() . ". Order details: " . json_encode($order));
            }
        }

        $this->info('All order details have been processed.');
    }
}