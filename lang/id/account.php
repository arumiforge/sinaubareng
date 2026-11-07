<?php

declare(strict_types=1);

return [
    'titles' => [
        'edit' => 'Akun',
        'profile' => 'Profil',
        'password' => 'Kata sandi',
        'roles' => 'Peran Anda',
    ],
    'fields' => [
        'name' => 'Nama lengkap',
        'email' => 'Email',
        'nip' => 'NIP',
        'current_password' => 'Kata sandi saat ini',
        'password' => 'Kata sandi baru',
        'password_confirmation' => 'Ulangi kata sandi baru',
    ],
    'hints' => [
        'email' => 'Email dipakai untuk masuk. Untuk menggantinya, hubungi Tim Sigrak.',
        'nip' => '18 angka. Kosongkan kalau Anda tidak punya NIP.',
        'password' => 'Minimal 8 karakter, berisi huruf dan angka.',
        'roles' => 'Pilih sekolah dan peran yang sedang Anda kerjakan.',
    ],
    'actions' => [
        'save_profile' => 'Simpan profil',
        'save_password' => 'Simpan kata sandi',
    ],
    'empty' => [
        'roles' => 'Anda belum punya peran aktif di sekolah mana pun. Minta operator sekolah mengirim undangan.',
    ],
    'toasts' => [
        'profile_saved' => 'Profil tersimpan.',
        'password_saved' => 'Kata sandi baru tersimpan.',
        'role_switched' => 'Sekarang Anda bekerja sebagai :role di :school.',
    ],
    'errors' => [
        'role_unavailable' => 'Peran itu sudah tidak aktif. Muat ulang halaman, lalu pilih lagi.',
    ],
];
