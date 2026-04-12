<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certified_center_payment_agent_persons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('certified_center_id')
                ->constrained('certified_centers', null, 'agent_person_center_foreign')
                ->cascadeOnDelete();
            $table->string('name');
            $table->unique(['certified_center_id', 'name'], 'agent_person_center_name_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certified_center_payment_agent_persons');
    }
};
