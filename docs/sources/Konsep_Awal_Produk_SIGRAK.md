# Konsep Awal Produk SIGRAK

30 Okt 2026 · disalin dari pesan pemilik produk (teks asli; hanya sisa tempelan seperti angka "14" dan "10" yang dihapus)

## Pengguna dan akses

Siswa bukan pengguna. Pengguna hanya guru, kepala sekolah yang utama, dan semua halaman tidak ada halaman publik, akses via login.

Nama brand yang sudah sementara: SIGRAK.

Guru seharusnya tidak perlu menjadi ahli prompt engineering. Guru cukup menjawab pertanyaan yang memang mereka pahami secara pedagogis.

## Konsep

Yang kamu rencanakan sebenarnya bukan sekadar fitur AI generator, melainkan AI-assisted educational content creation platform: sebuah platform yang membantu guru menghasilkan materi pendidikan melalui alur terpandu, template siap pakai, dan AI sebagai mesin pembuat konten.

Ada tiga komponen penting yang perlu dibangun:

1. Guided Generator — wizard multi-step untuk menerjemahkan kebutuhan guru menjadi spesifikasi yang jelas.
2. Template Engine — kumpulan template buatan pengembang yang bisa dipilih guru.
3. AI Provider Engine — lapisan khusus untuk mengelola Gemini, API key, rotasi key, kuota, retry, dan hasil generate.

Laravel bisa menangani ketiganya. Saya justru menyarankan agar kamu membuat wrapper khusus Gemini, tetapi wrapper tersebut jangan melekat langsung pada setiap fitur aplikasi.

### 1. Multi-step generator: jangan meminta guru membuat prompt

Ini keputusan desain yang sangat bagus.

### 2. Template Engine: pengembang menyediakan hasil siap pakai

Ini sebaiknya menjadi fitur tersendiri, bukan hanya kumpulan prompt.

Saya menyarankan ada dua jenis generator:

- **A. Guided Generator** — Fleksibel. Guru menentukan sendiri spesifikasi melalui wizard.
- **B. Developer Templates** — Siap digunakan. Pengembang menyediakan template yang sudah mempunyai struktur, desain, aturan, dan spesifikasi hasil.

Misalnya pengembang menyediakan:

| Template | Hasil |
| --- | --- |
| Quiz Battle | Game kuis dengan skor dan timer |
| Drag & Drop | Game mencocokkan objek |
| Word Puzzle | Teka-teki kata |
| Maze Challenge | Game labirin |
| Flashcard | Kartu belajar interaktif |
| Modul Ajar | Dokumen pembelajaran |
| LKPD | Lembar kerja peserta didik |
| Bank Soal | Kumpulan soal terstruktur |

Guru tinggal memilih template, mengisi materi, lalu generate.

Template jangan hanya menyimpan prompt. Sebaiknya satu template mempunyai beberapa komponen:

```
Template
├── Metadata
├── Wizard Configuration
├── Input Schema
├── Prompt Instructions
├── Output Schema
├── Design System
├── Validation Rules
├── Rendering Engine
└── Version
```

Contohnya, template Quiz Battle mempunyai desain antarmuka dan mekanisme permainan yang sudah ditentukan pengembang.

AI hanya bertugas mengisi konten soal, jawaban, penjelasan, dan elemen lain yang memang perlu dibuat.

Ini jauh lebih konsisten daripada meminta Gemini membuat seluruh game dari nol setiap kali.

Bahkan, saya menyarankan pendekatan hybrid:

- 70–90% struktur game berasal dari template Vanilla JS buatan pengembang.
- AI menghasilkan konten dan konfigurasi game.
- AI hanya menghasilkan kode tambahan jika guru meminta fitur yang belum tersedia.

Angka tersebut merupakan target desain, bukan hasil benchmark.

Dengan demikian, game yang dibuat guru akan mempunyai standar kualitas, responsivitas, dan perilaku yang lebih konsisten.

### 3. Apakah perlu wrapper khusus Gemini?

Saya rekomendasikan: Ya. Tetapi jangan membuat ulang seluruh SDK Gemini.

Buat lapisan abstraksi milik aplikasi yang menjadi penghubung antara fitur pendidikan dan provider AI.

