<?php

declare(strict_types=1);

return [
    'titles' => [
        'teachers' => 'Guru',
        'invite' => 'Undang guru',
        'invitations' => 'Undangan terkirim',
    ],
    'fields' => [
        'email' => 'Email guru',
    ],
    'hints' => [
        'invite' => 'Guru menerima email berisi tautan untuk membuat kata sandi. Tautan berlaku 7 hari.',
    ],
    'columns' => [
        'name' => 'Nama',
        'email' => 'Email',
        'status' => 'Status',
        'expires_at' => 'Berlaku sampai',
    ],
    'actions' => [
        'invite' => 'Kirim undangan',
        'resend' => 'Kirim ulang',
        'cancel_invitation' => 'Batalkan undangan',
    ],
    'dialogs' => [
        'cancel_invitation' => [
            'body' => 'Batalkan undangan untuk :email? Tautan di email itu tidak bisa dipakai lagi.',
        ],
    ],
    'empty' => [
        'teachers' => 'Belum ada guru. Undang guru dengan emailnya.',
        'invitations' => 'Tidak ada undangan yang menunggu.',
    ],
    'toasts' => [
        'invitation_sent' => 'Undangan terkirim ke :email.',
        'invitation_cancelled' => 'Undangan untuk :email dibatalkan.',
    ],
    'errors' => [
        'already_member' => 'Email ini sudah terdaftar dengan peran itu di sekolah ini.',
        'principal_taken' => 'Sekolah ini sudah punya kepala sekolah aktif.',
        'invitation_accepted' => 'Undangan ini sudah diterima, jadi tidak perlu dikirim ulang.',
    ],
];
