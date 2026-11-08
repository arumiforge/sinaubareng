<?php

declare(strict_types=1);

return [
    'years' => [
        'titles' => [
            'index' => 'Tahun Ajaran',
            'create' => 'Tambah tahun ajaran',
            'edit' => 'Ubah tahun ajaran :name',
        ],
        'fields' => [
            'starts_on' => 'Mulai semester ganjil',
            'even_semester_starts_on' => 'Mulai semester genap',
            'ends_on' => 'Akhir tahun ajaran',
        ],
        'hints' => [
            'form' => 'Nama tahun ajaran mengikuti tanggal mulai, misalnya 2026/2027. Biasanya dimulai 1 Juli.',
            'starts_on' => 'Hari pertama semester ganjil.',
            'even_semester_starts_on' => 'Semester ganjil berakhir sehari sebelumnya.',
        ],
        'columns' => [
            'odd_semester' => 'Semester ganjil',
            'even_semester' => 'Semester genap',
            'classes' => 'Kelas',
        ],
        'badges' => [
            'current' => 'Berjalan',
        ],
        'classes_count' => ':count kelas',
        'actions' => [
            'create' => 'Tambah tahun ajaran',
            'save' => 'Simpan',
            'edit' => 'Ubah',
            'delete' => 'Hapus',
            'classes' => 'Lihat kelas',
        ],
        'dialogs' => [
            'delete' => 'Hapus tahun ajaran :name? Tindakan ini tidak bisa dibatalkan.',
        ],
        'empty' => 'Belum ada tahun ajaran. Tambahkan tahun ajaran dulu, lalu buat kelasnya.',
        'toasts' => [
            'saved' => 'Tahun ajaran :name tersimpan.',
            'deleted' => 'Tahun ajaran :name dihapus.',
        ],
        'errors' => [
            'too_long' => 'Satu tahun ajaran paling lama 12 bulan.',
            'name_taken' => 'Tahun ajaran :name sudah ada.',
            'overlap' => 'Tanggalnya bertabrakan dengan tahun ajaran lain.',
            'has_classes' => 'Tahun ajaran :name masih punya kelas. Hapus kelasnya dulu.',
        ],
    ],
    'copy' => [
        'title' => 'Salin kelas dari :name',
        'hint' => 'Kelas tahun lalu disalin dengan tingkat naik satu (4A menjadi 5A). Kelas 6 tidak disalin karena sudah lulus. Wali kelas dan guru mapel dikosongkan supaya dipilih lagi. Siswa dipindah lewat Kenaikan nanti.',
        'field' => 'Salin kelas dari tahun ajaran :name',
        'grade_one' => 'Buat juga kelas 1 seperti tahun lalu untuk murid baru',
        'preview' => 'Kelas yang akan dibuat (:count)',
        'new_intake' => 'murid baru',
        'rename_hint' => 'Nama kelas yang tidak diawali angka tingkat tetap sama. Anda bisa menggantinya nanti di halaman kelas.',
        'actions' => [
            'open' => 'Salin kelas',
            'confirm' => 'Salin :count kelas',
        ],
        'toasts' => [
            'copied' => ':count kelas disalin ke tahun ajaran :name. Pilih wali kelas dan guru mapelnya.',
            'created_with_classes' => 'Tahun ajaran :name tersimpan dengan :count kelas. Pilih wali kelas dan guru mapelnya.',
        ],
        'errors' => [
            'not_empty' => 'Tahun ajaran :name sudah punya kelas, jadi kelas tidak disalin.',
            'nothing' => 'Tidak ada kelas yang bisa disalin dari tahun ajaran sebelumnya.',
        ],
    ],
    'classes' => [
        'titles' => [
            'index' => 'Kelas',
            'create' => 'Tambah kelas',
            'edit' => 'Ubah kelas :name',
            'show' => 'Kelas :name',
        ],
        'fields' => [
            'academic_year' => 'Tahun ajaran',
            'grade' => 'Tingkat',
            'name' => 'Nama kelas',
            'homeroom_teacher' => 'Wali kelas',
        ],
        'hints' => [
            'name' => 'Contoh: 4A atau 4 Al-Farabi. Nama tidak boleh sama dalam satu tahun ajaran.',
            'homeroom_teacher' => 'Wali kelas otomatis mengajar mata pelajaran guru kelas. Guru mapel dipilih di halaman kelas.',
            'no_teachers' => 'Belum ada guru aktif. Undang guru dulu di menu Guru.',
        ],
        'grade_option' => 'Kelas :grade',
        'no_homeroom' => 'Belum ada wali kelas',
        'year' => 'Tahun ajaran :name',
        'assigned' => ':assigned dari :total mapel sudah ada gurunya',
        'actions' => [
            'create' => 'Tambah kelas',
            'save' => 'Simpan',
            'edit' => 'Ubah',
            'delete' => 'Hapus kelas',
            'timetable' => 'Jadwal',
            'back' => 'Kembali ke daftar kelas',
        ],
        'dialogs' => [
            'delete' => 'Hapus kelas :name? Pembagian guru di kelas ini ikut terhapus.',
        ],
        'empty' => [
            'no_year' => 'Belum ada tahun ajaran. Tambahkan tahun ajaran dulu.',
            'classes' => 'Belum ada kelas di tahun ajaran ini.',
        ],
        'toasts' => [
            'saved' => 'Kelas :name tersimpan.',
            'deleted' => 'Kelas :name dihapus.',
        ],
        'errors' => [
            'year_missing' => 'Pilih tahun ajaran yang ada.',
            'teacher_missing' => 'Pilih guru aktif dari sekolah ini.',
            'name_taken' => 'Nama kelas ini sudah dipakai di tahun ajaran yang sama.',
        ],
    ],
    'assignments' => [
        'title' => 'Guru per mata pelajaran',
        'hint' => 'Mapel guru kelas otomatis diajar wali kelas. Mapel lain menunggu Anda memilih guru mapelnya. Setiap mapel bisa diganti gurunya.',
        'columns' => [
            'subject' => 'Mata pelajaran',
            'jp' => 'JP/minggu',
            'teacher' => 'Guru',
        ],
        'jp_per_week' => ':jp JP/minggu',
        'default' => 'Bawaan: :teacher',
        'no_teacher' => 'Belum ada guru',
        'inactive' => ':name (tidak aktif lagi)',
        'badges' => [
            'optional' => 'Pilihan sekolah',
            'not_in_report' => 'Tidak masuk rapor',
            'not_offered' => 'Tidak diajarkan lagi',
        ],
        'actions' => [
            'clear' => 'Kosongkan',
        ],
        'empty' => 'Belum ada mata pelajaran untuk tingkat ini. Hubungi Tim SIGRAK bila ini keliru.',
        'toasts' => [
            'saved' => 'Guru :subject tersimpan.',
        ],
        'errors' => [
            'subject_missing' => 'Mata pelajaran ini tidak diajarkan di kelas ini.',
        ],
    ],
];
