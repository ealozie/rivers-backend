<?php

namespace Database\Seeders;

use App\Models\Occupation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //list of all occupations
        $occupations = [
            'Accountant',
            'Actor',
            'Actuary',
            'Administrator',
            'Advertising',
            'Agricultural Engineer',
            'Agricultural Scientist',
            'Air Traffic Controller',
            'Airline Pilot',
            'Ambulance Officer',
            'Anaesthetist',
            'Analyst Programmer',
            'Archaeologist',
            'Architect',
            'Archivist',
            'Art Teacher (Private)',
            'Artistic Director',
            'Arts Administrator',
            'Audiologist',
            'Author',
            'Barrister',
            'Biochemist',
            'Biomedical Engineer',
            'Biotechnologist',
            'Boarding Kennel/Cattery Operator',
            'Boat Builder',
            'Bookkeeper',
            'Botanist',
            'Broadcasting (Other)',
            'Building Inspector',
            'Business Machine Mechanic',
            'Butcher or Smallgoods Maker',
            'Buyer',
            'Cabinetmaker',
            'Cafe or Restaurant Manager',
            'Call or Contact Centre Operator',
            'Caravan Park and Camping Ground Manager',
            'Cardiologist',
            'Cardiothoracic Surgeon',
            'Careers Counsellor',
            'Carpenter',
            'Cartographer',
            'Chef',
            'Chemical Engineer',
            'Chemist',
            'Chemistry Technician',
            'Chief Executive or Managing Director',
            'Child Care Centre Manager',
            'Chiropractor',
            'Civil Engineer',
            'Civil Engineering Draftsperson',
            'Clinical Haematologist',
            'Clinical Psychologist',
            'Commodities Trader',
            'Communications Operator',
            'Community Worker',
            'Company Secretary',
            'Complementary Health Therapist',
            'Computer Network Engineer',
            'Conference and Event Organiser',
            'Conservation Officer',
            'Conservator',
            'Construction Estimator',
            'Construction Project Manager',
            'Contract Administrator',
            'Cook',
            'Copywriter',
            'Corporate General Manager',
            'Corporate Services Manager',
            'Counsellors (nec)',
            'Crop Farmers (nec)',
            'Customer Service Manager',
            'Dairy Cattle Farmer',
            'Dance Teacher (Private)',
            'Dancer or Choreographer',
            'Database Administrator',
            'Deck Hand',
            'Dental Specialist',
            'Dentist',
            'Derm',
            'Developer Programmer',
            'Diagnostic and Interventional Radiologist',
            'Others'
        ];
        Occupation::truncate();
        foreach ($occupations as $occupation) {
            Occupation::create(['name' => $occupation]);
        }
    }
}
