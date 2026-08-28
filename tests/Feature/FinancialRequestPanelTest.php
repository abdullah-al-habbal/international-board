<?php

declare(strict_types=1);

use App\Enums\UserType;
use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\CertifiedCenterFinancialRequestResource;
use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Pages\CreateCertifiedCenterFinancialRequest;
use App\Filament\Admin\Resources\PaymentAgentPersons\Pages\EditPaymentAgentPerson;
use App\Filament\Admin\Resources\PaymentAgentPersons\RelationManagers\TrainerFinancialRequestsRelationManager as AgentTrainerFinancialRequestsRelationManager;
use App\Filament\Admin\Resources\TrainerFinancialRequests\Pages\CreateTrainerFinancialRequest;
use App\Filament\Admin\Resources\TrainerFinancialRequests\Pages\ListTrainerFinancialRequests;
use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use App\Filament\Admin\Resources\Trainers\Pages\EditTrainer;
use App\Filament\Admin\Resources\Trainers\RelationManagers\FinancialRequestsRelationManager as TrainerFinancialRequestsRelationManager;
use App\Filament\Center\Resources\CenterFinancialRequests\CenterFinancialRequestResource;
use App\Filament\Trainer\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource as TrainerPanelFinancialRequestResource;
use App\Models\AgentPerson;
use App\Models\CertifiedCenter;
use App\Models\Currency;
use App\Models\FinancialRequest;
use App\Models\Trainer;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['type' => UserType::Admin->value]);
    $this->agent = AgentPerson::factory()->create();
    $this->currency = Currency::query()->where('code', 'USD')->firstOrFail();
});

function adminPanel(): void
{
    Filament::setCurrentPanel('admin');
}

