<?php
/**
 * Creates the engagement_interaction_counters table.
 * Part of the aiarmada/engagement package.
 *
 * @see https://github.com/aiarmada/engagement/docs
 */
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $jsonType = config('interactions.database.json_column_type', 'jsonb');
        Schema::create(config('interactions.database.tables.interaction_counters', 'interaction_counters'), function (Blueprint $table) use ($jsonType): void {
            $table->uuid('id')->primary();
            $table->string('subject_type')->index();
            $table->uuid('subject_id')->index();
            $table->string('counter_type')->index();
            $table->string('counter_key')->nullable()->index();
            $table->bigInteger('count_value')->default(0);
            $table->timestampTz('recalculated_at')->nullable();
            $table->{$jsonType}('metadata')->nullable();
            $table->timestampsTz();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists(config('interactions.database.tables.interaction_counters', 'interaction_counters'));
    }
};
