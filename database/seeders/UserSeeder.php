<?php

namespace Database\Seeders;

use App\Enums\HealthRecordType;
use App\Enums\LoyaltyReason;
use App\Enums\PetSex;
use App\Enums\PetSpecies;
use App\Models\Pet;
use App\Models\User;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 1 admin, 1 staff, 5 customers (each with pets, health records and loyalty points).
 * All demo accounts use the password "password".
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Quản Trị Viên',
            'email' => 'admin@petshop.test',
            'phone' => '0901000001',
        ]);

        User::factory()->staff()->create([
            'name' => 'Lê Hoàng Yến',
            'email' => 'staff@petshop.test',
            'phone' => '0901000002',
        ]);

        foreach ($this->customers() as $data) {
            $customer = User::factory()->withLoyaltyPoints($data['points'])->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            if ($data['points'] > 0) {
                $customer->loyaltyTransactions()->create([
                    'points' => $data['points'],
                    'balance_after' => $data['points'],
                    'reason' => LoyaltyReason::Earned,
                    'description' => 'Tích điểm từ các đơn hàng đã hoàn thành',
                ]);
            }

            foreach ($data['pets'] as $petData) {
                $this->createPet($customer, $petData);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createPet(User $owner, array $data): void
    {
        /** @var PetSpecies $species */
        $species = $data['species'];

        $pet = $owner->pets()->create([
            'name' => $data['name'],
            'species' => $species,
            'breed' => $data['breed'],
            'birthday' => now()->subMonths($data['age_months'])->toDateString(),
            'weight_kg' => $data['weight'],
            'sex' => $data['sex'],
            'allergies' => $data['allergies'],
            'notes' => $data['notes'] ?? null,
            'photo_path' => PlaceholderImage::make(
                'placeholders/pets/'.Str::slug($owner->name.' '.$data['name']).'.svg',
                $this->emojiFor($species),
                $data['name'],
                '#E3F0E1',
            ),
        ]);

        $this->createHealthRecords($pet, $data['vaccine_due_in_days']);
    }

    /**
     * Fish have no useful health book entries; everything else gets a realistic set.
     * $vaccineDueIn is relative to today and negative values produce an overdue vaccination.
     */
    private function createHealthRecords(Pet $pet, int $vaccineDueIn): void
    {
        if ($pet->species === PetSpecies::Fish) {
            return;
        }

        $records = [
            [
                'type' => HealthRecordType::Checkup,
                'record_date' => now()->subMonths(6)->toDateString(),
                'next_due_date' => now()->addMonths(6)->toDateString(),
                'note' => 'Sức khỏe tốt, tăng cân đều.',
            ],
            [
                'type' => HealthRecordType::Weight,
                'record_date' => now()->subMonth()->toDateString(),
                'weight_kg' => $pet->weight_kg,
                'note' => 'Cân định kỳ hằng tháng.',
            ],
        ];

        if (in_array($pet->species, [PetSpecies::Dog, PetSpecies::Cat], true)) {
            $records[] = [
                'type' => HealthRecordType::Vaccination,
                'record_date' => now()->addDays($vaccineDueIn)->subYear()->toDateString(),
                'next_due_date' => now()->addDays($vaccineDueIn)->toDateString(),
                'note' => 'Tiêm mũi nhắc lại hằng năm.',
            ];
            $records[] = [
                'type' => HealthRecordType::Deworming,
                'record_date' => now()->subMonths(2)->toDateString(),
                'next_due_date' => now()->addMonth()->toDateString(),
                'note' => 'Tẩy giun định kỳ 3 tháng một lần.',
            ];
        }

        foreach ($records as $record) {
            $pet->healthRecords()->create($record);
        }
    }

    private function emojiFor(PetSpecies $species): string
    {
        return match ($species) {
            PetSpecies::Dog => '🐶',
            PetSpecies::Cat => '🐱',
            PetSpecies::Bird => '🦜',
            PetSpecies::Fish => '🐟',
            PetSpecies::SmallPet => '🐹',
            PetSpecies::Other => '🐾',
        };
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function customers(): array
    {
        return [
            [
                'name' => 'Nguyễn Minh Anh',
                'email' => 'minhanh.nguyen@example.com',
                'phone' => '0912000001',
                'points' => 120,
                'pets' => [
                    ['name' => 'Bông', 'species' => PetSpecies::Dog, 'breed' => 'Poodle', 'age_months' => 36, 'weight' => 4.5, 'sex' => PetSex::Female, 'allergies' => ['gà'], 'notes' => 'Dị ứng thịt gà, da dễ nổi mẩn.', 'vaccine_due_in_days' => 5],
                    ['name' => 'Mít', 'species' => PetSpecies::Cat, 'breed' => 'Mèo Anh lông ngắn', 'age_months' => 24, 'weight' => 4.2, 'sex' => PetSex::Male, 'allergies' => [], 'vaccine_due_in_days' => 45],
                ],
            ],
            [
                'name' => 'Trần Quốc Bảo',
                'email' => 'quocbao.tran@example.com',
                'phone' => '0912000002',
                'points' => 450,
                'pets' => [
                    ['name' => 'Lu', 'species' => PetSpecies::Dog, 'breed' => 'Golden Retriever', 'age_months' => 60, 'weight' => 29.5, 'sex' => PetSex::Male, 'allergies' => ['bò', 'ngũ cốc'], 'notes' => 'Tránh thức ăn có bò và ngũ cốc.', 'vaccine_due_in_days' => 20],
                ],
            ],
            [
                'name' => 'Lê Thu Hà',
                'email' => 'thuha.le@example.com',
                'phone' => '0912000003',
                'points' => 0,
                'pets' => [
                    ['name' => 'Mun', 'species' => PetSpecies::Cat, 'breed' => 'Mèo ta', 'age_months' => 96, 'weight' => 4.8, 'sex' => PetSex::Female, 'allergies' => ['cá'], 'notes' => 'Mèo lớn tuổi, dị ứng cá.', 'vaccine_due_in_days' => -3],
                    ['name' => 'Kem', 'species' => PetSpecies::Dog, 'breed' => 'Corgi', 'age_months' => 21, 'weight' => 9.8, 'sex' => PetSex::Female, 'allergies' => [], 'vaccine_due_in_days' => 60],
                ],
            ],
            [
                'name' => 'Phạm Đức Huy',
                'email' => 'duchuy.pham@example.com',
                'phone' => '0912000004',
                'points' => 80,
                'pets' => [
                    ['name' => 'Sóc', 'species' => PetSpecies::Bird, 'breed' => 'Vẹt Cockatiel', 'age_months' => 24, 'weight' => 0.09, 'sex' => PetSex::Male, 'allergies' => [], 'vaccine_due_in_days' => 90],
                    ['name' => 'Nemo', 'species' => PetSpecies::Fish, 'breed' => 'Cá Betta', 'age_months' => 8, 'weight' => null, 'sex' => PetSex::Male, 'allergies' => [], 'vaccine_due_in_days' => 0],
                ],
            ],
            [
                'name' => 'Võ Phương Linh',
                'email' => 'phuonglinh.vo@example.com',
                'phone' => '0912000005',
                'points' => 260,
                'pets' => [
                    ['name' => 'Milo', 'species' => PetSpecies::Dog, 'breed' => 'Pug', 'age_months' => 6, 'weight' => 5.2, 'sex' => PetSex::Male, 'allergies' => ['gà', 'lúa mì'], 'notes' => 'Chó con, dị ứng gà và lúa mì.', 'vaccine_due_in_days' => 10],
                    ['name' => 'Gạo', 'species' => PetSpecies::SmallPet, 'breed' => 'Hamster Winter White', 'age_months' => 7, 'weight' => 0.04, 'sex' => PetSex::Female, 'allergies' => [], 'vaccine_due_in_days' => 0],
                ],
            ],
        ];
    }
}
