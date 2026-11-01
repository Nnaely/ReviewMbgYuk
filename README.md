# Pondasi Laravel MBG

Kerangka awal Laravel 12 untuk Mini Project Proyek 3 Kasus A MBG.
Saat ini tersedia struktur Laravel serta pondasi data sekolah, distribusi makanan,
dan kolom peran pengguna.
Halaman welcome Laravel menyediakan tautan masuk dan tombol keluar untuk pengguna
terautentikasi. Pelapor dapat mengirim laporan dasar; pengelolaan sekolah dan
penanganan lanjutan laporan belum tersedia.

## Kebutuhan

- PHP dan ekstensi sesuai `composer.lock` (diuji pada PHP 8.5).
- Composer 2.
- MySQL/MariaDB untuk tahap pengembangan data berikutnya.
- Node.js dan npm jika ingin menjalankan Vite.

## Menjalankan

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan serve
```

Buka http://127.0.0.1:8000. Pada Linux/macOS, gunakan
`cp .env.example .env` untuk menyalin konfigurasi.

Halaman welcome dapat dibuka tanpa database karena session dan cache memakai file.
Untuk frontend dengan Vite, jalankan `npm install` lalu `npm run dev` di terminal lain.

## Database

Contoh konfigurasi MySQL tersedia di `.env.example`. Buat database
`mbg_komunitas` dan sesuaikan kredensial di `.env` saat diperlukan.
Kemudian jalankan `php artisan migrate`.

Tabel users bawaan Laravel diperluas melalui migrasi kolom peran. Model User masih
bawaan Laravel. Seeder utama mengisi data sekolah di Bandung dan distribusi menu simulasi,
serta akun demo untuk ketiga peran. Login dan logout memakai sesi Laravel, dengan
pembatasan lima percobaan masuk per menit.

## Pondasi data sekolah

- Migration membuat tabel `schools` dengan `id`, `name`, `address`, dan timestamps.
- Model `App\Models\School` digunakan untuk menyimpan dan membaca data sekolah.
- `SchoolSeeder` mengisi SDN 035 Soka, SMP Negeri 2 Bandung, dan SMAN 3 Bandung
  beserta alamat sekolah. Seeder memperbarui nama sekolah contoh lama saat dijalankan.

Setelah database MySQL/MariaDB tersedia dan kredensial `.env` sesuai, jalankan:

```powershell
php artisan migrate
php artisan db:seed
php artisan tinker
```

Di dalam Tinker, baca data sekolah dengan:

```php
\App\Models\School::all(['id', 'name', 'address']);
```

Nama dan alamat sekolah dalam seeder mengikuti data referensi Dapodik. Tahap ini belum menambahkan
controller, route, formulir, atau tampilan khusus sekolah.

## Pondasi data distribusi

- Tabel `distributions` menyimpan sekolah tujuan (`school_id`), tanggal distribusi
  (`distribution_date`), keterangan menu (`menu_description`), dan timestamps.
- Model `Distribution` memiliki relasi `school()`, sedangkan `School` memiliki
  relasi `distributions()`. Tanggal distribusi dikonversi menjadi objek tanggal.
- `DistributionSeeder` mengisi dua jadwal menu simulasi untuk masing-masing dari tiga
  sekolah di Bandung. Seeder utama menjalankan `SchoolSeeder` terlebih dahulu, lalu
  `DistributionSeeder` melalui `php artisan db:seed`.
- `UserSeeder` menyiapkan `pelapor@mbg.test`, `pemantau@mbg.test`, dan
  `koordinator@mbg.test` dengan peran masing-masing. Ketiganya memakai sandi
  `password`. Menjalankan ulang seeder memperbarui ketiga akun demo tersebut.

Buka `http://127.0.0.1:8000/login` untuk masuk setelah menjalankan `php artisan db:seed`.
- Mengulangi seeder dengan data contoh yang sama tidak menggandakan distribusi.
  Tanggal dan menu hanya simulasi, bukan catatan distribusi nyata.

Di dalam Tinker, baca distribusi beserta sekolahnya dengan:

