<?php

declare(strict_types=1);

use App\Filament\Center\Resources\Trainees\TraineeResource;
use App\Models\CertifiedCenter;
use App\Models\Trainee;
use App\Models\User;
use App\Policies\TraineePolicy;
use App\Services\Certification\CertificationImportService;
use Illuminate\Database\QueryException;

function traineeOwnershipCsv(array $rows): string
{
    $path = sys_get_temp_dir().'/owner_import_'.uniqid().'.csv';

    $handle = fopen($path, 'w');
    fputcsv($handle, ['trainee_name', 'document_type', 'country_name', 'trainer_name', 'accreditation_date']);

    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }

    fclose($handle);

    return $path;
}

it('allows the same trainee name under different owners', function () {
    $traineeA = Trainee::factory()->create(['name' => 'Shared Name', 'owner_type' => User::class, 'owner_id' => 1]);
    $traineeB = Trainee::factory()->create(['name' => 'Shared Name', 'owner_type' => CertifiedCenter::class, 'owner_id' => 2]);

    expect($traineeA->id)->not->toBe($traineeB->id);
});

it('blocks the same trainee name under the same owner', function () {
    Trainee::factory()->create(['name' => 'Duplicate Name', 'owner_type' => User::class, 'owner_id' => 1]);

    expect(fn () => Trainee::factory()->create(['name' => 'Duplicate Name', 'owner_type' => User::class, 'owner_id' => 1]))
        ->toThrow(QueryException::class);
});

it('scopes the center trainee resource to the current center owner', function () {
    $center = CertifiedCenter::factory()->create();

    $own = Trainee::factory()->ownedBy($center)->create();
    Trainee::factory()->ownedBy(CertifiedCenter::factory()->create())->create();

    $this->actingAs($center, 'certified_center');

    expect(TraineeResource::getEloquentQuery()->pluck('id'))->toHaveCount(1)
        ->and(TraineeResource::getEloquentQuery()->first()->id)->toBe($own->id);
});

it('limits center and trainer policy access to owned trainees', function () {
    $admin = User::factory()->create(['type' => 'admin']);
    $center = CertifiedCenter::factory()->create();

    $owned = Trainee::factory()->ownedBy($center)->create();
    $foreign = Trainee::factory()->create(['owner_type' => User::class, 'owner_id' => $admin->id]);

    expect((new TraineePolicy)->view($center, $owned))->toBeTrue()
        ->and((new TraineePolicy)->view($center, $foreign))->toBeFalse()
        ->and((new TraineePolicy)->update($center, $foreign))->toBeFalse()
        ->and((new TraineePolicy)->view($admin, $foreign))->toBeTrue();
});

it('creates import trainees owned by the importing user', function () {
    $path = traineeOwnershipCsv([['Imported Trainee One', 'doc', 'Saudi Arabia', 'trainer', '2026-01-15']]);

    app(CertificationImportService::class)->importCertifications($path, 42);

    $trainee = Trainee::where('name', 'Imported Trainee One')->first();

    expect($trainee)->not->toBeNull()
        ->and($trainee->owner_type)->toBe(User::class)
        ->and((int) $trainee->owner_id)->toBe(42);
});
