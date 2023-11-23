<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $default_settings = [
            'APP_NAME' => 'Ticketing System',
            'ALLOW_TICKET_VENDING' => true,
            'ALLOW_TICKET_BULK_VENDING' => true,
            'ALLOW_TICKET_ENFORCEMENT' => true,
            'ALLOW_WALLET_FUND_TRANSFER' => true,
            'ALLOW_TICKETING_ON_SATURDAY' => true,
            'ALLOW_TICKETING_ON_SUNDAY' => false,
            'APP_LOGO' => 'https://via.placeholder.com/150',
            "FORCE_ASSESSMENT_FIFO" => false,
            "DOCUMENT_VERIFICATION_FEES" => 1000,
            "PAYMENT_VERIFICATION_FEES" => 1000,
            "DOCUMENT_DOWNLOAD_TIMELINE" => 24,
            "DOCUMENT_DOWNLOAD_FEES" => 1000,
            "RECEIPT_TEMPLATE" => "https://via.placeholder.com/150",
            "TICKET_REVENUE_ITEM" => 5,
            "ORGANIZATION_NAME" => "ABIA State Government",
            "CONTACT_NUMBER" => "08012345678",
            "CONTACT_ADDRESS" => "No 1, Abia State Road, Umuahia",
            "CONTACT_EMAIL" => "admin@abiastategov.ng",
            "QT_MERCHANT_CODE" => '',
            "QT_PAY_ITEM_ID" => '',
            "QT_DATA_REF" => '',
            "QT_CLIENT_ID" => '',
            "QT_SECRET_KEY" => '',
            "QT_MERCHANT_ID" => '',
            "QT_ALIAS" => '',
            "QT_N_SECRET_KEY" => '',
            "VANSO_SENDER_ID" => 'QuickChop',
            "VANSO_USERNAME" => 'NG.105.0220',
            "VANSO_PASSWORD" => 'Axt0KWfC',
            "MONIFY_API_KEY" => 'MK_TEST_J1Q5YWHF4D',
            "MONIFY_SECRET_KEY" => '0QDZPWN21RR6GGH1P8KU9KXL1GYAXJ5Y',
            "MONIFY_BASE_URL" => 'https://sandbox.monnify.com',
            "MONIFY_WALLET_ACCOUNT" => '2789334947',
            "MONIFY_CONTRACT_CODE" => '3294886386',
            "MONIFY_MODE" => 'TEST',
            "ALLOW_LOCATION_TRACKING" => 'true',
        ];

        AppSetting::truncate();
        foreach ($default_settings as $key => $value) {
            AppSetting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
