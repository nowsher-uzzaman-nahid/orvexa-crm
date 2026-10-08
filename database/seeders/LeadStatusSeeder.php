<?php

namespace Database\Seeders;

use App\Models\LeadStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'New',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Contacted',
                'color' => '#F59E0B',
                'sort_order' => 2,
            ],
            [
                'name' => 'Qualified',
                'color' => '#10B981',
                'sort_order' => 3,
            ],
            [
                'name' => 'Disqualified',
                'color' => '#EF4444',
                'sort_order' => 4,
            ],
            [
                'name' => 'Converted',
                'color' => '#8B5CF6',
                'sort_order' => 5,
            ]
        ];

        foreach ($statuses as $status)
        {
            LeadStatus::firstOrCreate(
                ['name' => $status['name']], $status
            );
        }
    }
}
