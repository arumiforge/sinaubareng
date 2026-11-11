# Mesin Penyedia AI

<p class="source-note">Diterjemahkan dari <code>docs/final/ai-provider-engine.md</code> pada 10 November 2026.</p>

- **Status:** final (disetujui 2026-10-31, D-123)
- **Tugas:** P1-04
- **Terakhir diperbarui:** 2026-11-10 (pengaturan model dan adapter di konsol D-315, D-326; keunikan job D-050; privasi asesmen D-213, D-295, D-333, D-347, D-350, D-369, D-370)
- **Sumber:** `Konsep_Awal_Produk_SIGRAK.md`, `Catatan_Pengembang_Aplikasi_Lama_Design_System_Data_dan_AI.md` (di `docs/sources/`)
- **Fakta dicek pada 2026-10-30** terhadap halaman Google tentang [batas laju](https://ai.google.dev/gemini-api/docs/rate-limits), [pemecahan masalah](https://ai.google.dev/gemini-api/docs/troubleshooting), [galat API](https://ai.google.dev/gemini-api/docs/api-errors), [Gemini API Additional Terms](https://ai.google.dev/gemini-api/terms), dan [Google APIs Terms](https://developers.google.com/terms), serta [dokumentasi AI SDK](https://laravel.com/docs/ai-sdk) Laravel.

## Tujuan

Dokumen ini merinci satu-satunya lapisan yang berkomunikasi dengan penyedia AI: kunci Gemini milik guru, pemilihan kunci dengan round robin dan failover, penanganan galat, percobaan ulang, pengamanan dari permintaan ganda, dan log pemakaian. Dokumen ini untuk pengembang. Mesin konten memanggil lapisan ini lewat satu interface, seperti dijelaskan di `content-engine.md`.

## Cakupan: hanya paket gratis Gemini

SIGRAK **hanya mendukung API key Google Gemini, dan dirancang untuk paket gratis** (kuota tanpa bayar) (D-023). Kunci guru dari penyedia AI lain tidak diterima. Kunci Gemini berbayar secara teknis bisa dipakai, tetapi tidak ada yang dirancang untuknya. Interface `AIProvider` tetap ada, karena memisahkan kode Gemini dan memungkinkan tes memakai penyedia palsu.

**Penyedia di konsol untuk Tim SIGRAK (D-326).** Selain Gemini, `app/AI/` mendapat dua adapter untuk pekerjaan Tim SIGRAK sendiri: **kompatibel OpenAI** (base URL, kunci, model; mencakup OpenAI, OpenRouter, DeepSeek) dan **Anthropic**. Di konsol, admin dapat menguji kunci dari penyedia mana pun di antara ini dan memilihnya sebagai model atau cadangan per purpose (lihat bagian Model dan permintaan). Kunci guru tetap hanya Gemini (D-023); kunci guru dari penyedia lain ditunda ke usulan-02, setelah setiap templat diuji per penyedia. Sebelum purpose yang dipakai untuk pembuatan konten guru memakai kunci Tim SIGRAK, pemilik memutuskan siapa yang membayarnya.

Merancang untuk paket gratis berarti:

- **Batas rendah, per project.** Setiap pembuatan konten memakai panggilan sesedikit mungkin. Langkah digabung bila ukuran keluarannya memungkinkan. Tidak ada ping di latar belakang, tidak ada pembuatan ulang otomatis, dan tidak ada panggilan spekulatif.
- **Hanya model stabil.** Model preview dan eksperimental punya batas lebih ketat, dan ketentuan Google melarang memakainya di produksi.
- **Data yang dikirim bisa dibaca manusia.** Lihat bagian Aturan privasi.
- **Hanya orang dewasa.** Ketentuan Gemini mewajibkan pengguna API berusia 18+, dan melarang pemakaian API di layanan yang ditujukan untuk, atau kemungkinan dipakai oleh, orang di bawah 18 tahun. Pengguna AI di SIGRAK adalah guru. Situs ujian TKA yang dipakai murid tidak pernah memanggil AI (D-027).

## Sasaran

1. Setiap fitur konten memakai jalur AI yang sama. Logging, percobaan ulang, rotasi kunci, dan pemeriksaan keluaran ada di satu tempat.
2. Bila Google mengubah API-nya, hanya adapter Gemini yang berubah. Menambah penyedia lain nanti tidak menyentuh kode konten.
3. Kunci guru tidak pernah keluar dari server dan tidak pernah muncul di log.
4. Percobaan ulang tidak pernah berjalan membabi buta. Tidak pernah mengulang yang tidak mungkin berhasil, dan tidak pernah diam-diam menggandakan biaya permintaan.

## Fakta penting tentang kuota Gemini

- **Batas laju berlaku per Google Cloud project, bukan per API key.** Dua kunci dari satu project berbagi satu kuota.
- Batas diukur sebagai request per menit (RPM), token masukan per menit (TPM), dan request per hari (RPD), **per model**. Batasnya bergantung pada tingkat pemakaian project (Free, Tier 1–3).
- **Kuota harian (RPD) direset tengah malam waktu Pasifik.** Dalam WIB itu pukul 14.00 saat Amerika memakai waktu musim panas, dan pukul 15.00 di luar itu.
- Kelayakan paket gratis, penagihan, dan pembatasan dinilai **per project**. Kunci baru di project yang dibatasi tidak membantu.
- `models.list` hanya membuktikan bahwa kunci ada. Itu **tidak** membuktikan project bisa menjalankan inferensi (masalah tier atau penagihan tetap gagal), jadi uji kunci harus berupa panggilan pembuatan yang sangat kecil.
- Gemini 3.6 dan sesudahnya mengabaikan `temperature`, `top_p`, dan `top_k`, dan Google menyatakan akan mulai mengembalikan 400 untuk parameter itu. Templat tidak boleh mengaturnya.

| Kunci milik guru | Kuota efektif |
|---|---|
| 3 kunci dari 1 project | Satu kuota bersama |
| 3 kunci dari 3 project | Tiga kuota terpisah, satu per project |
| 3 kunci dari 3 akun/project | Kuota terpisah, masing-masing sesuai tier-nya |

**Ketentuan pemakaian dan risiko (D-023).** Ketentuan API Google menyatakan pengguna "will not attempt to circumvent" batas API yang terdokumentasi. Di paket gratis, merotasi beberapa project milik satu guru pada dasarnya melipatgandakan kuota gratis guru itu, sehingga Google bisa menganggapnya sebagai upaya mengakali batas dan membatasi atau menangguhkan project atau akun tersebut. Pemilik memutuskan untuk tetap memakai rotasi multi-kunci, **dengan risiko ditanggung guru**:

- Menambah kunci kedua (atau seterusnya) wajib mencentang kotak persetujuan: "Saya memahami bahwa memakai beberapa project untuk menambah kuota gratis dapat melanggar ketentuan Google, dan Google dapat membatasi atau menangguhkan project saya."
- Halaman kunci dan teks bantuan mengulang hal ini dengan jelas. Setiap kunci harus berasal dari project milik guru itu sendiri.
- SIGRAK tidak pernah membuatkan project atau kunci Google untuk guru, tidak pernah menyarankan membuat akun tambahan, dan tidak pernah membagikan kunci antarguru.

## Arsitektur

Struktur folder usulan pemilik dipertahankan, dengan satu perubahan: bagian khusus pendidikan (templat dan pipeline pembuatan) pindah ke `app/Content/` (lihat `content-engine.md`, bagian Anatomi templat). Dengan begitu lapisan AI bebas dari logika pendidikan. Nama kelas mengikuti konvensi Laravel (`Contracts\AIProvider`, bukan `AIProviderInterface`).

```
app/AI/
├── Contracts/
│   └── AIProvider.php            # generate(AIRequest): AIResponse, throws AIException
├── Data/
│   ├── AIRequest.php             # purpose, system + content parts, JSON schema, limits, timeout, trace ids
│   ├── AIResponse.php            # parsed JSON/text, finish reason, tokens in/out, model, latency, credential id
│   └── AIErrorClass.php          # enum, see Error handling
├── Providers/Gemini/
│   ├── GeminiProvider.php        # AIRequest → generateContent body; response/error → AIResponse/AIErrorClass
│   └── GeminiClient.php          # thin client on Laravel's HTTP client; key passed per call
├── Providers/OpenAICompatible/   # console only, Tim SIGRAK keys (D-326)
├── Providers/Anthropic/          # console only, Tim SIGRAK keys (D-326)
├── KeyManager/
│   ├── ApiKeyManager.php         # add, test, disable, delete; the encryption boundary
│   ├── KeySelector.php           # round robin + filters + sticky per generation
│   └── KeyHealthChecker.php      # state changes: cooldown, circuit breaker, invalid
├── Privacy/PromptDataGuard.php   # blocks student personal data from reaching a prompt
├── AIGateway.php                 # the only entry point used by app/Content: select key → call → classify → retry/failover → log
└── Usage/UsageLogger.php         # writes ai_usage_logs

app/Jobs/Generation/Generate{Rpm,BankSoal,Lkpd,QuizBattle,TkaPackage}.php, ReviseSection.php
                                  # one job per content type (D-024), all using GenerationRunner
```

**Mengapa memakai klien sendiri, bukan Laravel AI SDK** (diterima, D-116). Laravel menyediakan AI SDK resmi (`laravel/ai`, v1.0 dirilis September 2026) dengan Gemini, keluaran terstruktur, dan antrean. SDK itu ditujukan untuk agen dengan satu kunci yang dikonfigurasi per penyedia. Kebutuhan utama kita adalah memilih kunci berbeda untuk setiap panggilan, membaca status HTTP mentah dan detail galat untuk klasifikasi, dan mengendalikan percobaan ulang sendiri. Klien tipis di atas `Http::` membuat semua itu eksplisit dan mudah dites. Interface `AIProvider` membuat pilihan ini bisa dibatalkan: SDK nanti bisa menjadi transport di dalam `GeminiProvider` tanpa menyentuh pemanggilnya.

## Kunci

**Kepemilikan (D-012, D-023).** Kunci adalah kunci Gemini paket gratis milik guru (`user_id`), tidak pernah milik sekolah, dan pembuatan konten guru tidak memakai kunci platform. Kunci milik Tim SIGRAK hanya ada untuk tombol uji di konsol dan pekerjaan Tim SIGRAK sendiri (D-315, D-326), dan purpose untuk guru baru memakainya setelah pemilik memutuskan siapa yang membayar. Kepala sekolah tidak butuh kunci kecuali membuat konten. Batas bawaan: 5 kunci per guru (konfigurasi). Kunci kedua dan seterusnya butuh persetujuan seperti dijelaskan di atas.

**Menambah kunci** (*Kunci AI Saya*, juga ditanyakan langsung saat guru pertama kali memakai fitur AI):

1. Guru menempelkan kunci dan mengisi **nama project** (wajib) serta, bila mau, **akun Google** asalnya. Keduanya label yang diketik guru, karena kunci tidak menunjukkan project-nya.
2. Server membuat panggilan uji yang sangat kecil. Bila lolos, kunci disimpan sebagai `active`. Bila gagal, guru melihat alasannya dengan kata-kata sederhana dan tidak ada yang disimpan.
3. Bila kunci yang sama sudah tersimpan (fingerprint sama), kunci ditolak.
4. Bila nama project sama dengan kunci yang sudah ada, guru mendapat peringatan: "Kunci dari project yang sama berbagi kuota, jadi tidak menambah batas pemakaian."
5. Untuk kunci kedua dan seterusnya, kotak persetujuan (Ketentuan pemakaian dan risiko) harus dicentang, dan waktu persetujuan disimpan.
6. Halaman bantuan memandu cara membuat kunci di Google AI Studio dengan akun Gmail (atau belajar.id bila admin domain mengizinkan AI Studio). Halaman itu menjelaskan batas paket gratis, waktu reset harian dalam WIB, dan bahwa Google bisa membaca prompt paket gratis.

**Penyimpanan dan pengiriman.**

- Rahasia disimpan di `ai_credentials.secret` dengan cast `encrypted` Laravel (APP_KEY; dirotasi dengan `APP_PREVIOUS_KEYS`). Fingerprint (`HMAC-SHA256(secret, app key)`) mendeteksi duplikat, dan `secret_last4` disimpan untuk ditampilkan.
- **Hanya tulis.** Rahasia tidak pernah dikirim ke browser (prop Inertia hanya membawa label, empat karakter terakhir, dan status) dan tidak pernah didekripsi di luar `ApiKeyManager`/`GeminiClient`.
- Dikirim sebagai header `x-goog-api-key`, **tidak pernah** di query string URL (aplikasi lama menaruhnya di query string).
- Tidak pernah dicatat di log. Logging klien HTTP tidak menyertakan header, dan Telescope menyembunyikan `x-goog-api-key` (`Telescope::hideRequestHeaders`).
- Otorisasi: hanya pembuatan konten milik pemiliknya yang boleh memakai sebuah kunci. Kepala sekolah, operator, pengawas, dan staf bisa melihat bahwa seorang guru punya kunci beserta statusnya, tidak pernah rahasianya.

**Status kunci**

| Status | Arti | Bisa dipilih | Keluar dari status lewat |
|---|---|---|---|
| `active` | Sehat | Ya | Galat (di bawah) |
| cooldown (per model) | Kena batas laju pada satu model | Tidak untuk model itu, sampai `until` | Waktu berlalu |
| circuit terbuka | Kegagalan sementara berulang | Tidak, sampai jendelanya habis, lalu satu probe | Probe berhasil menutupnya; probe gagal membukanya lagi dengan jendela dua kali lipat |
| `invalid` | Kunci ditolak, project dibatasi, masalah penagihan/kredit | Tidak | Guru memperbaikinya lalu menekan "Uji lagi" |
| `disabled` | Dimatikan guru | Tidak | Guru menyalakannya |

Cooldown disimpan **per (kunci, model)** karena kuota berlaku per project per model: batas harian satu model tidak menghalangi model lain. Cooldown ada di Redis dengan TTL dan dicerminkan ke `ai_credential_cooldowns` agar halaman kunci bisa menampilkan "Kuota harian habis sampai pukul 14.00".

## Pemilihan kunci

Algoritmenya menggabungkan round robin, penyaringan kesehatan, cooldown, failover, circuit breaker, dan pemilihan lengket (sticky):

```
select(user, model, generation):
    if generation.pinned_key is usable for model:          # sticky selection
        return generation.pinned_key
    candidates = user's keys ordered by id
                 where state = active
                   and not cooling down for model
                   and circuit closed (or half-open with its probe slot free)
    if candidates is empty:
        fail NoUsableKey(earliest_available_at)             # teacher sees when to retry
    i = INCR redis "ai:rr:{user_id}"  mod  len(candidates)  # round robin
    key = candidates[i]
    generation.pin(key)
    return key
```

- **Pemilihan lengket.** Semua langkah dalam satu pembuatan memakai kunci yang disematkan, sehingga RPM multi-langkah dibuat oleh satu project dan satu keluarga model. Sematan hanya berubah saat failover.
- **Failover.** Bila sebuah percobaan gagal dengan galat kelas failover, status kunci diperbarui lebih dulu. Lalu `select` dijalankan lagi tanpa kunci itu, dan kunci baru disematkan.
- **Batas konkurensi.** Paling banyak 2 pembuatan berjalan per guru (`Redis::funnel`), agar satu guru tidak menghabiskan seluruh kuotanya sendiri dengan job paralel.

## Penanganan galat

Galat Gemini diklasifikasikan sekali di `GeminiProvider`, dan gateway bertindak berdasarkan kelasnya.

| Sinyal | Kelas | Ulang di kunci sama | Failover | Perubahan status kunci | Yang dilihat guru |
|---|---|---|---|---|---|
| 429, batas per menit | `rate_limited` | Sekali, bila jeda yang disarankan ≤ 20 dtk | Ya (selain itu) | Cooldown (kunci, model) sepanjang jeda, atau 60 dtk | Tidak ada, kecuali semua kunci gagal |
| 429, batas per hari | `rate_limited_daily` | Tidak | Ya | Cooldown (kunci, model) sampai tengah malam Pasifik berikutnya | Halaman kunci: "Kuota harian project … habis sampai pukul …" |
| 400 `INVALID_ARGUMENT` | `bad_request` | Tidak | **Tidak** (kunci lain tidak memperbaiki request yang salah) | Tidak ada | "Belum berhasil dibuat. Tim Sigrak sudah diberi tahu." Galat dilaporkan ke tim |
| 400 `FAILED_PRECONDITION` (penagihan, paket gratis tidak tersedia di wilayah itu) | `project_unavailable` | Tidak | Ya | `invalid` | Halaman kunci menjelaskan, dengan tautan ke AI Studio |
| 401 / 403 `PERMISSION_DENIED` (kunci salah, bocor, atau diblokir; project dibatasi) | `key_invalid` | Tidak | Ya | `invalid` | Notifikasi: "Kunci AI '…' tidak bisa dipakai lagi" |
| 402 (kredit prabayar habis; hanya project berbayar, seharusnya tidak terjadi di paket gratis) | `project_unavailable` | Tidak | Ya | `invalid` | Halaman kunci menjelaskan |
| 404 model tidak ditemukan | `model_unavailable` | Tidak | Model berikutnya dalam daftar purpose, kunci sama | Tidak ada | Tidak ada |
| 408, 500, 503 | `transient` | Sampai 2 kali, jeda 1 dtk → 2 dtk + jitter | Ya, setelah itu | Dihitung untuk circuit breaker | Tidak ada, kecuali semua kunci gagal |
| 504 atau timeout klien | `timeout` | Sekali (lihat bagian permintaan ganda) | Ya, setelah itu | Dihitung untuk circuit breaker | Tidak ada, kecuali semua kunci gagal |
| Diblokir: `safety`, `prohibited_content`, `blocklist`, `recitation`, `spii`, … | `blocked` | Tidak | Tidak | Tidak ada | "Isi yang diminta tidak bisa dibuat. Ubah topik atau catatan, lalu coba lagi." |
| JSON tidak valid, tidak cocok dengan skema, `MAX_TOKENS` | `malformed_output` | Diteruskan ke `OutputRepairer` | Tidak | Tidak ada | Hanya bila perbaikan gagal |

Yang perlu dipastikan dengan respons sungguhan saat implementasi:

- Cara membedakan 429 per menit dari per hari. Rencananya membaca id kuota di detail galat (`QuotaFailure`) dan jeda yang disarankan (`RetryInfo.retryDelay`).
- Nama status yang persis untuk `generateContent`.

**Anggaran.** Paling banyak 4 percobaan per langkah di semua kunci, dan paling lama 5 menit per langkah. Bila anggaran habis, langkah gagal dengan pesan paling berguna yang ditemui. Misalnya, bila semua kunci sedang cooldown, pesannya menyebut waktu paling awal untuk mencoba lagi.

**Circuit breaker** (per kunci; nilai konfigurasi):

- Terbuka setelah 5 kegagalan `transient`/`timeout` berturut-turut dalam 10 menit dan tetap terbuka selama 5 menit.
- Setelah itu satu request probe diizinkan. Bila berhasil, circuit tertutup dan penghitung direset. Bila gagal, circuit terbuka lagi dengan jendela dua kali lipat, paling lama 1 jam.

**Pemeriksaan kesehatan.**

- Setiap kunci dites saat ditambahkan, dan saat guru menekan "Uji kunci" atau "Uji lagi".
- Selain itu, kesehatan diketahui dari lalu lintas sungguhan. Tidak ada ping terjadwal, karena setiap ping adalah pembuatan sungguhan yang memakai kuota guru.

## Timeout dan permintaan ganda

`generateContent` Gemini tidak punya idempotency key. Panggilan yang habis waktu di sisi kita mungkin tetap selesai, dan ditagih, di sisi Google. Aturannya:

1. **Timeout klien per purpose** dibuat longgar (misalnya 180 dtk untuk langkah RPM yang panjang, 90 dtk untuk lainnya), dan timeout job serta `retry_after` antrean diatur di atasnya.
2. **Saat timeout, ulangi sekali di kunci yang sama sebelum failover.** Biaya ganda yang mungkin terjadi tetap di project yang sama, dan pemilihan lengket tetap terjaga.
3. **Setiap percobaan dicatat** dengan nomor percobaannya, sehingga duplikat terlihat di riwayat pemakaian.
4. **Hasil sebuah langkah diterima tepat sekali.** `generation_steps` unik pada (`generation_id`, `step_key`) dan berpindah `running → succeeded` dengan compare-and-set, sehingga duplikat yang terlambat tidak bisa menimpa keluaran yang sudah diterima.
5. **Tanpa percobaan ulang di tingkat antrean.** Setiap job pembuatan punya `tries = 1` dan memakai `ShouldBeUniqueUntilProcessing` ditambah middleware `WithoutOverlapping`, keduanya berdasarkan id generation (D-050). `ShouldBeUnique` biasa akan menghalangi job mengirim ulang dirinya untuk langkah berikutnya. Percobaan ulang terjadi di dalam gateway dengan aturan di atas. Saat dilanjutkan, langkah yang sudah `succeeded` dilewati.
6. **Klik ganda.** Tombol *Buat* mengirim client token (UUID). `generations` unik pada (`user_id`, `client_token`), sehingga kiriman ganda hanya membuat satu generation.
7. **Worker yang mati.** Penyapu terjadwal mencari langkah yang tertahan di `running` lebih lama dari timeout + margin, menandai percobaannya `unknown` di log, dan melanjutkan pembuatan dari langkah itu. Kemungkinan biaya ganda dicatat.

## Model dan request

- Setiap **purpose** (misalnya `document_long`, `document_short`, `game_content`, `repair`) punya model aktif dan daftar cadangan berurutan. Templat menyebut purpose, tidak pernah model. Admin mengaturnya per purpose (bukan per templat) di konsol, dan disimpan di database; `config/ai.php` hanya berisi nilai awal. Setiap perubahan dicatat (D-315, mengubah D-117). Tombol uji di konsol memanggil model dengan kunci Tim SIGRAK, tidak pernah kunci guru. Bawaannya **Gemini 3.8 Flash** (keluaran teks; paket gratis 5 RPM, 250 ribu TPM, 20 RPD per project, AI Studio 2026-10-31; D-117). Batasnya dicek ulang saat build dan saat Google mengubahnya.
- Keluaran terstruktur: `responseMimeType: application/json` dengan skema keluaran templat di kolom JSON schema yang didukung versi API saat ini.
- Jangan kirim `temperature`, `top_p`, atau `top_k` (lihat Fakta penting).
- Catatan guru dan teks kurikulum dimasukkan ke dalam blok data yang dibatasi dengan jelas. Instruksi hanya berasal dari templat. Keluaran hanya diterima lewat validasi skema. Inilah pengaman terhadap prompt injection.

## Antrean dan kemajuan

- Antrean: `ai` (pembuatan), `exports` (PDF/DOCX/HTML), `default`. Horizon memberi `ai` supervisor sendiri, sehingga panggilan AI yang lama tidak pernah menghambat ekspor atau email.
- **Satu kelas job per jenis konten** (D-024): `GenerateRpm`, `GenerateBankSoal`, `GenerateLkpd`, `GenerateQuizBattle`, `GenerateTkaPackage`, ditambah `ReviseSection`. Masing-masing menetapkan rencana langkah, timeout, dan label kemajuannya sendiri, lalu menyerahkan mekanismenya ke `GenerationRunner` bersama. Sebuah job menjalankan satu langkah yang tertunda, lalu mengirim ulang dirinya sampai pembuatan selesai, sehingga setiap jalannya tetap singkat dan bisa dilanjutkan.
- Status pembuatan: `queued → running (langkah i dari n) → succeeded | failed | cancelled`.
- UI melakukan polling setiap 2–3 dtk lewat reload parsial Inertia selama halamannya terbuka, dan notifikasi database masuk ke *Kabar* saat pembuatan selesai (D-152). WebSocket (Reverb) menyusul, hanya bila polling terbukti terlalu berat.

## Aturan privasi

Prompt dan respons paket gratis dipakai Google untuk memperbaiki produknya, dan peninjau manusia bisa membacanya. Ketentuan Google sendiri meminta agar informasi sensitif, rahasia, atau pribadi tidak dikirim ke layanan tanpa bayar. Karena itu:

1. **Data pribadi murid tidak pernah masuk ke prompt:** tanpa nama, NISN, tanggal lahir, absensi, catatan, atau nilai perorangan yang terkait identitas. `PromptDataGuard` memeriksa setiap request yang disusun dari data sekolah, dan kode pembuatan hanya menerima data murid dalam bentuk yang diizinkan di bawah.
2. **Draf deskripsi rapor** (Asesmen > Rapor) hanya mengirim tingkat penguasaan per TP untuk murid anonim ("Murid A"). Nama sebenarnya digabungkan setelah pembuatan, di server kita.
3. **Kolom catatan guru** menampilkan petunjuk: "Jangan tulis nama atau data pribadi murid." (D-210) Catatan berupa teks bebas, jadi ini panduan, bukan jaminan.
4. **Data kurikulum, jawaban guru, dan konten yang dihasilkan** boleh dikirim. Semuanya tidak memuat data pribadi.
5. Situs ujian (TKA) menyimpan kredensial dan jawaban murid dan **tidak pernah** memanggil AI.
6. **Draf catatan wali kelas** hanya memakai jumlah kehadiran, jenis catatan, dan hasil per TP, tidak pernah nama atau data pribadi; wali kelas menyunting dan menyetujuinya (D-213).
7. **Kelompok belajar** per TP sampai ke wizard RPM berikutnya hanya sebagai jumlah per kelompok (perlu bimbingan, cukup, siap pengayaan), tidak pernah nama (D-295).
8. **AI asesmen** membuat instrumen, menandai miskonsepsi, dan menyusun draf umpan balik dari pola kelas (D-347). Nama di kertas dibaca dengan pengenalan teks di perangkat guru dan dicocokkan dengan daftar murid di sana. Pindai LJK membaca bulatan jawaban di browser guru dengan pustaka OMR JavaScript; hanya jawaban dan tingkat keyakinannya yang sampai ke server, dan foto tidak diunggah (D-369). Jawaban tulisan tangan Uraian/Isian dibaca di ponsel; hanya teks jawaban, diberi kode `nomor absen-nomor soal` (misalnya `12-3`) dan tanpa nama, yang dikirim ke AI bersama kunci jawaban atau rubrik. AI menyarankan skor dan umpan balik, guru yang memutuskan, dan SIGRAK memetakan kode itu kembali ke murid (D-370, target P5-08 setelah Pindai LJK).
9. **Hasil sensitif** (sosial-emosional, kebutuhan khusus, minat atau bakat) tidak pernah dikirim ke AI (D-350).
10. **Konteks daerah** (*Konteks Daerah*): AI hanya memakai butir dari profil sekolah dan daftar awal yang sudah ditinjau, lalu guru mengonfirmasinya; AI tidak pernah mengarang fakta lokal (D-333).

## Log pemakaian dan visibilitas

`ai_usage_logs` mendapat satu baris per percobaan: pengguna, sekolah, id kunci (tidak pernah kuncinya), generation, langkah, nomor percobaan, model, purpose, kelas galat, status HTTP, token masukan/keluaran, latensi, dan waktu. Teks prompt dan respons **tidak** dicatat. Spesifikasi dan keluaran yang diterima pada generation sudah cukup untuk membangun ulang dan men-debug.

| Siapa | Melihat |
|---|---|
| Guru | *Kunci AI Saya*: status tiap kunci, cooldown, keberhasilan terakhir, hitungan harian, kegagalan terbaru dengan alasan sederhana |
| Kepala sekolah | Total per guru: jumlah pembuatan, kegagalan karena masalah kunci. Tanpa kunci, tanpa detail per kunci |
| Tim SIGRAK | Galat per kelas dan templat di Filament, ditambah Horizon. Telescope hanya di lokal/staging. Log perubahan model per purpose di konsol (D-315) |

Masa simpan (D-118): baris rinci selama 1 bulan, ringkasan bulanan disimpan.

## Pengujian

- **Penyedia palsu** yang mengimplementasikan `AIProvider`, dengan respons dan urutan galat yang sudah ditulis. Feature test mencakup pemilihan, penyematan lengket, setiap baris tabel galat, berakhirnya cooldown, circuit breaker, dan aturan permintaan ganda.
- **Contract test** untuk `GeminiProvider` terhadap respons Gemini yang direkam (berhasil, 429 per menit dan per hari, 400, 403, 503, diblokir, keluaran rusak).
- Satu **smoke test langsung yang bersifat pilihan** dengan kunci sungguhan, tidak pernah di CI.

## Pertanyaan terbuka

- [ ] Pastikan kolom detail 429 yang membedakan batas per menit dari per hari, dengan respons sungguhan.
- [x] Masa simpan log pemakaian: 1 bulan (D-118).
- [x] Kunci cadangan: ya, hanya dipakai bila semua kunci biasa tidak tersedia (D-119).
- [x] Model bawaan Gemini 3.8 Flash dengan batas paket gratisnya (D-117); dicek ulang saat build.
