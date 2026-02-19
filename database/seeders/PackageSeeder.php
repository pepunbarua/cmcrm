<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageContent;
use App\Models\User;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@checkmate.com')->first() ?? User::first();
        $createdBy = $adminUser?->id;

        $contentDefinitions = [
            [
                'name' => 'Photography Coverage',
                'unit' => 'hour',
                'description' => 'On-site professional photography coverage.',
                'base_price' => 4000,
            ],
            [
                'name' => 'Cinematography Coverage',
                'unit' => 'hour',
                'description' => 'On-site cinematic video coverage.',
                'base_price' => 5000,
            ],
            [
                'name' => 'Drone Coverage',
                'unit' => 'hour',
                'description' => 'Aerial shots with licensed drone operator.',
                'base_price' => 6500,
            ],
            [
                'name' => 'Edited Photos',
                'unit' => 'piece',
                'description' => 'Color-corrected and retouched final photos.',
                'base_price' => 50,
            ],
            [
                'name' => 'Highlight Film',
                'unit' => 'minute',
                'description' => 'Cinematic highlight video.',
                'base_price' => 1200,
            ],
            [
                'name' => 'Photo Album',
                'unit' => 'book',
                'description' => 'Printed premium photo album.',
                'base_price' => 9000,
            ],
            [
                'name' => 'Raw Files Delivery',
                'unit' => 'event',
                'description' => 'Original raw image and video files handover.',
                'base_price' => 15000,
            ],
            [
                'name' => 'Express Delivery',
                'unit' => 'event',
                'description' => 'Priority delivery within shorter turnaround.',
                'base_price' => 7000,
            ],
        ];

        $contentMap = [];

        foreach ($contentDefinitions as $definition) {
            $content = PackageContent::updateOrCreate(
                ['name' => $definition['name']],
                [
                    'unit' => $definition['unit'],
                    'description' => $definition['description'],
                    'base_price' => $definition['base_price'],
                    'is_active' => true,
                    'created_by' => $createdBy,
                ]
            );

            $contentMap[$content->name] = $content;
        }

        $packageDefinitions = [
            [
                'name' => 'Wedding Basic',
                'code' => 'WED-BASIC',
                'description' => 'Suitable for compact wedding coverage.',
                'pricing_mode' => 'item_sum',
                'base_price' => 0,
                'items' => [
                    ['content' => 'Photography Coverage', 'qty' => 6, 'unit_price' => null, 'mandatory' => true, 'editable' => false],
                    ['content' => 'Edited Photos', 'qty' => 300, 'unit_price' => null, 'mandatory' => true, 'editable' => true],
                    ['content' => 'Highlight Film', 'qty' => 4, 'unit_price' => null, 'mandatory' => false, 'editable' => true],
                ],
            ],
            [
                'name' => 'Wedding Standard',
                'code' => 'WED-STD',
                'description' => 'Balanced package for most events.',
                'pricing_mode' => 'hybrid',
                'base_price' => 30000,
                'items' => [
                    ['content' => 'Photography Coverage', 'qty' => 8, 'unit_price' => null, 'mandatory' => true, 'editable' => false],
                    ['content' => 'Cinematography Coverage', 'qty' => 6, 'unit_price' => null, 'mandatory' => true, 'editable' => false],
                    ['content' => 'Edited Photos', 'qty' => 500, 'unit_price' => null, 'mandatory' => true, 'editable' => true],
                    ['content' => 'Photo Album', 'qty' => 1, 'unit_price' => null, 'mandatory' => false, 'editable' => true],
                ],
            ],
            [
                'name' => 'Wedding Premium',
                'code' => 'WED-PRM',
                'description' => 'Full-service premium coverage package.',
                'pricing_mode' => 'hybrid',
                'base_price' => 50000,
                'items' => [
                    ['content' => 'Photography Coverage', 'qty' => 10, 'unit_price' => null, 'mandatory' => true, 'editable' => false],
                    ['content' => 'Cinematography Coverage', 'qty' => 8, 'unit_price' => null, 'mandatory' => true, 'editable' => false],
                    ['content' => 'Drone Coverage', 'qty' => 2, 'unit_price' => null, 'mandatory' => false, 'editable' => true],
                    ['content' => 'Edited Photos', 'qty' => 800, 'unit_price' => null, 'mandatory' => true, 'editable' => true],
                    ['content' => 'Photo Album', 'qty' => 2, 'unit_price' => null, 'mandatory' => true, 'editable' => true],
                    ['content' => 'Raw Files Delivery', 'qty' => 1, 'unit_price' => null, 'mandatory' => false, 'editable' => true],
                ],
            ],
        ];

        foreach ($packageDefinitions as $definition) {
            $package = Package::updateOrCreate(
                ['code' => $definition['code']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'pricing_mode' => $definition['pricing_mode'],
                    'base_price' => $definition['base_price'],
                    'is_active' => true,
                    'created_by' => $createdBy,
                ]
            );

            $package->items()->delete();

            foreach ($definition['items'] as $index => $item) {
                $content = $contentMap[$item['content']] ?? null;
                if (!$content) {
                    continue;
                }

                $package->items()->create([
                    'package_content_id' => $content->id,
                    'content_name_snapshot' => $content->name,
                    'default_qty' => $item['qty'],
                    'default_unit_price' => $item['unit_price'],
                    'is_mandatory' => $item['mandatory'],
                    'is_editable' => $item['editable'],
                    'sort_order' => $index,
                ]);
            }
        }

        $this->command?->info('Packages and package contents seeded successfully.');
    }
}
