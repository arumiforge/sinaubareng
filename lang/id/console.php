<?php

declare(strict_types=1);

return [
    'regions' => [
        'label' => 'wilayah',
        'plural_label' => 'Wilayah',
        'fields' => [
            'province' => 'Provinsi',
            'regency' => 'Kabupaten/kota',
            'schools_count' => 'Jumlah sekolah',
        ],
    ],
    'schools' => [
        'label' => 'sekolah',
        'plural_label' => 'Sekolah',
        'fields' => [
            'name' => 'Nama sekolah',
            'npsn' => 'NPSN',
            'level' => 'Jenjang',
            'region' => 'Wilayah',
            'address' => 'Alamat',
        ],
        'hints' => [
            'npsn' => '8 angka. Kalau NPSN ada di data referensi, nama, jenjang, wilayah, dan alamat terisi otomatis. Boleh dikosongkan sampai NPSN sudah dicek.',
            'npsn_missing' => 'Belum dicek',
        ],
        'delete_warning' => 'Sekolah ":name" akan dihapus permanen, termasuk semua anggota dan perannya. Tindakan ini tidak bisa dibatalkan.',
    ],
    'staff_users' => [
        'label' => 'akun staf',
        'plural_label' => 'Akun staf',
        'fields' => [
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Kata sandi',
            'role' => 'Peran',
        ],
        'hints' => [
            'password' => 'Minimal 12 karakter. Kosongkan saat mengubah jika kata sandi tidak diganti.',
        ],
    ],
];
