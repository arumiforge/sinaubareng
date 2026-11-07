<?php

declare(strict_types=1);

// Indonesian validation messages (TR-5). Each says what is wrong with the field; the
// field name comes from `attributes`.

return [
    'accepted' => ':Attribute harus disetujui.',
    'after' => ':Attribute harus setelah :date.',
    'after_or_equal' => ':Attribute harus sama dengan atau setelah :date.',
    'alpha_dash' => ':Attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'array' => ':Attribute harus berupa daftar.',
    'before' => ':Attribute harus sebelum :date.',
    'before_or_equal' => ':Attribute harus sama dengan atau sebelum :date.',
    'between' => [
        'numeric' => ':Attribute harus antara :min dan :max.',
        'string' => ':Attribute harus antara :min dan :max karakter.',
    ],
    'boolean' => ':Attribute harus berupa ya atau tidak.',
    'confirmed' => 'Konfirmasi :attribute tidak sama.',
    'current_password' => 'Kata sandi saat ini salah.',
    'date' => ':Attribute bukan tanggal yang benar.',
    'different' => ':Attribute harus berbeda dari :other.',
    'digits' => ':Attribute harus :digits angka.',
    'email' => ':Attribute harus berupa alamat email yang benar.',
    'exists' => ':Attribute yang dipilih tidak ditemukan.',
    'filled' => ':Attribute wajib diisi.',
    'in' => ':Attribute yang dipilih tidak tersedia.',
    'integer' => ':Attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':Attribute paling besar :max.',
        'string' => ':Attribute paling banyak :max karakter.',
    ],
    'min' => [
        'numeric' => ':Attribute paling kecil :min.',
        'string' => ':Attribute paling sedikit :min karakter.',
    ],
    'numeric' => ':Attribute harus berupa angka.',
    'password' => [
        'letters' => ':Attribute harus berisi paling sedikit satu huruf.',
        'mixed' => ':Attribute harus berisi huruf besar dan huruf kecil.',
        'numbers' => ':Attribute harus berisi paling sedikit satu angka.',
        'symbols' => ':Attribute harus berisi paling sedikit satu simbol.',
        'uncompromised' => ':Attribute ini pernah bocor di internet. Pilih kata sandi lain.',
    ],
    'regex' => 'Format :attribute tidak sesuai.',
    'required' => ':Attribute wajib diisi.',
    'same' => ':Attribute harus sama dengan :other.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah dipakai.',
    'uuid' => ':Attribute tidak dikenali.',

    'custom' => [
        'nip' => [
            'regex' => 'NIP terdiri dari 18 angka. Kosongkan kalau Anda tidak punya NIP.',
        ],
    ],

    'attributes' => [
        'current_password' => 'kata sandi saat ini',
        'email' => 'email',
        'membership' => 'peran',
        'name' => 'nama',
        'nip' => 'NIP',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'role' => 'peran',
        'token' => 'tautan',
    ],
];
