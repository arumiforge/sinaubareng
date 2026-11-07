<?php

declare(strict_types=1);

return [
    // Used by the framework too.
    'failed' => 'Email atau kata sandi salah. Periksa lagi, lalu coba masuk kembali.',
    'password' => 'Kata sandi salah.',
    'throttle' => 'Terlalu banyak percobaan masuk. Coba lagi dalam :minutes menit.',

    'titles' => [
        'login' => 'Masuk',
        'forgot_password' => 'Lupa kata sandi',
        'reset_password' => 'Buat kata sandi baru',
        'accept_invitation' => 'Terima undangan',
        'invitation_unavailable' => 'Undangan tidak bisa dipakai',
    ],
    'fields' => [
        'email' => 'Email',
        'password' => 'Kata sandi',
        'new_password' => 'Kata sandi baru',
        'password_confirmation' => 'Ulangi kata sandi',
        'name' => 'Nama lengkap',
        'remember' => 'Ingat saya di perangkat ini',
    ],
    'hints' => [
        'password_rules' => 'Minimal 8 karakter, berisi huruf dan angka.',
        'name' => 'Ditulis seperti di dokumen sekolah, termasuk gelar bila perlu.',
    ],
    'actions' => [
        'login' => 'Masuk',
        'forgot_password' => 'Lupa kata sandi?',
        'send_reset_link' => 'Kirim tautan',
        'save_password' => 'Simpan kata sandi',
        'back_to_login' => 'Kembali ke halaman masuk',
        'accept_invitation' => 'Terima undangan',
        'show_password' => 'Tampilkan kata sandi',
        'hide_password' => 'Sembunyikan kata sandi',
    ],
    'intro' => [
        'login' => 'Masuk dengan email dan kata sandi akun Sigrak Anda.',
        'forgot_password' => 'Tulis email akun Anda. Kami kirimkan tautan untuk membuat kata sandi baru.',
        'reset_password' => 'Buat kata sandi baru untuk :email.',
        'invite_only' => 'Belum punya akun? Akun dibuat lewat undangan dari operator sekolah Anda.',
    ],
    'reset' => [
        'link_sent' => 'Kalau email itu terdaftar, tautan untuk membuat kata sandi baru sudah kami kirim. Periksa juga folder spam.',
        'invalid' => 'Tautan ini sudah tidak berlaku. Minta tautan baru dari halaman Lupa kata sandi.',
        'done' => 'Kata sandi baru tersimpan. Silakan masuk.',
    ],
    'invitation' => [
        'intro' => 'Anda diundang ke :school sebagai :role.',
        'new_account' => 'Buat kata sandi untuk akun baru Anda dengan email :email.',
        'existing_account' => 'Email :email sudah punya akun Sigrak. Masukkan kata sandinya untuk menerima undangan ini.',
        'signed_in_as' => 'Anda sedang masuk sebagai :email. Setelah undangan diterima, Anda akan masuk dengan akun yang diundang.',
        'unavailable' => 'Undangan ini sudah dipakai, sudah lewat batas waktunya, atau tautannya tidak lengkap. Minta operator sekolah mengirim undangan baru.',
        'wrong_password' => 'Kata sandi salah. Kalau Anda lupa, buat kata sandi baru lewat halaman Lupa kata sandi.',
        'principal_taken' => 'Sekolah ini sudah punya kepala sekolah aktif. Hubungi Tim Sigrak untuk mengubahnya.',
        'accepted' => 'Selamat datang di :school.',
    ],
];
