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

class BulkAssessmentImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            $user = User::where('unique_id', $item[0])->first();
            if ($user) {
                $year = AssessmentYear::where('year', $item[4])->first();
                $agency = Agency::where('agency_code', $item[1])->first();
                $revenue_item = RevenueItem::where('revenue_code', $item[2])->first();
                $validatedData['status'] = 'pending';
                $validatedData['full_name'] = $user->name;
                $validatedData['phone_number'] = $user->phone_number;
                $validatedData['email'] = $user->email;
                $validatedData['amount'] = $item[3];
                $validatedData['user_id'] = $user->id;
                $validatedData['revenue_item_id'] = $revenue_item->id;
                $validatedData['agency_id'] = $agency->id;
                $validatedData['payment_status'] = 'pending';
                $validatedData['added_by'] = $this->user->id;
                $validatedData['assessment_year_id'] = $year->id;
                $validatedData['assessment_reference'] = date('Y') . '-' . rand(1111, 9999) . '-' . rand(1000, 9999);
                $validatedData['entity_id'] = $user->unique_id;
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
