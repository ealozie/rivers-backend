<?php

namespace App\Imports;

use App\Models\Agency;
use App\Models\Assessment;
use App\Models\AssessmentYear;
use App\Models\RevenueItem;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BulkAssessmentWithoutIDImport implements ToCollection, WithStartRow
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            if ($item[0] && $item[1] && $item[2] && $item[3] && $item[4] && $item[5] && $item[6] && $item[7]) {
                $year = AssessmentYear::where('year', $item[7])->first();
                $agency = Agency::where('agency_code', $item[4])->first();
                $revenue_item = RevenueItem::where('revenue_code', $item[5])->first();
                $validatedData['status'] = 'pending';
                $validatedData['full_name'] = $item[0];
                $validatedData['phone_number'] = $item[1];
                $validatedData['contact_address'] = $item[3];
                $validatedData['email'] = $item[2];
                $validatedData['amount'] = $item[6];
                $validatedData['revenue_item_id'] = $revenue_item->id;
                $validatedData['agency_id'] = $agency->id;
                $validatedData['payment_status'] = 'pending';
                $validatedData['added_by'] = $this->user->id;
                $validatedData['assessment_year_id'] = $year->id;
                $validatedData['assessment_reference'] = date('Y') . '-' . rand(1111, 9999) . '-' . rand(1000, 9999);
                Assessment::create($validatedData);
            }
        }
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}
