<?php

namespace Database\Seeders;

use App\Enums\PetSex;
use App\Enums\PetSpecies;
use App\Models\AdoptionListing;
use App\Models\User;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Adoption listings posted by customers, spread over several cities for the map. Requires UserSeeder.
 */
class AdoptionListingSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->orderBy('id')->get();

        foreach ($this->listings() as $index => $data) {
            $owner = $customers[$index % $customers->count()];
            $species = $data['species'];

            AdoptionListing::factory()->for($owner)->create([
                'name' => $data['name'],
                'species' => $species,
                'breed' => $data['breed'],
                'sex' => $data['sex'],
                'age_months' => $data['age_months'],
                'description' => $data['description'],
                'is_vaccinated' => $data['vaccinated'],
                'is_neutered' => $data['neutered'],
                'photo_path' => PlaceholderImage::make(
                    'placeholders/adoption/'.Str::slug($data['name'].'-'.$data['city']).'.svg',
                    $species === PetSpecies::Dog ? '🐶' : '🐱',
                    $data['name'],
                    '#FCE7C8',
                ),
                'contact_name' => $owner->name,
                'contact_phone' => $owner->phone,
                'city' => $data['city'],
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'status' => $data['status'],
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function listings(): array
    {
        return [
            ['name' => 'Vàng', 'species' => PetSpecies::Dog, 'breed' => 'Chó ta', 'sex' => PetSex::Male, 'age_months' => 8, 'vaccinated' => true, 'neutered' => false, 'description' => 'Bé Vàng hiền lành, thích chơi với trẻ em, đã tiêm đủ mũi cơ bản.', 'city' => 'TP. Hồ Chí Minh', 'lat' => 10.8231, 'lng' => 106.6297, 'status' => 'available'],
            ['name' => 'Mực', 'species' => PetSpecies::Cat, 'breed' => 'Mèo ta', 'sex' => PetSex::Female, 'age_months' => 5, 'vaccinated' => true, 'neutered' => false, 'description' => 'Mèo con lông đen tuyền, quấn người, đã biết dùng khay cát.', 'city' => 'Hà Nội', 'lat' => 21.0285, 'lng' => 105.8542, 'status' => 'available'],
            ['name' => 'Bơ', 'species' => PetSpecies::Dog, 'breed' => 'Corgi lai', 'sex' => PetSex::Female, 'age_months' => 14, 'vaccinated' => true, 'neutered' => true, 'description' => 'Bé Bơ đã triệt sản, ngoan, quen sống trong căn hộ.', 'city' => 'Đà Nẵng', 'lat' => 16.0544, 'lng' => 108.2022, 'status' => 'available'],
            ['name' => 'Tom', 'species' => PetSpecies::Cat, 'breed' => 'Mèo Anh lông ngắn', 'sex' => PetSex::Male, 'age_months' => 24, 'vaccinated' => true, 'neutered' => true, 'description' => 'Tom điềm tĩnh, hợp với gia đình có người lớn tuổi.', 'city' => 'Cần Thơ', 'lat' => 10.0452, 'lng' => 105.7469, 'status' => 'pending'],
            ['name' => 'Cốm', 'species' => PetSpecies::Dog, 'breed' => 'Poodle', 'sex' => PetSex::Male, 'age_months' => 36, 'vaccinated' => true, 'neutered' => true, 'description' => 'Cốm đã tìm được gia đình mới, cảm ơn mọi người đã quan tâm.', 'city' => 'TP. Hồ Chí Minh', 'lat' => 10.7769, 'lng' => 106.7009, 'status' => 'adopted'],
            ['name' => 'Lucky', 'species' => PetSpecies::Cat, 'breed' => 'Mèo tam thể', 'sex' => PetSex::Female, 'age_months' => 10, 'vaccinated' => false, 'neutered' => false, 'description' => 'Lucky được cứu hộ từ ngoài đường, đang khỏe mạnh và rất thân thiện.', 'city' => 'Hà Nội', 'lat' => 21.0245, 'lng' => 105.8412, 'status' => 'available'],
        ];
    }
}
