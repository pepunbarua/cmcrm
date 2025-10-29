<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class LeadsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Lead([
            'vendor_id' => $row['vendor_id'] ?? null,
            'user_id' => auth()->id(),
            'client_name' => $row['client_name'],
            'client_email' => $row['client_email'] ?? null,
            'client_phone' => $row['client_phone'],
            'event_type' => $row['event_type'] ?? 'wedding',
            'event_date' => $row['event_date'] ? \Carbon\Carbon::parse($row['event_date']) : null,
            'budget_range' => $row['budget_range'] ?? null,
            'package_interest' => $row['package_interest'] ?? null,
            'notes' => $row['notes'] ?? null,
            'status' => $row['status'] ?? 'new',
        ]);
    }

    public function rules(): array
    {
        return [
            'vendor_id' => 'nullable|exists:vendors,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|unique:leads,client_email',
            'client_phone' => 'required|string|max:20',
            'event_type' => 'nullable|in:wedding,birthday,corporate,portrait,other',
            'event_date' => 'nullable|date',
            'budget_range' => 'nullable|string|max:255',
            'package_interest' => 'nullable|string|max:255',
            'status' => 'nullable|in:new,contacted,follow_up,qualified,converted,lost',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'client_email.unique' => 'A lead with this email already exists.',
        ];
    }
}
