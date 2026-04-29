<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->date('tgl_dikembalikan')->nullable()->after('tanggal_kembali');
            $table->integer('denda')->default(0)->after('tgl_dikembalikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropColumn(['tgl_dikembalikan', 'denda']);
        });
    }
};