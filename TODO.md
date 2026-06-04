# TODO - Perbaikan Database (NID/NIM/NIP Login & Relasi tanpa id/user_id)

## Step 1
Audit migration skema relasi user/profile (tabel students/assistants/lecturers/coordinators/admins) dan tabel relasi (practicum_structure, attendance, score) untuk melihat kolom `id`, `user_id`, `student_id`, `assistant_id`, `lecturer_id`, dll.

## Step 2
Update migration `2026-05-21-000003_create_profile_tables.php`:
- Dosen & Koordinator login via **NID**
- Mahasiswa & Asisten login via **NIM**
- Admin via **NIP**
- Ubah skema profile:
  - hilangkan kolom `id` dan `user_id`
  - primary key/relasi pakai `user_nim`, `user_nid`, `user_nip`.


## Step 3
Update migration relasi:
- `2026-05-21-000005_create_practicum_structure_tables.php`
- dan migration lain yang memakai `student_id/assistant_id/lecturer_id` agar memakai `user_nim/user_nid` sesuai tabel profile.

## Step 4
Update seed `app/Database/Seeds/InitialSystemSeeder.php`:
- Sesuaikan `identifier_type` & `login_identifier` per role (NID/NIM/NIP)
- Pastikan insert ke tabel profile menggunakan primary key relasi `user_nim/user_nid/user_nip`.

## Step 5
Update model/controller yang memakai kolom lama `id`/`user_id` atau `assistants.nid`, `coordinators.nip`.

## Step 6
Jalankan migrasi & seed:
- `php spark migrate`
- `php spark db:seed InitialSystemSeeder`

## Step 7
Smoke test login untuk setiap role:
- Admin (NIP)
- Koordinator & Dosen (NID)
- Asisten & Mahasiswa (NIM)

