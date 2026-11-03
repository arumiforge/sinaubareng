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
            'directory_match' => 'Data referensi',
        ],
        'directory_match' => [
            'matched' => 'Cocok',
            'missing_npsn' => 'NPSN kosong',
            'not_in_directory' => 'Tidak ada di data referensi',
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
    'directory' => [
        'label' => 'sekolah',
        'plural_label' => 'Sebaran sekolah',
        'fields' => [
            'npsn' => 'NPSN',
            'name' => 'Nama sekolah',
            'level' => 'Jenjang',
            'district' => 'Kecamatan',
            'region' => 'Kabupaten/kota',
            'is_customer' => 'Pelanggan',
        ],
        'summary' => [
            'schools' => 'Jumlah sekolah',
            'customers' => 'Sudah pelanggan',
        ],
        'filters' => [
            'customers' => 'Sudah pelanggan',
            'not_customers' => 'Belum pelanggan',
        ],
        'actions' => [
            'summary' => 'Ringkasan per kelompok',
            'list' => 'Daftar sekolah',
            'map' => 'Lihat peta',
            'download' => 'Unduh Excel',
        ],
        'unmatched' => '{1} 1 sekolah pelanggan belum punya NPSN, jadi belum terhitung di sini. Lengkapi NPSN-nya di menu Sekolah.|[2,*] :count sekolah pelanggan belum punya NPSN, jadi belum terhitung di sini. Lengkapi NPSN-nya di menu Sekolah.',
    ],
    'map' => [
        'title' => 'Peta sekolah',
        'all' => 'Semua',
        'show' => 'Tampilkan',
        'count' => ':shown sekolah di peta, :customers di antaranya pelanggan.',
        'missing_locations' => '{1} 1 sekolah tidak tampil karena lokasinya belum tercatat di data referensi.|[2,*] :count sekolah tidak tampil karena lokasinya belum tercatat di data referensi.',
    ],
];
