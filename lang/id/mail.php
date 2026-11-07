<?php

declare(strict_types=1);

return [
    'footer' => 'Email ini dikirim otomatis oleh Sigrak. Mohon tidak membalas email ini.',
    'link_fallback' => 'Kalau tombol tidak bisa dibuka, salin tautan ini ke peramban:',
    'invitation' => [
        'subject' => 'Undangan bergabung dengan :school di Sigrak',
        'intro' => 'Anda diundang bergabung dengan :school di Sigrak sebagai :role.',
        'instructions' => 'Buka tautan di bawah untuk membuat kata sandi dan mulai memakai Sigrak.',
        'action' => 'Terima undangan',
        'expires' => 'Tautan ini berlaku sampai :date WIB.',
        'ignore' => 'Kalau Anda tidak merasa diundang, abaikan email ini.',
    ],
    'password_reset' => [
        'subject' => 'Buat kata sandi baru Sigrak',
        'intro' => 'Halo :name, kami menerima permintaan untuk membuat kata sandi baru akun Sigrak Anda.',
        'action' => 'Buat kata sandi baru',
        'expires' => 'Tautan ini berlaku :minutes menit.',
        'ignore' => 'Kalau Anda tidak memintanya, abaikan email ini. Kata sandi Anda tidak berubah.',
    ],
];
