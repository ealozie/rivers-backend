<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountManagerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'manager' => new UserResource($this->manager),
            'entity_type' => $this->accountable_type,
            'entity' => $this->accountable,
            'entity_bills' => self::entity_collection($this->accountable_type, $this->accountable_id),
            'created_at' => $this->created_at,
        ];
    }

    private static function entity_collection($entity_type, $entity_id)
    {
        $total_paid = Assessment::selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount')
                    ->where('assessmentable_type', $entity_type)
                    ->where('assessmentable_id', $entity_id)
                    //->whereYear('created_at', $current_year)
                    ->where('payment_status', 'paid')
                    ->first();
        $total_unpaid = Assessment::selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount')
                    ->where('assessmentable_type', $entity_type)
                    ->where('assessmentable_id', $entity_id)
                    //->whereYear('created_at', $current_year)
                    ->where('payment_status', 'pending')
                    ->first();
        $total_approved = Assessment::selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount')
                    ->where('assessmentable_type', $entity_type)
                    ->where('assessmentable_id', $entity_id)
                    //->whereYear('created_at', $current_year)
                    ->where('status', 'approved')
                    ->first();
        $total_cancelled = Assessment::selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount')
                    ->where('assessmentable_type', $entity_type)
                    ->where('assessmentable_id', $entity_id)
                    //->whereYear('created_at', $current_year)
                    ->where('status', 'cancelled')
                    ->first();
        $total_assessments = Assessment::selectRaw('COUNT(*) as total_count, SUM(amount) as total_amount')
            ->where('assessmentable_type', $entity_type)
            ->where('assessmentable_id', $entity_id)
            //->whereYear('created_at', $current_year)
            ->first();
        return [
                'approved_assessments' => [
                    'amount' => number_format($total_approved->total_amount, 2),
                    'count' => $total_approved->total_count,
                ],
                'cancelled_assessments' => [
                    'amount' => number_format($total_cancelled->total_amount, 2),
                    'count' => $total_cancelled->total_count,
                ],
                'paid_assessments' => [
                    'amount' => number_format($total_paid->total_amount, 2),
                    'count' => $total_paid->total_count,
                ],
                'unpaid_assessments' => [
                    'amount' => number_format($total_unpaid->total_amount, 2),
                    'count' => $total_unpaid->total_count,
                ],
                'total_assessments' => [
                    'amount' => number_format($total_assessments->total_amount, 2),
                    'count' => $total_assessments->total_count,
                ],
            ];
    }
}
