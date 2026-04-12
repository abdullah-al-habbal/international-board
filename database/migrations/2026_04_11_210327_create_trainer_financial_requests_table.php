<?php
// d:\board\database\migrations\2026_04_11_210327_create_trainer_financial_requests_table.php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_financial_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('trainer_id')->constrained('trainers')->cascadeOnDelete();
            $table->foreignId('agent_person_id')->nullable()->constrained('certified_center_payment_agent_persons')->nullOnDelete();
            $table->decimal('total_payment', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->text('reason')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_financial_requests');
    }
};
