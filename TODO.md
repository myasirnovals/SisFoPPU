# TODO - Fix Seed Error

## Info
- Error saat `php spark db:seed InitialSystemSeeder`: `Undefined variable $data` di `app/Database/Seeds/InitialSystemSeeder.php` baris ~285.

## Langkah
- [ ] Buat rencana perbaikan.
- [ ] Perbaiki seeder: hapus/benahi penggunaan `unset($data['user_id']);` agar tidak ada variable `$data` yang tidak didefinisikan.
- [ ] Jalankan `php spark db:seed InitialSystemSeeder` untuk verifikasi.
- [ ] Jika muncul error lanjutan (mis. kolom unik/tabel profile), perbaiki mapping field sesuai skema migration.