```php
\App\Models\Distribution::with('school')->get();
```

Daftar distribusi dapat dibuka oleh pemantau dan koordinator melalui dasbor masing-masing.
Halaman ini menampilkan sekolah tujuan, tanggal distribusi, dan keterangan menu dari data
yang sudah tersimpan; pengelolaan atau penambahan distribusi belum tersedia.

## Pelaporan dasar

Pelapor dapat memilih sekolah, mengisi laporan, dan melihat laporan yang pernah dikirim
melalui `/laporan`. Setiap laporan tersimpan dengan status awal `baru` dan terhubung ke
akun pelapor serta sekolah. Pemantau dan koordinator dapat melihat daftar laporan masuk
serta halaman detailnya melalui dasbor. Status dapat diperbarui menjadi `baru`, `diproses`,
atau `selesai`, dan terlihat oleh pelapor di halaman laporannya. Daftar laporan dapat
difilter berdasarkan sekolah dan status.

## Migrasi peran pengguna

Migrasi menambahkan kolom `role` pada tabel `users` dengan tiga nilai:
`pelapor`, `pemantau`, dan `koordinator`. Peran default adalah `pelapor`, termasuk
untuk pengguna yang sudah tersimpan sebelum migrasi dijalankan. Nilai di luar
ketiga peran ditolak oleh batasan kolom database.

Jalankan `php artisan migrate` setelah konfigurasi database sesuai. Rollback migrasi
ini menghapus kolom `role` beserta nilai perannya, tetapi mempertahankan data akun
lainnya. Tahap ini menyediakan struktur data peran dan akun demo; login dan
pembatasan akses akan dikerjakan terpisah.

## Pemeriksaan

```powershell
composer validate --strict
composer check-platform-reqs
php artisan test
```

Dua test bawaan Laravel memeriksa respons halaman welcome dan contoh unit test.
Tiga test sekolah memeriksa penyimpanan dan pembacaan melalui model, pengulangan
seeder tanpa duplikasi, serta pemanggilan seeder utama tanpa membuat akun.
Empat test distribusi memeriksa penyimpanan dan relasi sekolah, penolakan referensi
sekolah yang tidak tersedia, pembatasan penghapusan sekolah yang memiliki distribusi,
serta pengulangan seeder tanpa duplikasi atau perubahan data sekolah lain.
Lima test peran memeriksa nilai default, penyimpanan tiga peran, penolakan nilai
lain, pemberian peran kepada akun lama, dan rollback tanpa menghapus akun.

Test database menggunakan SQLite di memori yang terpisah dari database aplikasi,
sehingga tidak mengubah data MySQL lokal. Pastikan ekstensi `pdo_sqlite` tersedia.
Pengujian ini belum memverifikasi koneksi atau perilaku khusus MySQL/MariaDB.
Jangan commit `.env`, token, atau kredensial pribadi.

## Commit pondasi

1. Kerangka Laravel
2. Konfigurasi Lingkungan
3. Kompatibilitas PHP
4. Panduan Instalasi

## Commit pondasi sekolah

1. Migrasi Sekolah
2. Model Sekolah
3. Data Contoh Sekolah
4. Pengujian Data Sekolah

## Commit pondasi distribusi

1. Migrasi Distribusi
2. Model Distribusi
3. Data Contoh Distribusi
4. Pengujian Distribusi

## Commit migrasi peran

Pengembangan memakai alur test terlebih dahulu. Commit pertama berisi test yang
memang gagal karena kolom `role` belum tersedia. Commit kedua menyediakan kolom
dan nilai default, tetapi test pembatasan peran masih gagal. Commit ketiga
melengkapi batasan nilai peran hingga ketiga test lulus. Commit keempat menambahkan
pemeriksaan akun lama, rollback, serta dokumentasi. Kegagalan pada commit awal
merupakan tahap pengujian yang disengaja dan dapat dijalankan ulang.

1. Pengujian Peran Pengguna
2. Migrasi Peran Pengguna
3. Pembatasan Peran Pengguna
4. Pengujian Migrasi Peran
