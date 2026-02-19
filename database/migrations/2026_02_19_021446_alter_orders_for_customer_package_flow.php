<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('lead_id')->constrained('customers')->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'package_id')) {
                $table->foreignId('package_id')->nullable()->after('package_type')->constrained('packages')->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'package_name')) {
                $table->string('package_name')->nullable()->after('package_id');
            }

            if (!Schema::hasColumn('orders', 'package_details')) {
                $table->text('package_details')->nullable()->after('package_name');
            }

            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');
            }

            if (!Schema::hasColumn('orders', 'event_end_date')) {
                $table->date('event_end_date')->nullable()->after('event_date');
            }

            if (!Schema::hasColumn('orders', 'time_duration')) {
                $table->string('time_duration', 100)->nullable()->after('event_end_date');
            }

            if (!Schema::hasColumn('orders', 'location')) {
                $table->string('location')->nullable()->after('event_venue_name');
            }

            if (!Schema::hasColumn('orders', 'bride_name')) {
                $table->string('bride_name')->nullable()->after('location');
            }

            if (!Schema::hasColumn('orders', 'groom_name')) {
                $table->string('groom_name')->nullable()->after('bride_name');
            }

            if (!Schema::hasColumn('orders', 'requirements')) {
                $table->text('requirements')->nullable()->after('groom_name');
            }

            if (!Schema::hasColumn('orders', 'photographer_count')) {
                $table->unsignedTinyInteger('photographer_count')->default(1)->after('requirements');
            }

            if (!Schema::hasColumn('orders', 'videographer_count')) {
                $table->unsignedTinyInteger('videographer_count')->default(1)->after('photographer_count');
            }

            if (!Schema::hasColumn('orders', 'outdoor_shoot')) {
                $table->boolean('outdoor_shoot')->default(false)->after('videographer_count');
            }
        });

        // Allow orders without a lead for direct customer-based booking.
        // This statement targets MySQL/MariaDB which is used in this project setup.
        DB::statement('ALTER TABLE orders DROP FOREIGN KEY orders_lead_id_foreign');
        DB::statement('ALTER TABLE orders MODIFY lead_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_lead_id_foreign FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE orders DROP FOREIGN KEY orders_lead_id_foreign');
        DB::statement('ALTER TABLE orders MODIFY lead_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_lead_id_foreign FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE');

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'outdoor_shoot')) {
                $table->dropColumn('outdoor_shoot');
            }
            if (Schema::hasColumn('orders', 'videographer_count')) {
                $table->dropColumn('videographer_count');
            }
            if (Schema::hasColumn('orders', 'photographer_count')) {
                $table->dropColumn('photographer_count');
            }
            if (Schema::hasColumn('orders', 'requirements')) {
                $table->dropColumn('requirements');
            }
            if (Schema::hasColumn('orders', 'groom_name')) {
                $table->dropColumn('groom_name');
            }
            if (Schema::hasColumn('orders', 'bride_name')) {
                $table->dropColumn('bride_name');
            }
            if (Schema::hasColumn('orders', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('orders', 'time_duration')) {
                $table->dropColumn('time_duration');
            }
            if (Schema::hasColumn('orders', 'event_end_date')) {
                $table->dropColumn('event_end_date');
            }
            if (Schema::hasColumn('orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('orders', 'package_details')) {
                $table->dropColumn('package_details');
            }
            if (Schema::hasColumn('orders', 'package_name')) {
                $table->dropColumn('package_name');
            }
            if (Schema::hasColumn('orders', 'package_id')) {
                $table->dropConstrainedForeignId('package_id');
            }
            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }
        });
    }
};
