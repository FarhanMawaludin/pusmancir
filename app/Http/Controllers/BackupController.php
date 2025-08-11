<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{

    public function index()
    {
        $activeMenu = 'backup';
        return view('admin.backup.index', compact('activeMenu'));
    }
public function backupDatabase()
{
    $dbHost = env('DB_HOST');
    $dbName = env('DB_DATABASE');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');

    $filename = $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
    $filePath = storage_path('app/' . $filename);

    // Koneksi database
    $mysqli = new \mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($mysqli->connect_error) {
        return "Koneksi database gagal: " . $mysqli->connect_error;
    }

    $tables = [];
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    $sqlDump = "";
    foreach ($tables as $table) {
        // Struktur tabel
        $result = $mysqli->query("SHOW CREATE TABLE `$table`");
        $row = $result->fetch_assoc();

        // Tambahkan IF NOT EXISTS pada CREATE TABLE
        $createTableSQL = preg_replace('/^CREATE TABLE /i', 'CREATE TABLE IF NOT EXISTS ', $row['Create Table']);

        $sqlDump .= "\n\n-- Struktur untuk tabel `$table`\n\n";
        $sqlDump .= $createTableSQL . ";\n\n";

        // Data tabel
        $result = $mysqli->query("SELECT * FROM `$table`");

        while ($rowData = $result->fetch_assoc()) {
            $valuesArr = [];

            foreach ($rowData as $value) {
                if (is_null($value)) {
                    $valuesArr[] = "NULL";
                } elseif (is_bool($value)) {
                    // Cast boolean ke 1 atau 0
                    $valuesArr[] = $value ? '1' : '0';
                } elseif (is_numeric($value) && !is_string($value)) {
                    // Angka tanpa kutip
                    $valuesArr[] = $value;
                } else {
                    // Escape string dengan benar
                    // Tambahkan tambahan stripslashes agar tidak double escape jika ada
                    $escaped = $mysqli->real_escape_string($value);
                    $valuesArr[] = "'" . $escaped . "'";
                }
            }

            $sqlDump .= "INSERT IGNORE INTO `$table` VALUES(" . implode(", ", $valuesArr) . ");\n";
        }
        $sqlDump .= "\n";
    }

    // Simpan file
    file_put_contents($filePath, $sqlDump);

    return response()->download($filePath)->deleteFileAfterSend(true);
}
        

public function importDatabase(Request $request)
{
    $request->validate([
        'sql_file' => 'required|file|mimes:sql,txt',
    ]);

    $file = $request->file('sql_file');
    $filePath = $file->getRealPath();

    $dbHost = env('DB_HOST');
    $dbName = env('DB_DATABASE');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');

    $mysqli = new \mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($mysqli->connect_error) {
        return back()->with('error', 'Koneksi database gagal: ' . $mysqli->connect_error);
    }

    $sqlContent = file_get_contents($filePath);

    // Cari semua nama tabel yang didefinisikan di CREATE TABLE statements
    preg_match_all('/CREATE TABLE IF NOT EXISTS `?(\w+)`?/i', $sqlContent, $matches);
    $tablesInFile = $matches[1] ?? [];

    // Cek tabel mana saja yang sudah ada
    $existingTables = [];
    foreach ($tablesInFile as $table) {
        $escapedTable = $mysqli->real_escape_string($table);
        $checkTable = $mysqli->query("SHOW TABLES LIKE '$escapedTable'");
        if ($checkTable && $checkTable->num_rows > 0) {
            $existingTables[] = $table;
        }
    }

    // Jika ada tabel yang sudah ada, hapus CREATE TABLE & INSERT untuk tabel tersebut dari sqlContent
    foreach ($existingTables as $table) {
        // Hapus CREATE TABLE dan INSERT terkait tabel tersebut dari SQL
        // Ini pola sederhana, bisa disesuaikan jika file sangat kompleks
        $pattern = "/CREATE TABLE IF NOT EXISTS `?$table`?.*?;\n(INSERT IGNORE INTO `?$table`?.*?;)?/is";
        $sqlContent = preg_replace($pattern, '', $sqlContent);
    }

    // Jalankan query multi_query sisa SQL
    if (!$mysqli->multi_query($sqlContent)) {
        return back()->with('error', 'Gagal menjalankan query: ' . $mysqli->error);
    }

    // Kosongkan semua sisa result supaya koneksi bersih
    do {
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    if (count($existingTables) > 0) {
        $tablesStr = implode(', ', $existingTables);
        return back()->with('warning', "Import selesai, tapi tabel berikut sudah ada dan tidak diimpor ulang: $tablesStr");
    }

    return back()->with('success', 'Import database berhasil!');
}



}
