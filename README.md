# RuangKarya — Versi GitHub Pages

Versi statis yang dapat dipublikasikan melalui GitHub Pages. File PHP dari versi asal tidak dapat dijalankan langsung oleh GitHub Pages, sehingga halaman utama dan detail dikonversi ke HTML + JavaScript.

## Cara memasang
1. Buat repository GitHub baru (atau backup repository lama terlebih dahulu).
2. Unggah seluruh isi folder ini ke root repository, sehingga `index.html` berada di root.
3. Buka **Settings → Pages**.
4. Pada **Build and deployment**, pilih **Deploy from a branch**, branch `main`, folder `/(root)`, lalu Save.
5. Tunggu deployment selesai dan buka alamat Pages yang diberikan GitHub.

## Isi
- `index.html`: galeri dengan pencarian dan filter kategori.
- `project.html`: halaman detail karya.
- `submit.html`: formulir yang menyiapkan data untuk disalin; tidak mengirim ke database.
- `projects.json`: data karya yang tampil di galeri.
- `uploads/`: gambar sampul.
- `admin/login.html`: penjelasan batasan portal admin pada GitHub Pages.

## Mengubah data galeri
Edit `projects.json`. Setiap karya memiliki `id`, `title`, `student_name`, `student_class`, `category`, `description`, `cover_image`, `demo_url`, `project_url`, dan `created_at`. Pastikan alamat gambar relatif sesuai, misalnya `uploads/sampul.jpg`.

## Batasan penting
GitHub Pages hanya untuk konten statis. Formulir tidak menyimpan data ke server, login admin dan moderasi tidak aktif, dan file upload dari pengunjung tidak dapat disimpan ke repository secara otomatis. Untuk fitur tersebut, gunakan hosting PHP/MySQL atau integrasikan backend seperti Google Apps Script/Formspree. Jangan unggah kredensial database atau kata sandi ke repository publik.
