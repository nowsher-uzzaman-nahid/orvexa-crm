<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Support\Facades\DB;

class LeadConversionService
{
    public function convert(Lead $lead): Customer
    {
        return DB::transaction(function () use ($lead) {

            if ($lead->converted_at !== null) {
                throw new \RuntimeException('This lead has already been converted.');
            }

            // Company
            $company = null;

            if ($lead->company_name) {
                $company = Company::query()
                    ->whereRaw('LOWER(TRIM(name)) = ?', [
                        strtolower(trim($lead->company_name))
                    ])
                    ->first();

                if (! $company) {
                    $company = Company::create([
                        'name' => trim($lead->company_name)
                    ]);
                }
            }

            // Contact
            $email = $lead->email ? strtolower(trim($lead->email)) : null;

            $contact = null;

            if ($email) {
                $contact = Contact::query()
                    ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                    ->first();

                if (! $contact) {
                    $contact = Contact::create([
                        'company_id' => $company?->id,
                        'first_name' => $lead->first_name,
                        'last_name' => $lead->last_name,
                        'email' => $lead->email,
                        'phone' => $lead->phone,
                        'job_title' => $lead->job_title,
                        'notes' => $lead->notes,
                    ]);
                }
            }

            // Customer
            $customer = $contact->customer;

            if (! $customer) {
                $customer = Customer::create([
                    'contact_id' => $contact->id,
                    // 'customer_code' => 'TEMP-' . uniqid(),
                    'status' => 'active',
                    'became_customer_at' => now(),
                    'notes' => $lead->notes,
                ]);

                $customer->update([
                    'customer_code' => 'CUS-' . str_pad(
                        (string) $customer->id,
                        6,
                        '0',
                        STR_PAD_LEFT
                    ),
                ]);
            }

            $conversionStatus = LeadStatus::query()
                ->where('name', 'Converted')
                ->firstOrFail();

            $lead->update([
                'customer_id' => $customer->id,
                'lead_status_id' => $conversionStatus->id,
                'converted_at' => now(),
            ]);

            return $customer;

        });
    }
}