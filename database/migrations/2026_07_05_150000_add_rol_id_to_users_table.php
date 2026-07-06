<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rol_id')->nullable()->after('tipo')
                ->constrained('roles')->nullOnDelete();
        });

        DB::table('roles')->get(['id', 'nombre'])->each(function ($rol) {
            DB::table('users')->where('tipo', $rol->nombre)->update(['rol_id' => $rol->id]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rol_id');
        });
    }
};
