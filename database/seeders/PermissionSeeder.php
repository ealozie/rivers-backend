<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //         Permission::create(['name' => 'create revenueitem', 'label'=>'Revenue item']);
        //         Permission::create(['name' => 'edit revenueitem', 'label'=>'Revenue item']);
        //         Permission::create(['name' => 'delete revenueitem', 'label'=>'Revenue item']);
        //         Permission::create(['name' => 'view revenueitem', 'label'=>'Revenue item']);
        //
        //         Permission::create(['name' => 'create revenuetype', 'label'=>'Revenue type']);
        //         Permission::create(['name' => 'edit revenuetype', 'label'=>'Revenue type']);
        //         Permission::create(['name' => 'delete revenuetype', 'label'=>'Revenue type']);
        //         Permission::create(['name' => 'view revenuetype', 'label'=>'Revenue type']);
        //
        //         Permission::create(['name' => 'create demandnotice', 'label'=>'Demand notice']);
        //         Permission::create(['name' => 'edit demandnotice', 'label'=>'Demand notice']);
        //         Permission::create(['name' => 'delete demandnotice', 'label'=>'Demand notice']);
        //         Permission::create(['name' => 'view demandnotice', 'label'=>'Demand notice']);
        //
        //         Permission::create(['name' => 'create signage', 'label'=>'Signage']);
        //         Permission::create(['name' => 'edit signage', 'label'=>'Signage']);
        //         Permission::create(['name' => 'delete signage', 'label'=>'Signage']);
        //         Permission::create(['name' => 'view signage', 'label'=>'Signage']);
        //
        //         Permission::create(['name' => 'create shop', 'label'=>'Shop']);
        //         Permission::create(['name' => 'edit shop', 'label'=>'Shop']);
        //         Permission::create(['name' => 'delete shop', 'label'=>'Shop']);
        //         Permission::create(['name' => 'view shop', 'label'=>'Shop']);
        //
        //         Permission::create(['name' => 'create property', 'label'=>'Property']);
        //         Permission::create(['name' => 'edit property', 'label'=>'Property']);
        //         Permission::create(['name' => 'delete property', 'label'=>'Property']);
        //         Permission::create(['name' => 'view property', 'label'=>'Property']);
        //
        //         Permission::create(['name' => 'create individual', 'label'=>'Individual']);
        //         Permission::create(['name' => 'edit individual', 'label'=>'Individual']);
        //         Permission::create(['name' => 'delete individual', 'label'=>'Individual']);
        //         Permission::create(['name' => 'view individual', 'label'=>'Individual']);
        //
        //
        //         Permission::create(['name' => 'create demandnoticecategory', 'label'=>'Demand Notice Category']);
        //         Permission::create(['name' => 'edit demandnoticecategory', 'label'=>'Demand Notice Category']);
        //         Permission::create(['name' => 'delete demandnoticecategory', 'label'=>'Demand Notice Category']);
        //         Permission::create(['name' => 'view demandnoticecategory', 'label'=>'Demand Notice Category']);
        //
        //         Permission::create(['name' => 'create demandnoticecategoryitem', 'label'=>'Demand Notice Category Item']);
        //         Permission::create(['name' => 'edit demandnoticecategoryitem', 'label'=>'Demand Notice Category Item']);
        //         Permission::create(['name' => 'delete demandnoticecategoryitem', 'label'=>'Demand Notice Category Item']);
        //         Permission::create(['name' => 'view demandnoticecategoryitem', 'label'=>'Demand Notice Category Item']);
        //
        //         Permission::create(['name' => 'create document', 'label'=>'Document']);
        //         Permission::create(['name' => 'edit document', 'label'=>'Document']);
        //         Permission::create(['name' => 'delete document', 'label'=>'Document']);
        //         Permission::create(['name' => 'view document', 'label'=>'Document']);
        //
        //         Permission::create(['name' => 'create cooperate', 'label'=>'Cooperate']);
        //         Permission::create(['name' => 'edit cooperate', 'label'=>'Cooperate']);
        //         Permission::create(['name' => 'delete cooperate', 'label'=>'Cooperate']);
        //         Permission::create(['name' => 'view cooperate', 'label'=>'Cooperate']);
        //
        //         Permission::create(['name' => 'create vehicle', 'label'=>'Vehicle']);
        //         Permission::create(['name' => 'edit vehicle', 'label'=>'Vehicle']);
        //         Permission::create(['name' => 'delete vehicle', 'label'=>'Vehicle']);
        //         Permission::create(['name' => 'view vehicle', 'label'=>'Vehicle']);
        //
        //         Permission::create(['name' => 'create user', 'label'=>'User']);
        //         Permission::create(['name' => 'edit user', 'label'=>'User']);
        //         Permission::create(['name' => 'delete user', 'label'=>'User']);
        //         Permission::create(['name' => 'view user', 'label'=>'User']);
        //
        //         Permission::create(['name' => 'create agency', 'label'=>'Agency']);
        //         Permission::create(['name' => 'edit agency', 'label'=>'Agency']);
        //         Permission::create(['name' => 'delete agency', 'label'=>'Agency']);
        //         Permission::create(['name' => 'view agency', 'label'=>'Agency']);
        //
        //         Permission::create(['name' => 'create assessment', 'label'=>'Assessment']);
        //         Permission::create(['name' => 'edit assessment', 'label'=>'Assessment']);
        //         Permission::create(['name' => 'delete assessment', 'label'=>'Assessment']);
        //         Permission::create(['name' => 'view assessment', 'label'=>'Assessment']);
        // Permission::create(['name' => 'create payment', 'label' => 'Payment']);
        // Permission::create(['name' => 'edit payment', 'label'=> 'Payment']);
        // Permission::create(['name' => 'delete payment', 'label'=> 'Payment']);
        // Permission::create(['name' => 'view payment', 'label'=> 'Payment']);

        // Permission::create(['name' => 'create ticket', 'label' => 'Ticket']);
        // Permission::create(['name' => 'edit ticket', 'label'=> 'Ticket']);
        // Permission::create(['name' => 'delete ticket', 'label'=> 'Ticket']);
        // Permission::create(['name' => 'view ticket', 'label'=> 'Ticket']);

        // Permission::create(['name' => 'create agent', 'label' => 'Agent']);
        // Permission::create(['name' => 'edit agent', 'label'=> 'Agent']);
        // Permission::create(['name' => 'delete agent', 'label'=> 'Agent']);
        // Permission::create(['name' => 'view agent', 'label'=> 'Agent']);

        // Permission::create(['name' => 'create enforcement', 'label' => 'Enforcement']);
        // Permission::create(['name' => 'edit enforcement', 'label'=> 'Enforcement']);
        // Permission::create(['name' => 'delete enforcement', 'label'=> 'Enforcement']);
        // Permission::create(['name' => 'view enforcement', 'label'=> 'Enforcement']);

        // Permission::create(['name' => 'create wallet', 'label' => 'Wallet']);
        // Permission::create(['name' => 'edit wallet', 'label'=> 'Wallet']);
        // Permission::create(['name' => 'delete wallet', 'label'=> 'Wallet']);
        // Permission::create(['name' => 'view wallet', 'label'=> 'Wallet']);

        // Permission::create(['name' => 'create ticketcategory', 'label' => 'Ticket Category']);
        // Permission::create(['name' => 'edit ticketcategory', 'label'=> 'Ticket Category']);
        // Permission::create(['name' => 'delete ticketcategory', 'label'=> 'Ticket Category']);
        // Permission::create(['name' => 'view ticketcategory', 'label'=> 'Ticket Category']);

        // Permission::create(['name' => 'create audittrail', 'label' => 'Audit Trail']);
        // Permission::create(['name' => 'edit audittrail', 'label'=> 'Audit Trail']);
        // Permission::create(['name' => 'delete audittrail', 'label'=> 'Audit Trail']);
        // Permission::create(['name' => 'view audittrail', 'label'=> 'Audit Trail']);

        //Permission::create(['name' => 'view dashboard', 'label' => 'Dashboard']);
        Permission::create(['name' => 'view settings', 'label' => 'Settings']);

        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $permission->update(['guard_name' => 'web']);
        }
    }
}
