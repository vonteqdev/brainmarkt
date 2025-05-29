<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ensure users table's 'id' is UUID if 'agencies.id' is UUID for foreign key constraint.
            // If users.id is default BIGINT, agencies.id should also be BIGINT or use a different non-constrained approach.
            // This example assumes you've standardized on UUIDs for primary keys for both.
            // If not, adjust foreignUuid to foreignId or ensure types match.
            if (!Schema::hasColumn('users', 'agency_id')) {
                $table->foreignUuid('agency_id')->after('id')->constrained('agencies')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                // Assuming roles.id is BIGINT (default for id())
                $table->foreignId('role_id')->after('agency_id')->constrained('roles')->restrictOnDelete();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                 $table->boolean('is_active')->default(true)->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'profile_picture_url')) {
                $table->string('profile_picture_url')->nullable()->after('last_login_at');
            }

            // If email needs to be unique per agency (not globally):
            // This requires dropping the default unique index on 'email' first if it exists
            // and then adding a composite unique index. This is an advanced change.
            // Example (check if 'users_email_unique' exists before dropping):
            // if (collect(DB::select("SHOW INDEXES FROM users WHERE Key_name = 'users_email_unique'"))->isNotEmpty()) {
            //     $table->dropUnique('users_email_unique');
            // }
            // $table->unique(['agency_id', 'email'], 'users_agency_id_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'agency_id')) {
                $table->dropForeign(['agency_id']); // Drops based on column name convention
                $table->dropColumn('agency_id');
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
            if (Schema::hasColumn('users', 'profile_picture_url')) {
                $table->dropColumn('profile_picture_url');
            }
            // Revert unique constraint if changed:
            // if (collect(DB::select("SHOW INDEXES FROM users WHERE Key_name = 'users_agency_id_email_unique'"))->isNotEmpty()) {
            //     $table->dropUnique('users_agency_id_email_unique');
            // }
            // if (collect(DB::select("SHOW INDEXES FROM users WHERE Key_name = 'users_email_unique'"))->isEmpty()) {
            //     $table->unique('email');
            // }
        });
    }
};
