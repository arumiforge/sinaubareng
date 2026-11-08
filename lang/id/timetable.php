<?php

declare(strict_types=1);

return [
    'titles' => [
        'class' => 'Jadwal kelas :name',
        'mine' => 'Jadwal',
    ],
    'days' => [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ],
    'fields' => [
        'weekday' => 'Hari',
        'starts_at' => 'Jam mulai',
        'ends_at' => 'Jam selesai',
        'kind' => 'Jenis',
        'subject' => 'Mata pelajaran',
        'jp' => 'JP',
        'label' => 'Keterangan',
    ],
    'hints' => [
        'page' => 'Isi jadwal per hari. Urutan jam mengikuti jam mulai. Guru setiap mapel diambil dari halaman kelas.',
        'jp' => '1 JP = :minutes menit. JP diisi otomatis dari lamanya, bisa diubah.',
        'label' => 'Boleh dikosongkan. Contoh: Senam, Literasi, Sholat Dhuha.',
        'subject_no_teacher' => 'belum ada guru',
    ],
    'columns' => [
        'period' => 'Jam ke',
        'time' => 'Waktu',
        'activity' => 'Kegiatan',
        'teacher' => 'Guru',
        'class' => 'Kelas',
        'scheduled' => 'Terjadwal',
        'allocated' => 'Alokasi',
    ],
    'jp_value' => ':jp JP',
    'jp_progress' => ':scheduled dari :allocated JP',
    'jp_free' => ':scheduled JP (tanpa batas)',
    'no_teacher' => 'Belum ada guru',
    'not_offered' => 'Tidak diajarkan lagi',
    'status' => [
        'complete' => 'Jadwal lengkap: semua mapel sudah sesuai alokasi JP.',
        'incomplete' => 'Jadwal belum lengkap: JP beberapa mapel belum sesuai alokasi.',
        'under' => 'Kurang',
        'over' => 'Lebih',
        'ok' => 'Pas',
    ],
    'summary' => 'JP per mata pelajaran',
    'total_jp' => 'Total :jp JP per minggu',
    'empty_day' => 'Belum ada jadwal.',
    'empty_mine' => 'Belum ada jam mengajar Anda di jadwal tahun ajaran ini.',
    'no_year' => 'Belum ada tahun ajaran. Operator sekolah perlu menambahkannya dulu.',
    'my_lessons' => 'Jam mengajar saya',
    'homeroom' => 'Kelas perwalian: :name',
    'year' => 'Tahun ajaran :name',
    'actions' => [
        'open' => 'Jadwal',
        'add' => 'Tambah jam',
        'save' => 'Simpan',
        'edit' => 'Ubah',
        'delete' => 'Hapus',
        'back' => 'Kembali ke kelas',
    ],
    'dialogs' => [
        'delete' => 'Hapus :item hari :day jam :time dari jadwal?',
    ],
    'toasts' => [
        'saved' => 'Jadwal tersimpan.',
        'deleted' => 'Jam dihapus dari jadwal.',
    ],
    'errors' => [
        'overlap' => 'Waktunya bertabrakan dengan jam lain di hari yang sama.',
        'no_teacher' => ':subject belum punya guru. Pilih gurunya dulu di halaman kelas.',
        'over_allocation' => 'Alokasi :subject :allocated JP per minggu. Sisa yang bisa dijadwalkan: :left JP.',
        'teacher_clash' => ':teacher sudah mengajar di kelas :class pada hari :day jam :time.',
    ],
];
