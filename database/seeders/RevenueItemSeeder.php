<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RevenueItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $revenue_items = ['Direct Assessment Tax (Current)', 'Pay As You Earn (PAYE) - Companies', 'Administrative Fees', 'Plot Development Charge', 'Probate Fees', 'Annual Supervision Fees (Current)', 'Annual Supervision Fees (Arreas)', 'Livestock Farm Site Inspection Fees', 'Meat Inspection Fees', 'Environmental Audit/Impact Assessment', 'Nursing/Midwifery Exams Fees.', 'Hostel Fees for Accomodation of Trainees Nurses', 'Search Fees', 'Fees for Plans Deposited by Licenced Surveyors', 'Renewal of Business Premises', 'Haulage Fees', 'Driving Fees', 'Daily TolI Ticket', 'Driving /Eye Test Fees'];
        $revenue_codes = ['12010002', '12010007', '12040090', '12040181', '12040283', '12040218', '12040219', '12040112', '12040113', '12040031', '12040201', '12040202', '12040158', '12040159', '12040127', '12040130', '12040135', '12040136', '12040137'];
        $agencies = \App\Models\Agency::all();
        \App\Models\RevenueItem::truncate();
        foreach ($agencies as $agency) {
            foreach ($revenue_items as $key => $revenue_item) {
                \App\Models\RevenueItem::create(['revenue_name' => $revenue_item, 'agency_id' => $agency->id, 'revenue_code' => $revenue_codes[$key], 'added_by' => 1, 'revenue_type_id' => mt_rand(1, 4), 'unique_code' => mt_rand(10000000, 99999999), 'fixed_fee' => mt_rand(1000, 100000)]);
            }
        }
    }
}