Laravel sendiri sekarang sudah mempunyai Laravel AI SDK dengan dukungan Gemini, structured output, agents, dan queue.

Namun, untuk kebutuhanmu, saya tetap menyarankan wrapper internal.

Struktur misalnya:

```
app/
├── AI/
│   ├── Contracts/
│   │   └── AIProviderInterface.php
│   │
│   ├── Providers/
│   │   ├── GeminiProvider.php
│   │   └── GeminiClient.php
│   │
│   ├── KeyManager/
│   │   ├── APIKeyManager.php
│   │   ├── KeySelector.php
│   │   └── KeyHealthChecker.php
│   │
│   ├── Generation/
│   │   ├── PromptBuilder.php
│   │   ├── SpecificationBuilder.php
│   │   ├── OutputValidator.php
│   │   └── OutputRepairer.php
│   │
│   └── Templates/
│       ├── TemplateResolver.php
│       └── TemplateVersionManager.php
│
├── Jobs/
│   ├── GenerateModule.php
│   ├── GenerateQuestions.php
│   └── GenerateGame.php
│
└── Services/
    ├── PDFService.php
    └── GameRendererService.php
```

Manfaatnya:

- Kalau Google mengubah API, kamu cukup menyesuaikan adapter.
- Kalau suatu saat ingin menambahkan OpenAI atau provider lain, tidak perlu mengubah seluruh modul.
- Semua fitur menggunakan mekanisme API key yang sama.
- Logging, retry, rotasi key, dan validasi output berada di satu tempat.

### 4. Multi API key dengan sistem Round Robin

Ini bagian yang menarik dari rencanamu.

Guru dapat menambahkan beberapa API key yang berasal dari beberapa Google Cloud Project miliknya.

Misalnya:

Pengaturan AI Guru — API Key yang terdaftar: 3 aktif

| Project | Akun | Status |
| --- | --- | --- |
| Project Pendidikan A | Akun Google 1 | Aktif |
| Project Pendidikan B | Akun Google 2 | Aktif |
| Project Pendidikan C | Akun Google 3 | Aktif |

Strategi pemilihan key: **Round Robin + Failover** — memilih project secara bergantian dan beralih ketika key atau project mengalami kegagalan sementara.

Namun, ada satu hal penting mengenai Gemini:

Kuota Gemini berlaku per Google Cloud Project, bukan per API key. Dua API key dari project yang sama tetap berbagi kuota.

Artinya:

| Konfigurasi | Hasil |
| --- | --- |
| 3 key dari 1 project | Tetap satu kuota |
| 3 key dari 3 project berbeda | Masing-masing mempunyai kuota project sendiri |
| 3 key dari 3 akun/project berbeda | Bisa mempunyai batas penggunaan terpisah sesuai tier masing-masing |

Google AI Studio saat ini juga mencantumkan batas jumlah project yang dapat dibuat melalui halaman Projects-nya.

Saya menyarankan round robin digunakan untuk distribusi beban dan failover yang sah, bukan untuk mengakali batas penggunaan Free Tier atau menyamarkan penggunaan lintas akun. Setiap project harus benar-benar dikelola dan diotorisasi pemiliknya, serta tetap mengikuti ketentuan Google.

#### Algoritma pemilihan key yang lebih baik

Jangan menggunakan round robin sederhana tanpa pemeriksaan status.

Gunakan kombinasi:

1. Round robin untuk distribusi normal.
2. Health check untuk mengetahui key yang valid.
3. Cooldown jika terkena 429.
4. Failover jika project mengalami gangguan.
5. Circuit breaker jika project berulang kali gagal.
6. Sticky selection untuk satu proses generate agar seluruh tahap tetap konsisten.

Contoh:

```
Generate Game
      |
      v
Key Selector
      |
      v
Project A
      |
   429 Error
      |
      v
Cooldown Project A
      |
      v
Project B
      |
      v
Generate berhasil
```

Jangan mengulang request secara membabi buta. Jika gagal karena INVALID_ARGUMENT, misalnya, berpindah API key tidak akan menyelesaikan masalah.

Dan jika request timeout setelah berhasil diproses provider, perlu diperhatikan kemungkinan request ganda ketika retry.
