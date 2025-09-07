<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DBSchemaController extends Controller
{
    public function index()
    {
        $connection = DB::getDriverName(); // sqlite, mysql, pgsql
        $schema = [];

        if ($connection === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $tableNames = array_map(fn($table) => $table->name, $tables);
        } elseif ($connection === 'mysql') {
            $dbName = DB::getDatabaseName();
            $tables = DB::select("SHOW TABLES");
            $tableKey = "Tables_in_$dbName";
            $tableNames = array_map(fn($table) => $table->$tableKey, $tables);
        } elseif ($connection === 'pgsql') {
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            $tableNames = array_map(fn($table) => $table->tablename, $tables);
        } else {
            return "Unsupported database: $connection";
        }

        foreach ($tableNames as $table) {
            $columns = Schema::getColumnListing($table);
            $schema[$table] = [];

            foreach ($columns as $column) {
                $type = Schema::getColumnType($table, $column);
                $schema[$table][] = [
                    'name' => $column,
                    'type' => $type,
                ];
            }
        }

        return view('database-schema', compact('schema'));
    }
}