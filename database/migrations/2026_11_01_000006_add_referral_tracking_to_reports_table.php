<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->boolean('needs_referral')->default(false)->after('follow_up_note');
            $table->string('referral_target')->nullable()->after('needs_referral');
            $table->text('referral_note')->nullable()->after('referral_target');
            $table->timestamp('referred_at')->nullable()->after('referral_note');
            $table->foreignId('referred_by')->nullable()->after('referred_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['needs_referral', 'referral_target', 'referral_note', 'referred_at', 'referred_by']);
        });
    }
};
