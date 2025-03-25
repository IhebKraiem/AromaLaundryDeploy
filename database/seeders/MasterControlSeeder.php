<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterSettings;

class MasterControlSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new MasterSettings();
        $site = $settings->siteData();
        $site['default_currency'] = '$';
        $site['default_application_name'] = 'Aroma Laundry';
        $site['default_phone_number'] = '70787197';
        $site['default_tax_percentage'] = '0';
        $site['default_state'] = 'Zone 43';
        $site['default_city'] = 'DOha';
        $site['default_country'] = 'QA';
        $site['default_zip_code'] = '';
        $site['default_address'] = 'Zone 43, Street 330, Building 22, Doha, Qatar';
        $site['store_email'] = 'info@aroma-laundry.com';
        $site['store_tax_number'] = '5001646041';
        $site['default_printer'] = '1';
        $site['forget_password_enable'] = 1;
        $site['country_code'] = +974;
        $site['default_currency_alignment'] = 1;
        $site['sms_createorder'] = 'Hi <name> An Order #<order_number> was created and will be delivered on <delivery_date> Your Order Total is <total>.';
        $site['sms_statuschange'] = 'Hi <name> Your Order #<order_number> status has been changed to <status> on <current_time>';
        foreach ($site as $key => $value) {
            MasterSettings::updateOrCreate(['master_title' => $key],['master_value' => $value]);
        }

        //run permission seeder
        $this->call(PermissionSeeder::class);
    }
}
