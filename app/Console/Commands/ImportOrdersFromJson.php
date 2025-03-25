<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportOrdersFromJson extends Command
{
    protected $signature = 'import:orders';
    protected $description = 'Import orders from JSON file into the database';

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

        foreach ($data['Sheet1'] as $order) {
            try {
                // Validate required fields
                if (empty($order['ID']) || empty($order['Name']) || empty($order['Phone Number']) || empty($order['Service']) || empty($order['Pick-Up Date']) || empty($order['Pick-Up Time'])) {
                    $this->error("Skipping order due to missing fields. Details: " . json_encode($order));
                    continue;
                }

                // Convert pick-up date to YYYY-MM-DD format
                $pickUpDate = $this->convertToDate($order['Pick-Up Date']);

                // Convert pick-up time to HH:MM:SS format (24-hour)
                $pickUpTime = $this->convertToTime($order['Pick-Up Time']);

                // Generate order_number in the format ORD-XXXX
                $orderNumber = 'ORD-' . str_pad($order['ID'], 4, '0', STR_PAD_LEFT);

                // Map service to order type
                $orderType = $this->mapServiceToOrderType($order['Service']);

                DB::table('orders')->insert([
                    'id' => $order['ID'],
                    'order_number' => $orderNumber,
                    'customer_id' => null, // Always NULL
                    'customer_name' => $order['Name'] ?? 'Unknown Customer',
                    'phone_number' => $order['Phone Number'] ?? '',
                    'address' => $order['Address'] ?? '',
                    'order_date' => Carbon::parse($order['Submission Create Date'])->format('Y-m-d H:i:s') ?? null,
                    'delivery_date' => $pickUpDate,
                    'delivery_time' => $pickUpTime,
                    'sub_total' => 0, // Default value
                    'addon_total' => 0, // Default value
                    'discount' => 0, // Default value
                    'tax_percentage' => 0, // Default value
                    'tax_amount' => 0, // Default value
                    'total' => 0, // Default value
                    'note' => null, // Always NULL
                    'status' => 1, // Active status
                    'order_type' => $orderType,
                    'created_by' => 1, // Default value
                    'financial_year_id' => 1, // Default value
                    'order_source' => 'website', // Always "website"
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $this->info("Order #{$order['ID']} imported successfully.");
            } catch (\Exception $e) {
                // Log the error with the order ID if available
                $orderId = $order['ID'] ?? 'unknown';
                $this->error("Error importing order #$orderId: " . $e->getMessage() . ". Order details: " . json_encode($order));
            }
        }

        $this->info('All orders have been processed.');
    }

    /**
     * Convert date from DD/MM/YYYY to YYYY-MM-DD format.
     *
     * @param string|null $date
     * @return string|null
     */
    private function convertToDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Split the date into components
        $parts = explode('/', $date);
        if (count($parts) !== 3) {
            return null; // Invalid format
        }

        // Reformat to YYYY-MM-DD
        return $parts[2] . '-' . str_pad($parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($parts[0], 2, '0', STR_PAD_LEFT);
    }

    /**
     * Convert time from 12-hour AM/PM format to 24-hour HH:MM:SS format.
     *
     * @param string|null $time
     * @return string|null
     */
    private function convertToTime($time)
    {
        if (empty($time)) {
            return null;
        }

        // Parse the 12-hour time format and convert to 24-hour format
        try {
            $convertedTime = Carbon::createFromFormat('h:i A', $time)->format('H:i:s');
            return $convertedTime;
        } catch (\Exception $e) {
            return null; // Invalid time format
        }
    }

    /**
     * Map service to order type.
     *
     * @param string|null $service
     * @return int|null
     */
    private function mapServiceToOrderType($service)
    {
        if (empty($service)) {
            return null;
        }

        // Define mappings for services to order types
        $serviceMappings = [
            'Laundry & Ironing' => 1,
            'Dry Cleaning' => 2,
            'Shoe Revival' => 3,
            'Carpet Rejuvenation' => 4,
            'Tent Refresh' => 5,
        ];

        // Return the mapped order type or default to NULL if service is unknown
        return $serviceMappings[$service] ?? null;
    }
}