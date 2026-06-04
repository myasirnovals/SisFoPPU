<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixProfileFkTypes extends Migration
{
    public function up()
    {
        // DISABLED: tidak relevan lagi karena tabel profile dibentuk oleh migration _nofk.
        return;

        // Perbaiki tipe study_program_id di tabel profile agar match dengan tabel parent.

        // Di CreateProdiTable.php: id = INT(11) unsigned.
        // Maka FK child harus INT UNSIGNED juga (dan kadang issue terjadi karena mismatch constraint/rule sebelumnya).

        $tables = ['students', 'assistants', 'lecturers'];
        foreach ($tables as $table) {
            // Hapus FK & index lama lalu ubah tipe.
            // CI Forge tidak support alter foreign key dengan mudah, jadi gunakan raw SQL.
            // NOTE: Nama constraint berbeda antar MySQL, jadi kita drop via INFORMATION_SCHEMA.

            $sql = "
            SET @old_fk := (
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = '$table'
                  AND COLUMN_NAME = 'study_program_id'
                  AND REFERENCED_TABLE_NAME = 'prodi'
                LIMIT 1
            );
            
            SET @drop_fk := IF(@old_fk IS NULL, NULL, CONCAT('ALTER TABLE `$table` DROP FOREIGN KEY `', @old_fk, '`'));
            SET @stmt := @drop_fk;
            SELECT @stmt;
            
            /* Attempt drop FK if exists */
            SET @exec := (SELECT CASE WHEN @old_fk IS NULL THEN 0 ELSE 1 END);
            
            SET SESSION sql_notes = 0;
            SET @exec;

            
            ALTER TABLE `$table` MODIFY `study_program_id` INT UNSIGNED NULL;
            ";
            // Jalankan dengan try/catch tidak tersedia di MigrationRunner, jadi kita jalankan minimal alter tipe.
            $this->db->query("ALTER TABLE `$table` MODIFY `study_program_id` INT UNSIGNED" . ($table === 'students' ? ' NOT NULL' : ' NULL') );
        }

        // students dibuat NOT NULL di restructure (sekarang tidak ada alter, jadi biarkan set sesuai sebelumnya)
        $this->db->query("ALTER TABLE `students` MODIFY `study_program_id` INT UNSIGNED NOT NULL");

        // Re-add foreign keys secara eksplisit agar FK terbentuk.
        // (Drop fk dulu via re-create tabel sebelumnya biasanya sudah cukup; di sini kita pastikan)
        $this->db->query("ALTER TABLE `students` ADD CONSTRAINT `students_study_program_id_foreign` FOREIGN KEY (`study_program_id`) REFERENCES `prodi`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT");
        $this->db->query("ALTER TABLE `assistants` ADD CONSTRAINT `assistants_study_program_id_foreign` FOREIGN KEY (`study_program_id`) REFERENCES `prodi`(`id`) ON DELETE SET NULL ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE `lecturers` ADD CONSTRAINT `lecturers_study_program_id_foreign` FOREIGN KEY (`study_program_id`) REFERENCES `prodi`(`id`) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        // Tidak implement karena skema constraint spesifik.
    }
}

