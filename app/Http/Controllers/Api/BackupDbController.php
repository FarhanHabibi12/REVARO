<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackupDbController extends Controller
{
    /**
     * Generate a SQL backup of the MySQL database and download it.
     */
    public function backup()
    {
        $tables = [];
        $result = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $key = 'Tables_in_' . $dbName;

        foreach ($result as $row) {
            $tables[] = $row->$key;
        }

        $sql = "-- REVARO Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Get create table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
            $createTableArray = (array)$createTable[0];
            $createStatement = array_values($createTableArray)[1];
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createStatement . ";\n\n";

            // Get records
            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $rowArray = (array)$row;
                $keys = array_map(function($k) { return "`{$k}`"; }, array_keys($rowArray));
                $values = array_map(function($v) {
                    if (is_null($v)) return 'NULL';
                    return DB::getPdo()->quote($v);
                }, array_values($rowArray));

                $sql .= "INSERT INTO `{$table}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $fileName = 'backup_' . date('Ymd_His') . '.sql';

        return response($sql, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Restore/overwrite the database from an uploaded SQL backup file.
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $file = $request->file('backup_file');
        $sql = file_get_contents($file->getRealPath());

        if (empty($sql)) {
            return response()->json(['message' => 'File backup kosong atau tidak valid.'], 400);
        }

        try {
            // Temporary disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Run statements DDL / DML directly (MySQL DDL commits implicitly)
            DB::unprepared($sql);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json(['message' => 'Database berhasil ditimpa/di-restore dari file backup.']);
        } catch (\Exception $e) {
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $ex) {}

            return response()->json([
                'message' => 'Gagal merestore database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear framework caches (route, config, view, cache).
     */
    public function clearCache(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            \App\Models\ActivityLog::create([
                'user_id' => $request->user()?->id,
                'aktivitas' => 'Membersihkan seluruh kompilasi cache framework Laravel (cache, config, route, view)',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Seluruh cache framework Laravel berhasil dibersihkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset transaction data (orders, transactions, disputes, ratings).
     */
    public function resetTransactions(Request $request)
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            DB::table('disputes')->truncate();
            DB::table('rating')->truncate();
            DB::table('transaksi')->truncate();
            DB::table('orders')->truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            \App\Models\ActivityLog::create([
                'user_id' => $request->user()?->id,
                'aktivitas' => 'Melakukan reset seluruh riwayat transaksi & order platform',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Seluruh data riwayat transaksi platform telah berhasil di-reset!'
            ]);
        } catch (\Exception $e) {
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $ex) {}

            return response()->json([
                'message' => 'Gagal mengosongkan data transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}
