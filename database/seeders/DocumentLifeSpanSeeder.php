<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentLifeSpanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $document_life_spans = ['Monthly', 'Quarterly', 'Annually', 'Weekly', 'Custom',];
        \App\Models\DocumentLifeSpan::truncate();
        foreach ($document_life_spans as $document_life_span) {
            \App\Models\DocumentLifeSpan::create([
                'name' => $document_life_span,
            ]);
        }
    }
}
