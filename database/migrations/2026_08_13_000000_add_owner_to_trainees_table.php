<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainees', function (Blueprint $blueprint): void {
            $blueprint->string('owner_type')->nullable()->after('id');
            $blueprint->unsignedBigInteger('owner_id')->nullable()->after('owner_type');
            $blueprint->index(['owner_type', 'owner_id']);
        });

        $adminId = DB::table('users')->where('type', 'admin')->orderBy('id')->limit(1)->value('id');

        if ($adminId !== null) {
            DB::table('trainees')
                ->whereNull('owner_type')
                ->whereNull('owner_id')
                ->update([
                    'owner_type' => User::class,
                    'owner_id' => (int) $adminId,
                ]);
        }

        Schema::table('trainees', function (Blueprint $blueprint): void {
            $blueprint->dropUnique('trainees_name_unique');
            $blueprint->dropUnique('trainees_email_unique');
            $blueprint->dropUnique('trainees_phone_unique');
            $blueprint->dropUnique('trainees_name_key_unique');

            $blueprint->unique(['name', 'owner_type', 'owner_id'], 'trainees_name_owner_unique');
            $blueprint->unique(['email', 'owner_type', 'owner_id'], 'trainees_email_owner_unique');
            $blueprint->unique(['phone', 'owner_type', 'owner_id'], 'trainees_phone_owner_unique');
            $blueprint->unique(['name_key', 'owner_type', 'owner_id'], 'trainees_name_key_owner_unique');
        });
    }

    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $blueprint): void {
            $blueprint->dropUnique('trainees_name_owner_unique');
            $blueprint->dropUnique('trainees_email_owner_unique');
            $blueprint->dropUnique('trainees_phone_owner_unique');
            $blueprint->dropUnique('trainees_name_key_owner_unique');

            $blueprint->unique('name', 'trainees_name_unique');
            $blueprint->unique('email', 'trainees_email_unique');
            $blueprint->unique('phone', 'trainees_phone_unique');
            $blueprint->unique('name_key', 'trainees_name_key_unique');

            $blueprint->dropIndex(['owner_type', 'owner_id']);
            $blueprint->dropColumn(['owner_type', 'owner_id']);
        });
    }
};
