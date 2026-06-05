# TODO

## Tahap 1 — Analisa & Konfirmasi
- [x] Identifikasi flow login: `AuthController::attemptLogin()` memilih `role_active` dari role_codes user.
- [x] Identifikasi proteksi route: `app/Config/Routes.php` memakai filter `auth:protected` dan `role:...`.
- [x] Identifikasi potensi gap: `AuthFilter` tidak memblokir akses route dashboard lain selain validasi role dashboard ada/tidak.
- [x] Validasi `RoleFilter` memang akan mem-block jika role user tidak termasuk allowedRoles.

## Tahap 2 — Perbaiki batasan login per role
- [ ] Tambahkan mekanisme yang menjamin user dengan role tidak valid/terpetakan tidak bisa mengakses *route dashboard apa pun*.
- [ ] Pastikan jika user punya banyak role, route yang diakses tetap diizinkan hanya untuk role yang termasuk allowed.

## Tahap 3 — Implementasi
- [x] Update `app/Filters/AuthFilter.php` agar saat mode `protected`, user hanya boleh mengakses prefiks dashboard sesuai `role_active`.
- [ ] (Opsional) Update `RoleFilter.php` agar jika session role kosong/ tidak terpetakan, selalu redirect login.

## Tahap 4 — Testing
- [ ] Test login untuk setiap role: admin, koordinator, dosen, asisten, mahasiswa.
- [ ] Test akses dashboard yang salah role: harus forbidden/ditolak.

## Tahap 5 — Catatan
- Jika route dashboard lain masih bisa diakses, evaluasi kembali prefix route yang dibandingkan di `AuthFilter` (currentPath vs allowedPrefix).


