<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'Website',
            'Referral',
            'Social Media',
            'Email Campaign',
            'Phone Call',
            'Advertisement',
            'Cold Outreach',
            'Other',
        ];

        foreach ($sources as $source)
        {
            LeadSource::firstOrCreate([
                'name' => $source
            ]);
        }
    }
}
