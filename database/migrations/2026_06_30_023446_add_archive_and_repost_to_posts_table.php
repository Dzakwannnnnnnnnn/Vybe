<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('parent_id');
            $table->foreignId('repost_of_id')->nullable()->constrained('posts')->onDelete('cascade')->after('is_archived');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['repost_of_id']);
            $table->dropColumn(['is_archived', 'repost_of_id']);
        });
    }
};
