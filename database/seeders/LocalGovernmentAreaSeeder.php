<?php

namespace Database\Seeders;

use App\Models\LocalGovernmentArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocalGovernmentAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //all Local Government Areas with state_id in Nigeria, Abia state id = 1
        $local_government_areas = [
            ['name' => 'Aba North', 'state_id' => 1],
            ['name' => 'Aba South', 'state_id' => 1],
            ['name' => 'Arochukwu', 'state_id' => 1],
            ['name' => 'Bende', 'state_id' => 1],
            ['name' => 'Ikwuano', 'state_id' => 1],
            ['name' => 'Isiala Ngwa North', 'state_id' => 1],
            ['name' => 'Isiala Ngwa South', 'state_id' => 1],
            ['name' => 'Isuikwuato', 'state_id' => 1],
            ['name' => 'Obi Ngwa', 'state_id' => 1],
            ['name' => 'Ohafia', 'state_id' => 1],
            ['name' => 'Osisioma Ngwa', 'state_id' => 1],
            ['name' => 'Ugwunagbo', 'state_id' => 1],
            ['name' => 'Ukwa East', 'state_id' => 1],
            ['name' => 'Ukwa West', 'state_id' => 1],
            ['name' => 'Umuahia North', 'state_id' => 1],
            ['name' => 'Umuahia South', 'state_id' => 1],
            ['name' => 'Umu Nneochi', 'state_id' => 1],
            ['name' => 'Demsa', 'state_id' => 2],
            ['name' => 'Fufore', 'state_id' => 2],
            ['name' => 'Ganye', 'state_id' => 2],
            ['name' => 'Girei', 'state_id' => 2],
            ['name' => 'Gombi', 'state_id' => 2],
            ['name' => 'Guyuk', 'state_id' => 2],
            ['name' => 'Hong', 'state_id' => 2],
            ['name' => 'Jada', 'state_id' => 2],
            ['name' => 'Lamurde', 'state_id' => 2],
            ['name' => 'Madagali', 'state_id' => 2],
            ['name' => 'Maiha', 'state_id' => 2],
            ['name' => 'Mayo Belwa', 'state_id' => 2],
            ['name' => 'Michika', 'state_id' => 2],
            ['name' => 'Mubi North', 'state_id' => 2],
            ['name' => 'Mubi South', 'state_id' => 2],
            ['name' => 'Numan', 'state_id' => 2],
            ['name' => 'Shelleng', 'state_id' => 2],
            ['name' => 'Song', 'state_id' => 2],
            ['name' => 'Toungo', 'state_id' => 2],
            ['name' => 'Yola North', 'state_id' => 2],
            ['name' => 'Yola South', 'state_id' => 2],
            ['name' => 'Abak', 'state_id' => 3],
            ['name' => 'Eastern Obolo', 'state_id' => 3],
            ['name' => 'Eket', 'state_id' => 3],
            ['name' => 'Esit Eket', 'state_id' => 3],
            ['name' => 'Essien Udim', 'state_id' => 3],
            ['name' => 'Etim Ekpo', 'state_id' => 3],
            ['name' => 'Etinan', 'state_id' => 3],
            ['name' => 'Ibeno', 'state_id' => 3],
            ['name' => 'Ibesikpo Asutan', 'state_id' => 3],
            ['name' => 'Ibiono-Ibom', 'state_id' => 3],
            ['name' => 'Ika', 'state_id' => 3],
            ['name' => 'Ikono', 'state_id' => 3],
            ['name' => 'Ikot Abasi', 'state_id' => 3],
            ['name' => 'Ikot Ekpene', 'state_id' => 3],
            ['name' => 'Ini', 'state_id' => 3],
            ['name' => 'Itu', 'state_id' => 3],
            ['name' => 'Mbo', 'state_id' => 3],
            ['name' => 'Mkpat-Enin', 'state_id' => 3],
            ['name' => 'Nsit-Atai', 'state_id' => 3],
            ['name' => 'Nsit-Ibom', 'state_id' => 3],
            ['name' => 'Nsit-Ubium', 'state_id' => 3],
            ['name' => 'Obot Akara', 'state_id' => 3],
            ['name' => 'Okobo', 'state_id' => 3],
            ['name' => 'Onna', 'state_id' => 3],
            ['name' => 'Oron', 'state_id' => 3],
            ['name' => 'Oruk Anam', 'state_id' => 3],
            ['name' => 'Udung-Uko', 'state_id' => 3],
            ['name' => 'Ukanafun', 'state_id' => 3],
            ['name' => 'Uruan', 'state_id' => 3],
            ['name' => 'Urue-Offong/Oruko', 'state_id' => 3],
            ['name' => 'Uyo', 'state_id' => 3],
            ['name' => 'Aguata', 'state_id' => 4],
            ['name' => 'Anambra East', 'state_id' => 4],
            ['name' => 'Anambra West', 'state_id' => 4],
            ['name' => 'Anaocha', 'state_id' => 4],
            ['name' => 'Awka North', 'state_id' => 4],
            ['name' => 'Awka South', 'state_id' => 4],
            ['name' => 'Ayamelum', 'state_id' => 4],
            ['name' => 'Dunukofia', 'state_id' => 4],
            ['name' => 'Ekwusigo', 'state_id' => 4],
            ['name' => 'Idemili North', 'state_id' => 4],
            ['name' => 'Idemili South', 'state_id' => 4],
            ['name' => 'Ihiala', 'state_id' => 4],
        ];
        //truncate the local_government_areas table first
        LocalGovernmentArea::truncate();
        //loop through the local_government_areas array and create a record for each
        foreach ($local_government_areas as $local_government_area) {
            LocalGovernmentArea::create($local_government_area);
        }
    }
}
