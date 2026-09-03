<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add full moderation support to the reviews table while preserving any
     * existing rows:
     *   - `status`                    : pending | approved | rejected
     *   - `is_visible_on_homepage`    : whether the approved review may appear on the homepage
     *   - `is_verified_purchase`      : whether it was created from a real completed order
     *   - `order_id`                  : the (optional) purchase order that earned this review
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('comment');
            $table->boolean('is_visible_on_homepage')->default(false)->after('status');
            $table->boolean('is_verified_purchase')->default(false)->after('is_visible_on_homepage');
            $table->foreignId('order_id')->nullable()->after('is_verified_purchase')->constrained()->nullOnDelete();
        });

        // Backfill existing records so no data is lost:
        // - Already approved reviews stay approved AND become homepage-visible (legacy behaviour).
        // - Unapproved reviews become "pending" so an admin can review them.
        DB::table('reviews')->where('is_approved', true)->update([
            'status' => 'approved',
            'is_visible_on_homepage' => true,
        ]);
        DB::table('reviews')->where('is_approved', false)->update([
            'status' => 'pending',
        ]);
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_id');
            $table->dropColumn(['status', 'is_visible_on_homepage', 'is_verified_purchase']);
        });
    }
};
