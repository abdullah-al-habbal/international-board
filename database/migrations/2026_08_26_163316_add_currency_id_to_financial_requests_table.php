<?php

declare(strict_types=1);

use App\Models\Currency;
use App\Models\FinancialRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_requests', function (Blueprint $table): void {
            $table->foreignId('currency_id')->nullable()->after('agent_person_id')->constrained('currencies')->nullOnDelete();
        });

        $defaultCurrencyId = Currency::where('code', 'USD')->value('id');

        if ($defaultCurrencyId) {
            FinancialRequest::query()->update(['currency_id' => $defaultCurrencyId]);
        }
    }

    public function down(): void
    {
        Schema::table('financial_requests', function (Blueprint $table): void {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
};
