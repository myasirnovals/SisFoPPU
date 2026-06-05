# TODO

## Login RBAC rombak
- [x] Analisis flow login saat ini (AuthController, UserModel, AuthFilter, Routes).
- [x] Pastikan redirect dashboard berdasarkan role alias yang konsisten.
- [x] Terapkan single source of truth role untuk session: gunakan `role_codes` dari `findForAuthentication()`.
- [x] Set session keys yang dibaca filter: `is_logged_in`, `role`, `role_active`.
- [x] Tolak login jika role tidak terpetakan (tidak ada alias dashboard).
- [x] Quick check kompatibilitas logika RoleFilter (role:*).

