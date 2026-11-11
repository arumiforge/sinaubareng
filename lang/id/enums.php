<?php

declare(strict_types=1);

return [
    'school_level' => [
        'sd' => 'SD',
        'mi' => 'MI',
    ],
    'membership_role' => [
        'principal' => 'Kepala sekolah',
        'operator' => 'Operator sekolah',
        'teacher' => 'Guru',
    ],
    'national_holiday_kind' => [
        'national_holiday' => 'Libur nasional',
        'joint_leave' => 'Cuti bersama',
    ],
    'calendar_event_kind' => [
        'holiday' => 'Libur sekolah',
        'exam' => 'Asesmen',
        'school_event' => 'Kegiatan sekolah',
        'no_lessons' => 'Tidak ada pelajaran',
    ],
    'exam_type' => [
        'sts' => 'STS',
        'sas' => 'SAS',
        'other' => 'Asesmen lain',
    ],
    'calendar_change_action' => [
        'create' => 'Usulan acara baru',
        'update' => 'Usulan perubahan acara',
        'delete' => 'Usulan hapus acara',
    ],
    'calendar_change_status' => [
        'pending' => 'Menunggu persetujuan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ],
    'calendar_status' => [
        'draft' => 'Draf',
        'active' => 'Aktif',
    ],
    'timetable_slot_kind' => [
        'lesson' => 'Pelajaran',
        'break' => 'Istirahat',
        'ceremony' => 'Upacara',
        'activity' => 'Kegiatan',
    ],
    'membership_status' => [
        'active' => 'Aktif',
        'ended' => 'Berakhir',
    ],
    'invitation_status' => [
        'pending' => 'Menunggu',
        'accepted' => 'Diterima',
        'expired' => 'Kedaluwarsa',
    ],
    'staff_role' => [
        'admin' => 'Admin',
        'support' => 'Dukungan',
        'content' => 'Konten',
    ],
    'curriculum_version_status' => [
        'draft' => 'Draf',
        'active' => 'Aktif',
        'retired' => 'Tidak dipakai lagi',
    ],
    'subject_kind' => [
        'compulsory' => 'Wajib',
        'local_content' => 'Muatan lokal',
        'cocurricular' => 'Kokurikuler',
    ],
    'default_teacher' => [
        'class_teacher' => 'Guru kelas',
        'subject_teacher' => 'Guru mapel',
    ],
    'subject_school_level' => [
        'sd' => 'SD',
        'mi' => 'MI',
        'both' => 'SD dan MI',
    ],
    'religion' => [
        'islam' => 'Islam',
        'kristen' => 'Kristen',
        'katolik' => 'Katolik',
        'hindu' => 'Hindu',
        'buddha' => 'Buddha',
        'khonghucu' => 'Khonghucu',
    ],
    'phase' => [
        'A' => 'Fase A',
        'B' => 'Fase B',
        'C' => 'Fase C',
    ],
    'learning_type' => [
        'intracurricular' => 'Intrakurikuler',
        'cocurricular' => 'Kokurikuler',
    ],
];