describe('admin money form', function () {
    it('stores a masked amount as a clean decimal', function () {
        adminPanel();
        $trainer = Trainer::factory()->create();

        Livewire::actingAs($this->admin, 'web')
            ->test(CreateTrainerFinancialRequest::class)
            ->fillForm([
                'requestable_id' => $trainer->id,
                'agent_person_id' => $this->agent->id,
                'currency_id' => $this->currency->id,
                // As the $money mask submits it, thousands separators included.
                'total_payment' => '100,000.00',
                'amount_paid' => '25,000.50',
                'date' => now()->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $request = FinancialRequest::query()->sole();

        expect($request->total_payment)->toBe('100000.00')
            ->and($request->amount_paid)->toBe('25000.50')
            ->and($request->remaining_amount)->toBe('74999.50')
            ->and($request->currency_id)->toBe($this->currency->id);
    });

    it('shows the remaining amount for the values being entered', function () {
        adminPanel();
        $trainer = Trainer::factory()->create();

        Livewire::actingAs($this->admin, 'web')
            ->test(CreateTrainerFinancialRequest::class)
            ->fillForm([
                'requestable_id' => $trainer->id,
                'agent_person_id' => $this->agent->id,
                'currency_id' => $this->currency->id,
                'total_payment' => '100000.00',
                'amount_paid' => '25000.00',
                'date' => now()->toDateString(),
            ])
            // Money::subtract, formatted by Filament's money formatter, so the
            // preview cannot drift from the model's accessor.
            ->assertSee('75,000.00');
    });

    it('rejects a paid amount above the total', function () {
        adminPanel();
        $trainer = Trainer::factory()->create();

        Livewire::actingAs($this->admin, 'web')
            ->test(CreateTrainerFinancialRequest::class)
            ->fillForm([
                'requestable_id' => $trainer->id,
                'agent_person_id' => $this->agent->id,
                'currency_id' => $this->currency->id,
                'total_payment' => '100.00',
                'amount_paid' => '100.01',
                'date' => now()->toDateString(),
            ])
            ->call('create')
            ->assertHasFormErrors(['amount_paid']);

        expect(FinancialRequest::query()->count())->toBe(0);
    });

    it('rejects a zero total', function () {
        adminPanel();
        $trainer = Trainer::factory()->create();

        Livewire::actingAs($this->admin, 'web')
            ->test(CreateTrainerFinancialRequest::class)
            ->fillForm([
                'requestable_id' => $trainer->id,
                'agent_person_id' => $this->agent->id,
                'currency_id' => $this->currency->id,
                'total_payment' => '0',
                'amount_paid' => '0',
                'date' => now()->toDateString(),
            ])
            ->call('create')
            ->assertHasFormErrors(['total_payment']);
    });

    it('creates a center request from the center schema', function () {
        adminPanel();
        $center = CertifiedCenter::factory()->create();

        Livewire::actingAs($this->admin, 'web')
            ->test(CreateCertifiedCenterFinancialRequest::class)
            ->fillForm([
                'requestable_id' => $center->id,
                'agent_person_id' => $this->agent->id,
                'currency_id' => $this->currency->id,
                'total_payment' => '1,500.75',
                'amount_paid' => '500.25',
                'date' => now()->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $request = FinancialRequest::query()->sole();

        expect($request->requestable_type)->toBe(CertifiedCenter::class)
            ->and($request->total_payment)->toBe('1500.75')
            ->and($request->remaining_amount)->toBe('1000.50');
    });
});

describe('panel permissions', function () {
    it('lets the admin panel manage requests for both requestable types', function () {
        $request = FinancialRequest::factory()->forTrainer()->create();

        expect(TrainerFinancialRequestResource::canCreate())->toBeTrue()
            ->and(TrainerFinancialRequestResource::canEdit($request))->toBeTrue()
            ->and(CertifiedCenterFinancialRequestResource::canCreate())->toBeTrue()
            ->and(CertifiedCenterFinancialRequestResource::canEdit($request))->toBeTrue();
    });

    it('keeps the center panel read-only', function () {
        $request = FinancialRequest::factory()->forCenter()->create();

        expect(CenterFinancialRequestResource::canCreate())->toBeFalse()
            ->and(CenterFinancialRequestResource::canEdit($request))->toBeFalse()
            ->and(CenterFinancialRequestResource::canDelete($request))->toBeFalse()
            ->and(CenterFinancialRequestResource::canDeleteAny())->toBeFalse()
            ->and(CenterFinancialRequestResource::getPages())->toHaveKeys(['index', 'view'])
            ->and(CenterFinancialRequestResource::getPages())->not->toHaveKey('create');
    });

    it('keeps the trainer panel read-only', function () {
        $request = FinancialRequest::factory()->forTrainer()->create();

        expect(TrainerPanelFinancialRequestResource::canCreate())->toBeFalse()
            ->and(TrainerPanelFinancialRequestResource::canEdit($request))->toBeFalse()
            ->and(TrainerPanelFinancialRequestResource::canDelete($request))->toBeFalse()
            ->and(TrainerPanelFinancialRequestResource::canDeleteAny())->toBeFalse()
            ->and(TrainerPanelFinancialRequestResource::getPages())->toHaveKeys(['index', 'view'])
            ->and(TrainerPanelFinancialRequestResource::getPages())->not->toHaveKey('create');
    });

    it('scopes each panel to its own principal', function () {
        $center = CertifiedCenter::factory()->create();
        $trainer = Trainer::factory()->create();

        $centerRequest = FinancialRequest::factory()->for($center, 'requestable')->create();
        $trainerRequest = FinancialRequest::factory()->for($trainer, 'requestable')->create();
        $otherCenterRequest = FinancialRequest::factory()->forCenter()->create();

        $this->actingAs($center, 'certified_center');
        expect(CenterFinancialRequestResource::getEloquentQuery()->pluck('id')->all())
            ->toBe([$centerRequest->id]);

        $this->actingAs($trainer, 'trainer');
        expect(TrainerPanelFinancialRequestResource::getEloquentQuery()->pluck('id')->all())
            ->toBe([$trainerRequest->id]);

        expect($otherCenterRequest->requestable_id)->not->toBe($center->id);
    });
});

describe('relation manager eager loading', function () {
    // Laravel only arms preventLazyLoading for a result set of more than one
    // row (Builder::hydrate), so these fixtures need several records: with a
    // single one a missing eager-load stays invisible.
    it('renders money columns for a trainer without a lazy-load violation', function () {
        adminPanel();
        $trainer = Trainer::factory()->create();
        $requests = FinancialRequest::factory()
            ->count(3)
            ->for($trainer, 'requestable')
            ->create(['currency_id' => $this->currency->id]);

        Livewire::actingAs($this->admin, 'web')
            ->test(TrainerFinancialRequestsRelationManager::class, [
                'ownerRecord' => $trainer,
                'pageClass' => EditTrainer::class,
            ])
            ->assertOk()
            ->assertCanSeeTableRecords($requests);
    });

    it('renders money columns for an agent person without a lazy-load violation', function () {
        adminPanel();
        $requests = FinancialRequest::factory()
            ->count(3)
            ->forTrainer()
            ->create([
                'agent_person_id' => $this->agent->id,
                'currency_id' => $this->currency->id,
            ]);

        Livewire::actingAs($this->admin, 'web')
            ->test(AgentTrainerFinancialRequestsRelationManager::class, [
                'ownerRecord' => $this->agent,
                'pageClass' => EditPaymentAgentPerson::class,
            ])
            ->assertOk()
            ->assertCanSeeTableRecords($requests);
    });

    it('resolves every row currency in one query, not one per row', function () {
        adminPanel();
        $trainer = Trainer::factory()->create();
        FinancialRequest::factory()->count(5)->for($trainer, 'requestable')
            ->create(['currency_id' => $this->currency->id]);

        $currencyQueries = 0;
        DB::listen(function ($query) use (&$currencyQueries) {
            if (str_contains($query->sql, 'from "currencies"')) {
                $currencyQueries++;
            }
        });

        Livewire::actingAs($this->admin, 'web')
            ->test(TrainerFinancialRequestsRelationManager::class, [
                'ownerRecord' => $trainer,
                'pageClass' => EditTrainer::class,
            ])
            ->assertOk();

        expect($currencyQueries)->toBeLessThan(5);
    });
});

it('sorts a table by the derived remaining amount', function () {
    $trainer = Trainer::factory()->create();

    $small = FinancialRequest::factory()->for($trainer, 'requestable')
        ->create(['total_payment' => '100.00', 'amount_paid' => '90.00']);   // 10.00
    $large = FinancialRequest::factory()->for($trainer, 'requestable')
        ->create(['total_payment' => '100.00', 'amount_paid' => '10.00']);   // 90.00

    adminPanel();

    Livewire::actingAs($this->admin, 'web')
        ->test(ListTrainerFinancialRequests::class)
        ->sortTable('remaining_amount')
        ->assertCanSeeTableRecords([$small, $large], inOrder: true)
        ->sortTable('remaining_amount', 'desc')
        ->assertCanSeeTableRecords([$large, $small], inOrder: true);
});
