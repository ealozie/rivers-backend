<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // $this->call([VehicleManufacturerSeeder::class, VehicleModelSeeder::class, VehicleCategorySeeder::class, TicketCategorySeeder::class, RoleSeeder::class, UserSeeder::class]);
        // $this->call([TicketCategorySeeder::class]);
        $this->call([
            // AppSettingSeeder::class, RevenueTypeSeeder::class, NationalitySeeder::class, TitleSeeder::class, StateSeeder::class, BusinessLevelSeeder::class,
            // ClassificationSeeder::class, BusinessTypeSeeder::class,
            // DemandNoticeCategorySeeder::class,
            // LocalGovernmentAreaSeeder::class,
            // OccupationSeeder::class,
            // PropertyCategorySeeder::class,
            // SettlementTypeSeeder::class,
            // MarketNameSeeder::class,
            // AssessmentYearSeeder::class,
            // BusinessCategorySeeder::class,
            // BusinessSubCategorySeeder::class,
            // MaritalStatusSeeder::class,
            // BloodGroupSeeder::class,
            // GenoTypeSeeder::class,
            // RoleSeeder::class,
        ]);
        $this->call([
            // PropertyUseSeeder::class,
            // PropertyTypeSeeder::class,
            // AgencySeeder::class,
            // UserUniqueIdSeeder::class,
            // RevenueItemSeeder::class,
            //  AppSettingSeeder::class,
            RoleSeeder::class,
            // DocumentLifeSpanSeeder::class,
            // TollGateCategorySeeder::class,
            //PermissionSeeder::class
        ]);
    }
}
