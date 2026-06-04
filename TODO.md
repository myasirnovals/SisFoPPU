# TODO

## 1) Identifikasi duplikat migration profile
- [x] Baca migration utama dan alternatif: `_nofk`, `_relations`, `create_profile_tables`, `fix_profile_fk_types`.
- [x] Tentukan file alternatif/duplikat yang up()-nya `return;` (non-aktif).

## 2) Hapus file migration duplikatif/alternatif untuk keamanan migrate
- [x] Hapus: `app/Database/Migrations/2026-06-04_000012_restructure_profile_tables_relations.php`
- [x] Hapus: `app/Database/Migrations/2026-05-21-000003_create_profile_tables.php`
- [x] Hapus: `app/Database/Migrations/2026-06-04_000013_fix_profile_fk_types.php`
- [x] Update checklist ini setelah berhasil


