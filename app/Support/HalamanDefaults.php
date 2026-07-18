<?php

namespace App\Support;

class HalamanDefaults
{
    public static function items(): array
    {
        return [
            [
                'slug' => 'contact',
                'title' => 'Hubungi Auraquina',
                'eyebrow' => 'Layanan Pelanggan',
                'description' => 'Saluran resmi untuk pertanyaan pesanan, ukuran, dan bantuan belanja Auraquina.',
                'sections' => [
                    ['heading' => 'Kontak Utama', 'body' => [
                        'WhatsApp admin: +62 877-1151-6373.',
                        'Email: hello@auraquina.com.',
                        'Lokasi operasional: Malang, East Java, Indonesia.',
                    ]],
                    ['heading' => 'Jam Layanan', 'body' => [
                        'Senin sampai Sabtu, pukul 09.00 - 17.00 WIB.',
                        'Pesan yang masuk di luar jam kerja akan dibalas pada hari kerja berikutnya.',
                    ]],
                ],
            ],
            [
                'slug' => 'shipping-policy',
                'title' => 'Kebijakan Pengiriman',
                'eyebrow' => 'Ketentuan Pengiriman',
                'description' => 'Informasi pengiriman, waktu proses, dan tanggung jawab penerimaan pesanan.',
                'sections' => [
                    ['heading' => 'Waktu Proses Pesanan', 'body' => [
                        'Pesanan Auraquina diproses pada hari kerja setelah data checkout lengkap dan pembayaran berhasil diterima atau terverifikasi oleh sistem.',
                        'Waktu proses normal adalah 1 sampai 3 hari kerja sebelum pesanan diserahkan kepada kurir, tergantung antrean operasional, pengecekan stok, dan kesiapan pengemasan.',
                        'Pesanan yang masuk pada hari Minggu, hari libur nasional, atau di luar jam operasional akan mulai diproses pada hari kerja berikutnya.',
                    ]],
                    ['heading' => 'Verifikasi Pembayaran', 'body' => [
                        'Pesanan baru dapat masuk ke tahap pengemasan setelah pembayaran dinyatakan berhasil oleh payment gateway, bank, atau sistem pembayaran yang digunakan saat checkout.',
                        'Apabila pembayaran belum selesai, kedaluwarsa, gagal, atau belum terverifikasi, pesanan dapat tetap berstatus menunggu pembayaran dan belum diproses untuk pengiriman.',
                        'Jika dana sudah terdebit tetapi status pesanan belum berubah, pelanggan dapat menghubungi customer care dengan nomor pesanan dan bukti transaksi agar kami dapat membantu pengecekan sesuai prosedur pihak pembayaran terkait.',
                    ]],
                    ['heading' => 'Estimasi Kurir dan Area Pengiriman', 'body' => [
                        'Estimasi pengiriman mengikuti layanan kurir yang dipilih saat checkout dan dihitung sejak paket diterima oleh kurir, bukan sejak pesanan dibuat.',
                        'Durasi pengiriman dapat berbeda untuk setiap kota, kabupaten, kecamatan, atau area tertentu sesuai jaringan dan ketentuan operasional kurir.',
                        'Informasi ongkir dan estimasi yang tampil di checkout merupakan estimasi dari sistem kurir atau layanan logistik dan dapat berubah mengikuti kondisi pengiriman aktual.',
                    ]],
                    ['heading' => 'Tanggung Jawab Alamat Penerima', 'body' => [
                        'Pelanggan bertanggung jawab memastikan nama penerima, nomor telepon, alamat lengkap, kecamatan, kota atau kabupaten, provinsi, kode pos, dan catatan pengiriman sudah benar sebelum menyelesaikan pesanan.',
                        'Auraquina tidak bertanggung jawab atas keterlambatan, retur otomatis, kehilangan kesempatan pengantaran, atau biaya tambahan yang timbul karena alamat tidak lengkap, nomor tidak aktif, penerima tidak dapat dihubungi, atau kesalahan data yang diberikan pelanggan.',
                        'Perubahan alamat hanya dapat dibantu selama pesanan belum diproses, belum dikemas, dan belum diserahkan kepada kurir.',
                    ]],
                    ['heading' => 'Nomor Resi dan Pelacakan', 'body' => [
                        'Nomor resi akan diberikan atau diperbarui pada detail pesanan setelah paket diserahkan kepada kurir dan data pengiriman tersedia di sistem.',
                        'Pelanggan dapat menggunakan nomor resi untuk memantau perjalanan paket melalui halaman pelacakan kurir atau kanal resmi yang tersedia.',
                        'Pada periode tertentu, pembaruan status resi dapat membutuhkan waktu karena sinkronisasi sistem kurir, antrean pickup, atau proses sortir di gudang kurir.',
                    ]],
                    ['heading' => 'Keterlambatan Kurir dan Force Majeure', 'body' => [
                        'Keterlambatan akibat kepadatan pengiriman, kendala operasional kurir, cuaca buruk, bencana, gangguan sistem, kebijakan wilayah, hari libur, atau keadaan di luar kendali Auraquina dapat terjadi sewaktu-waktu.',
                        'Auraquina akan membantu melakukan pengecekan dan komunikasi yang wajar dengan kurir apabila pelanggan melaporkan kendala pengiriman, namun keputusan investigasi, klaim, atau kompensasi mengikuti ketentuan kurir yang berlaku.',
                        'Risiko paket setelah diterima kurir akan ditangani berdasarkan hasil pelacakan dan bukti dari pihak kurir, dengan tetap mengutamakan bantuan yang adil bagi pelanggan.',
                    ]],
                    ['heading' => 'Bantuan Customer Care', 'body' => [
                        'Jika paket belum bergerak, status resi tidak jelas, alamat perlu dikonfirmasi, atau paket diterima dalam kondisi bermasalah, segera hubungi customer care Auraquina melalui kanal resmi.',
                        'Mohon sertakan nomor pesanan, nama penerima, nomor resi, dan bukti pendukung seperti foto paket agar tim kami dapat membantu pengecekan dengan lebih cepat dan akurat.',
                        'Laporan yang masuk di luar jam layanan akan ditangani pada hari kerja berikutnya sesuai antrean customer care.',
                    ]],
                ],
            ],
            [
                'slug' => 'return-exchange',
                'title' => 'Retur & Penukaran',
                'eyebrow' => 'Layanan Purna Jual',
                'description' => 'Panduan pengajuan penukaran, retur, dan penanganan kendala pasca pembelian.',
                'sections' => [
                    ['heading' => 'Kelayakan Retur dan Penukaran', 'body' => [
                        'Pelanggan dapat mengajukan retur, penukaran, atau komplain purna jual apabila produk yang diterima tidak sesuai pesanan, terdapat indikasi cacat produksi, terjadi kesalahan pengiriman, atau ukuran perlu ditukar sesuai ketersediaan stok dan ketentuan Auraquina.',
                        'Setiap pengajuan wajib terhubung dengan nomor pesanan yang valid dan produk yang dibeli melalui kanal resmi Auraquina.',
                        'Penukaran ukuran atau model hanya dapat diproses apabila produk pengganti tersedia dan pengajuan memenuhi syarat kondisi barang.',
                    ]],
                    ['heading' => 'Batas Waktu Pengajuan', 'body' => [
                        'Pengajuan retur, penukaran, atau komplain harus dilakukan maksimal 7 hari kalender sejak pesanan diterima oleh pelanggan berdasarkan status pengiriman atau bukti penerimaan paket.',
                        'Pengajuan yang melewati batas waktu tersebut dapat ditolak karena kondisi produk, riwayat pemakaian, dan bukti kejadian sudah tidak dapat diverifikasi secara memadai.',
                        'Untuk menjaga proses tetap jelas, pelanggan disarankan memeriksa paket segera setelah diterima sebelum melepas label atau menggunakan produk.',
                    ]],
                    ['heading' => 'Bukti yang Diperlukan', 'body' => [
                        'Pelanggan wajib menyertakan nomor pesanan, foto produk yang jelas, foto label atau tag, foto kemasan luar dan dalam, serta penjelasan kendala yang dialami.',
                        'Untuk klaim barang rusak, kurang, salah item, atau tidak sesuai pesanan, Auraquina dapat meminta video unboxing tanpa jeda sebagai bukti pendukung agar proses verifikasi lebih akurat.',
                        'Bukti yang buram, tidak lengkap, telah diedit secara berlebihan, atau tidak menunjukkan hubungan dengan pesanan dapat membuat pengajuan membutuhkan waktu tambahan atau tidak dapat disetujui.',
                    ]],
                    ['heading' => 'Kondisi Produk', 'body' => [
                        'Produk yang diajukan harus dalam kondisi bersih, belum dipakai, belum dicuci, belum disetrika ulang secara berlebihan, tidak berbau, tidak terkena noda, tidak rusak karena penggunaan, dan label atau kelengkapan masih tersedia.',
                        'Produk wajib dikembalikan bersama kemasan dan kelengkapan yang relevan apabila pengajuan disetujui oleh tim Auraquina.',
                        'Auraquina berhak menolak pengajuan apabila kondisi produk yang dikirim kembali berbeda dari bukti awal atau tidak memenuhi syarat kebersihan dan kelengkapan.',
                    ]],
                    ['heading' => 'Kondisi yang Tidak Dapat Diretur', 'body' => [
                        'Retur atau penukaran tidak berlaku untuk produk yang sudah dipakai, dicuci, terkena parfum atau bau tertentu, terkena noda, rusak karena penggunaan pelanggan, label hilang, atau tidak dikembalikan dalam kondisi layak verifikasi.',
                        'Perbedaan warna yang wajar akibat pencahayaan foto, pengaturan layar, atau batch produksi tidak selalu menjadi dasar retur apabila produk masih sesuai deskripsi utama.',
                        'Toleransi ukuran manual, perubahan preferensi pribadi setelah produk diterima, kesalahan memilih ukuran, atau kesalahan alamat tidak otomatis menjadi dasar pengembalian dana, kecuali disetujui berdasarkan hasil review Auraquina.',
                    ]],
                    ['heading' => 'Proses Review', 'body' => [
                        'Setelah pengajuan diterima, tim Auraquina akan meninjau bukti, riwayat pesanan, status pengiriman, dan ketersediaan produk pengganti jika diperlukan.',
                        'Tim kami dapat menghubungi pelanggan melalui WhatsApp, email, atau kanal resmi lain untuk meminta informasi tambahan sebelum memberikan keputusan.',
                        'Keputusan retur, penukaran, perbaikan solusi, atau refund mengikuti hasil verifikasi Auraquina berdasarkan bukti yang tersedia dan ketentuan purna jual yang berlaku.',
                    ]],
                    ['heading' => 'Biaya Kirim, Refund, dan Penyelesaian', 'body' => [
                        'Biaya pengiriman kembali dan pengiriman ulang akan ditentukan berdasarkan penyebab pengajuan, hasil verifikasi, dan kesepakatan dengan customer care sebelum produk dikirim kembali.',
                        'Apabila kendala terbukti berasal dari kesalahan Auraquina atau cacat produksi yang terverifikasi, Auraquina dapat membantu penggantian produk, penukaran, atau penyelesaian lain yang sesuai dengan kondisi pesanan.',
                        'Refund, jika disetujui, akan diproses melalui metode yang diinformasikan oleh customer care dan dapat membutuhkan waktu sesuai prosedur internal, payment gateway, bank, atau penyedia layanan pembayaran terkait.',
                    ]],
                ],
            ],
            [
                'slug' => 'faq',
                'title' => 'Pertanyaan Umum',
                'eyebrow' => 'Tanya Jawab',
                'description' => 'Jawaban ringkas untuk pertanyaan yang paling sering ditanyakan pelanggan Auraquina.',
                'sections' => [
                    ['heading' => 'Apakah bisa belanja tanpa akun?', 'body' => [
                        'Bisa. Pelanggan dapat checkout sebagai guest selama mengisi data pesanan, alamat, nomor telepon, dan email dengan benar.',
                        'Pastikan data kontak aktif karena informasi pembayaran, status pesanan, dan pengiriman akan dikirim melalui kanal yang tersedia.',
                    ]],
                    ['heading' => 'Apakah saya perlu membuat akun?', 'body' => [
                        'Akun pelanggan membantu Anda melihat riwayat pesanan dan mengakses informasi transaksi dengan lebih mudah apabila fitur tersebut tersedia.',
                        'Jika berbelanja sebagai guest, simpan nomor pesanan, email, atau tautan detail pesanan agar customer care dapat membantu pengecekan saat dibutuhkan.',
                    ]],
                    ['heading' => 'Metode pembayaran apa saja yang tersedia?', 'body' => [
                        'Metode pembayaran mengikuti pilihan yang tampil saat checkout, seperti virtual account, QRIS, kartu, e-wallet, atau metode lain yang diaktifkan melalui payment gateway.',
                        'Setiap metode pembayaran dapat memiliki batas waktu, biaya, dan instruksi berbeda. Ikuti instruksi pembayaran yang ditampilkan sampai transaksi dinyatakan berhasil.',
                    ]],
                    ['heading' => 'Bagaimana jika pembayaran gagal tetapi dana terdebit?', 'body' => [
                        'Jika dana sudah terdebit tetapi status pesanan belum berubah, segera hubungi customer care Auraquina dengan nomor pesanan dan bukti transaksi.',
                        'Tim kami akan membantu pengecekan sesuai data yang tersedia dan prosedur payment gateway atau bank terkait. Waktu penyelesaian dapat mengikuti ketentuan penyedia pembayaran.',
                    ]],
                    ['heading' => 'Bagaimana mengecek status pesanan?', 'body' => [
                        'Setelah order dibuat, Anda dapat membuka detail pesanan dari tautan yang diberikan, melalui akun jika login, atau menghubungi customer care dengan nomor pesanan.',
                        'Status pesanan dapat mencakup menunggu pembayaran, dibayar, diproses, dikirim, selesai, dibatalkan, atau status lain sesuai alur transaksi di website.',
                    ]],
                    ['heading' => 'Berapa lama pesanan diproses?', 'body' => [
                        'Pesanan diproses setelah pembayaran berhasil diterima atau terverifikasi. Waktu proses normal adalah 1 sampai 3 hari kerja sebelum paket diserahkan kepada kurir.',
                        'Pada periode promo, peluncuran koleksi, akhir pekan, atau hari libur, proses dapat membutuhkan waktu lebih lama sesuai antrean operasional.',
                    ]],
                    ['heading' => 'Bagaimana jika ukuran tidak sesuai?', 'body' => [
                        'Silakan baca panduan ukuran dan detail produk sebelum checkout. Jika produk sudah diterima dan ukuran tidak sesuai, Anda dapat mengajukan penukaran sesuai Kebijakan Return & Exchange.',
                        'Pengajuan harus dilakukan dalam batas waktu yang ditentukan, produk belum dipakai atau dicuci, dan stok ukuran pengganti masih tersedia.',
                    ]],
                    ['heading' => 'Apakah warna produk selalu sama seperti foto?', 'body' => [
                        'Auraquina berupaya menampilkan warna produk seakurat mungkin, tetapi perbedaan dapat terjadi karena pencahayaan foto, pengaturan layar, batch produksi, atau karakter bahan.',
                        'Perbedaan warna minor yang masih wajar tidak selalu menjadi dasar retur, kecuali terdapat kesalahan produk yang jelas dan terverifikasi.',
                    ]],
                    ['heading' => 'Bagaimana menggunakan voucher atau promo?', 'body' => [
                        'Masukkan kode voucher pada kolom yang tersedia saat checkout dan pastikan syarat promo terpenuhi sebelum menyelesaikan pesanan.',
                        'Voucher dapat memiliki batas periode, kuota, minimum transaksi, kategori produk, metode pembayaran, dan ketentuan lain. Voucher yang sudah kedaluwarsa atau tidak memenuhi syarat tidak dapat digunakan.',
                    ]],
                    ['heading' => 'Bagaimana menghubungi admin Auraquina?', 'body' => [
                        'Anda dapat menghubungi customer care melalui WhatsApp +62 877-1151-6373 atau email hello@auraquina.com.',
                        'Agar pengecekan lebih cepat, sertakan nomor pesanan, nama pemesan, kendala yang dialami, dan bukti pendukung seperti foto, video, atau bukti pembayaran bila relevan.',
                    ]],
                ],
            ],
            [
                'slug' => 'about',
                'title' => 'Tentang Auraquina',
                'eyebrow' => 'Tentang Kami',
                'description' => 'Auraquina menghadirkan modest fashion dengan siluet tenang, material nyaman, dan estetika yang timeless.',
                'sections' => [
                    ['heading' => 'Brand Story', 'body' => [
                        'Auraquina dirancang untuk perempuan yang menemukan keindahan dalam kesederhanaan.',
                        'Setiap koleksi berangkat dari kebutuhan akan busana yang mudah dipakai, tetap anggun, dan relevan lebih lama.',
                    ]],
                    ['heading' => 'Filosofi Produk', 'body' => [
                        'Kami memilih palet warna yang lembut, potongan yang bersih, dan detail yang tidak berlebihan.',
                        'Fokus kami adalah kenyamanan, kepraktisan, dan rasa percaya diri saat dipakai setiap hari.',
                    ]],
                ],
            ],
            [
                'slug' => 'product-care',
                'title' => 'Panduan Pemeliharaan Produk',
                'eyebrow' => 'Petunjuk Perawatan',
                'description' => 'Ikuti panduan ini untuk menjaga kualitas, warna, dan keawetan bahan pakaian Auraquina Anda.',
                'sections' => [
                    ['heading' => 'Cara Mencuci', 'body' => [
                        'Gunakan air dingin atau air bersuhu ruang untuk mencuci pakaian. Hindari penggunaan air panas agar serat kain tidak menyusut.',
                        'Cuci pakaian dengan warna senada untuk menghindari luntur. Disarankan mencuci secara manual (hand wash) atau menggunakan putaran lambat (delicate cycle) jika menggunakan mesin cuci.',
                        'Gunakan detergen cair yang lembut dan hindari pemutih pakaian agar warna alami kain tetap terjaga.',
                    ]],
                    ['heading' => 'Cara Menjemur', 'body' => [
                        'Jemur pakaian dengan posisi dibalik (bagian dalam di luar) untuk melindunginya dari paparan sinar matahari langsung yang dapat memudarkan warna.',
                        'Hindari memeras pakaian terlalu kuat agar serat dan bentuk pakaian tidak rusak.',
                        'Cukup gantung atau bentangkan pakaian di tempat yang teduh dengan sirkulasi udara yang baik.',
                    ]],
                    ['heading' => 'Cara Menyetrika', 'body' => [
                        'Setrika pakaian dengan suhu rendah hingga sedang, menyesuaikan dengan jenis bahan.',
                        'Gunakan kain pelapis (alas setrika) atau balik pakaian saat menyetrika untuk mencegah efek mengilap pada permukaan bahan pakaian.',
                        'Untuk pakaian berbahan rentan atau pleated (berlipat), disarankan menggunakan setrika uap (steamer).',
                    ]],
                    ['heading' => 'Cara Menyimpan', 'body' => [
                        'Simpan pakaian dalam keadaan benar-benar kering untuk menghindari jamur dan bau lembap.',
                        'Gantung pakaian menggunakan gantungan baju yang tebal atau berbusa untuk menjaga bentuk bahu, atau lipat dengan rapi di lemari pakaian yang sejuk dan kering.',
                    ]],
                ],
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Kebijakan Privasi',
                'eyebrow' => 'Kebijakan Privasi',
                'description' => 'Penjelasan singkat tentang data yang kami kumpulkan dan cara Auraquina menggunakannya.',
                'sections' => [
                    ['heading' => 'Data yang Dikumpulkan', 'body' => [
                        'Auraquina mengumpulkan data yang Anda berikan saat mengakses website, membuat akun, checkout, melakukan pembayaran, menghubungi customer care, atau menggunakan fitur lain yang tersedia.',
                        'Data tersebut dapat mencakup nama, email, nomor telepon, alamat pengiriman, alamat penagihan, detail pesanan, catatan pengiriman, riwayat komunikasi, dan informasi lain yang diperlukan untuk memproses transaksi.',
                        'Kami juga dapat mengumpulkan data teknis yang wajar seperti alamat IP, informasi perangkat, aktivitas website, dan log sistem untuk keamanan, pemeliharaan layanan, serta pencegahan penyalahgunaan.',
                    ]],
                    ['heading' => 'Data Transaksi dan Pembayaran', 'body' => [
                        'Transaksi pembayaran diproses melalui payment gateway atau penyedia layanan pembayaran yang tersedia saat checkout. Auraquina dapat menerima informasi status pembayaran, metode pembayaran, nomor referensi transaksi, waktu transaksi, nominal pembayaran, dan status verifikasi.',
                        'Data sensitif pembayaran seperti PIN, kata sandi perbankan, atau detail autentikasi tertentu tidak diminta oleh Auraquina dan diproses sesuai sistem penyedia pembayaran terkait.',
                        'Pelanggan wajib menjaga kerahasiaan OTP, kode pembayaran, tautan transaksi, dan informasi pribadi lain yang digunakan untuk menyelesaikan pembayaran.',
                    ]],
                    ['heading' => 'Penggunaan Data', 'body' => [
                        'Data pelanggan digunakan untuk membuat dan mengelola pesanan, memverifikasi pembayaran, mengirim produk, menerbitkan invoice atau notifikasi transaksi, menangani retur atau komplain, dan memberikan layanan pelanggan.',
                        'Data juga dapat digunakan untuk menjaga keamanan website, mencegah transaksi mencurigakan, menyelesaikan sengketa, memenuhi kewajiban administrasi, serta mengirim informasi yang relevan dengan pesanan atau layanan Auraquina.',
                        'Apabila Auraquina mengirim informasi promosi, pelanggan dapat menghubungi customer care untuk meminta penyesuaian komunikasi sesuai kanal yang tersedia.',
                    ]],
                    ['heading' => 'Pembagian Data kepada Mitra Layanan', 'body' => [
                        'Auraquina dapat membagikan data yang diperlukan kepada payment gateway, bank, penyedia layanan pembayaran, kurir, penyedia teknologi, layanan hosting, layanan komunikasi, atau mitra operasional lain yang membantu pemenuhan transaksi.',
                        'Pembagian data dilakukan sejauh diperlukan untuk memproses pembayaran, mengirim pesanan, mengirim notifikasi, menjaga keamanan sistem, menyelesaikan kendala pelanggan, atau memenuhi ketentuan hukum yang berlaku.',
                        'Mitra layanan tersebut hanya menerima data yang relevan dengan fungsi masing-masing dan tunduk pada prosedur keamanan atau kebijakan privasi mereka sendiri.',
                    ]],
                    ['heading' => 'Perlindungan Data', 'body' => [
                        'Auraquina berupaya menjaga kerahasiaan data pelanggan melalui pembatasan akses, pengelolaan sistem yang wajar, dan penggunaan penyedia layanan yang mendukung keamanan transaksi.',
                        'Akses internal terhadap data pelanggan dibatasi untuk kebutuhan operasional, layanan pelanggan, pemenuhan pesanan, administrasi, dan keamanan.',
                        'Meskipun kami berupaya melindungi data dengan langkah yang wajar, pelanggan juga perlu menjaga keamanan perangkat, akun, email, nomor telepon, dan informasi transaksi pribadi.',
                    ]],
                    ['heading' => 'Penyimpanan Data', 'body' => [
                        'Data pelanggan disimpan selama diperlukan untuk tujuan transaksi, layanan pelanggan, administrasi, pencegahan penyalahgunaan, audit internal, atau pemenuhan kewajiban hukum dan pembukuan yang berlaku.',
                        'Apabila data tidak lagi diperlukan, Auraquina dapat menghapus, menonaktifkan, atau menyimpan data dalam bentuk yang dibatasi sesuai kebutuhan operasional dan ketentuan yang berlaku.',
                        'Riwayat transaksi tertentu dapat tetap disimpan untuk keperluan bukti pesanan, penyelesaian sengketa, pelaporan, atau rekonsiliasi pembayaran.',
                    ]],
                    ['heading' => 'Hak Pelanggan', 'body' => [
                        'Pelanggan dapat menghubungi Auraquina untuk meminta bantuan terkait akses, koreksi, pembaruan, atau pertanyaan mengenai data pribadi yang digunakan dalam transaksi.',
                        'Permintaan akan ditinjau berdasarkan kecocokan identitas, hubungan dengan pesanan, kebutuhan operasional, dan ketentuan hukum yang berlaku.',
                        'Untuk keamanan, Auraquina dapat meminta informasi tambahan sebelum memproses permintaan yang berkaitan dengan data pelanggan atau riwayat transaksi.',
                    ]],
                    ['heading' => 'Tidak Menjual Data Pelanggan', 'body' => [
                        'Auraquina tidak menjual data pribadi pelanggan kepada pihak ketiga.',
                        'Data pelanggan hanya digunakan dan dibagikan untuk tujuan yang relevan dengan layanan, transaksi, pengiriman, pembayaran, keamanan, komunikasi pelanggan, atau kewajiban hukum yang berlaku.',
                    ]],
                    ['heading' => 'Kontak Privasi', 'body' => [
                        'Jika Anda memiliki pertanyaan tentang Kebijakan Privasi, penggunaan data, pembayaran, atau riwayat pesanan, silakan hubungi customer care Auraquina melalui WhatsApp +62 877-1151-6373 atau email hello@auraquina.com.',
                        'Mohon sertakan nama, nomor pesanan jika ada, dan penjelasan permintaan agar tim kami dapat membantu pengecekan dengan lebih tepat.',
                    ]],
                ],
            ],
            [
                'slug' => 'terms-conditions',
                'title' => 'Syarat & Ketentuan',
                'eyebrow' => 'Syarat & Ketentuan',
                'description' => 'Ketentuan penggunaan website, akun, transaksi, pembayaran, pengiriman, dan layanan purna jual Auraquina.',
                'sections' => [
                    ['heading' => 'Ringkasan dan Persetujuan Penggunaan', 'body' => [
                        'Dengan mengakses website Auraquina, membuat akun, menelusuri produk, atau melakukan transaksi, Anda dianggap telah membaca, memahami, dan menyetujui Syarat & Ketentuan ini.',
                        'Auraquina menyediakan website ini untuk membantu pelanggan memperoleh informasi produk modest fashion, melakukan pemesanan, pembayaran, pelacakan pesanan, dan komunikasi layanan pelanggan secara aman dan tertib.',
                        'Jika Anda tidak menyetujui sebagian atau seluruh ketentuan ini, mohon untuk tidak menggunakan website atau layanan transaksi Auraquina.',
                    ]],
                    ['heading' => 'Perubahan Ketentuan dan Website', 'body' => [
                        'Auraquina dapat memperbarui Syarat & Ketentuan, konten website, fitur, harga, promosi, atau informasi operasional sewaktu-waktu untuk menyesuaikan kebutuhan layanan.',
                        'Perubahan berlaku sejak ditampilkan di website, kecuali dinyatakan lain. Anda disarankan membaca halaman ini secara berkala sebelum bertransaksi.',
                        'Auraquina juga dapat melakukan pemeliharaan, pembatasan akses sementara, atau penyesuaian fitur tanpa mengurangi pemenuhan pesanan yang telah dikonfirmasi sesuai ketentuan yang berlaku.',
                    ]],
                    ['heading' => 'Hak Cipta dan Kekayaan Intelektual', 'body' => [
                        'Seluruh nama, logo, foto, video, desain, teks, katalog, tampilan website, dan materi lain yang ditampilkan di website Auraquina merupakan milik Auraquina atau pihak yang memberikan hak penggunaan kepada Auraquina.',
                        'Anda tidak diperkenankan menyalin, menggunakan ulang, memodifikasi, menjual, atau mendistribusikan materi tersebut untuk kepentingan komersial tanpa persetujuan tertulis dari Auraquina.',
                        'Penggunaan materi Auraquina untuk ulasan pribadi, referensi pesanan, atau komunikasi layanan pelanggan diperbolehkan sepanjang tidak melanggar hukum dan tidak merugikan Auraquina.',
                    ]],
                    ['heading' => 'Akun, Pendaftaran, dan Data Pelanggan', 'body' => [
                        'Anda dapat berbelanja sebagai tamu atau melalui akun pelanggan apabila fitur akun tersedia. Saat mendaftar atau checkout, Anda wajib memberikan nama, nomor telepon, alamat, email, dan informasi lain yang benar, lengkap, serta dapat dihubungi.',
                        'Anda bertanggung jawab menjaga keamanan akses akun, tautan pesanan, kode OTP, atau informasi transaksi pribadi. Setiap aktivitas yang terjadi melalui akun atau data kontak Anda dianggap sebagai aktivitas yang sah sampai Auraquina menerima pemberitahuan adanya penyalahgunaan.',
                        'Auraquina berhak menolak, membatasi, atau membatalkan akses akun apabila terdapat indikasi data palsu, penyalahgunaan promo, aktivitas mencurigakan, atau pelanggaran ketentuan website.',
                    ]],
                    ['heading' => 'Komunikasi Elektronik', 'body' => [
                        'Dengan menggunakan website Auraquina, Anda menyetujui bahwa komunikasi terkait akun, pesanan, pembayaran, pengiriman, promo, dan layanan pelanggan dapat dilakukan secara elektronik melalui website, email, WhatsApp, SMS, notifikasi payment gateway, atau kanal resmi lain.',
                        'Notifikasi elektronik, struk, invoice, status pesanan, dan riwayat komunikasi yang dikirim melalui kanal tersebut dapat digunakan sebagai bukti komunikasi transaksi.',
                        'Pastikan nomor telepon dan email yang Anda gunakan aktif agar informasi pembayaran, batas waktu pembayaran, dan pembaruan pesanan dapat diterima dengan baik.',
                    ]],
                    ['heading' => 'Informasi dan Deskripsi Produk', 'body' => [
                        'Auraquina berupaya menampilkan foto, warna, ukuran, bahan, harga, stok, dan deskripsi produk seakurat mungkin sesuai informasi yang tersedia.',
                        'Perbedaan warna dapat terjadi karena pencahayaan foto, pengaturan layar, batch produksi, atau karakter bahan. Toleransi ukuran juga dapat terjadi karena metode pengukuran manual dan karakter potongan busana.',
                        'Informasi produk bukan jaminan bahwa warna atau ukuran akan terlihat sama persis pada setiap perangkat atau tubuh pelanggan. Silakan membaca detail produk dan panduan ukuran sebelum checkout.',
                    ]],
                    ['heading' => 'Pemesanan, Harga, Promo, dan Voucher', 'body' => [
                        'Pesanan dianggap dibuat setelah data checkout lengkap dan sistem berhasil menerbitkan nomor atau kode pesanan. Auraquina dapat meninjau pesanan apabila terdapat kendala stok, kesalahan harga yang wajar, data tidak lengkap, atau alasan operasional yang sah.',
                        'Harga, stok, promo, voucher, dan hadiah pembelian dapat berubah sewaktu-waktu. Promo atau voucher hanya berlaku sesuai periode, kuota, minimum transaksi, metode pembayaran, kategori produk, dan syarat lain yang ditampilkan pada saat digunakan.',
                        'Promo atau voucher tidak dapat diuangkan, tidak selalu dapat digabungkan, dan dapat dibatalkan apabila terdapat indikasi penyalahgunaan, duplikasi akun, kesalahan sistem, atau pelanggaran ketentuan promo.',
                    ]],
                    ['heading' => 'Pembayaran dan Status Pesanan', 'body' => [
                        'Pembayaran Auraquina diproses melalui payment gateway dan metode pembayaran yang tersedia saat checkout, termasuk namun tidak terbatas pada virtual account, QRIS, kartu, atau metode lain yang diaktifkan.',
                        'Setiap metode pembayaran memiliki batas waktu pembayaran, instruksi, biaya, dan proses verifikasi masing-masing. Virtual account, QRIS, atau transaksi kartu yang kedaluwarsa, gagal, ditolak, atau tidak selesai sampai batas waktu dapat menyebabkan pesanan tetap berstatus menunggu pembayaran atau otomatis dibatalkan.',
                        'Pesanan baru diproses setelah pembayaran berhasil diterima atau terverifikasi oleh sistem. Apabila pembayaran gagal tetapi dana terdebit, pelanggan dapat menghubungi customer care Auraquina dengan bukti transaksi agar kami dapat membantu pengecekan sesuai prosedur payment gateway dan bank terkait.',
                    ]],
                    ['heading' => 'Pembatalan Pesanan', 'body' => [
                        'Pembatalan oleh pelanggan hanya dapat dilakukan saat pesanan masih berstatus menunggu pembayaran, kecuali Auraquina menyetujui pembatalan karena kondisi tertentu.',
                        'Pesanan yang sudah dibayar, diproses, dikemas, atau diserahkan kepada kurir tidak dapat dibatalkan secara sepihak oleh pelanggan.',
                        'Auraquina dapat membatalkan pesanan apabila pembayaran tidak selesai sampai batas waktu, stok tidak tersedia, data pengiriman tidak valid, terdapat indikasi penyalahgunaan transaksi, atau terdapat kendala operasional yang tidak dapat dihindari.',
                    ]],
                    ['heading' => 'Pengiriman dan Risiko Kehilangan', 'body' => [
                        'Pesanan dikirim ke alamat yang Anda masukkan saat checkout. Anda bertanggung jawab memastikan nama penerima, nomor telepon, alamat, kode pos, dan catatan pengiriman sudah benar sebelum menyelesaikan pesanan.',
                        'Estimasi pengiriman mengikuti layanan kurir yang dipilih dan dapat berubah karena antrean operasional, kondisi kurir, cuaca, alamat tidak lengkap, hari libur, atau keadaan di luar kendali Auraquina.',
                        'Risiko keterlambatan, kehilangan, atau kerusakan selama pengiriman akan ditangani sesuai hasil penelusuran dengan kurir. Auraquina akan membantu proses pelacakan dan komunikasi, namun keputusan klaim mengikuti ketentuan kurir yang berlaku.',
                    ]],
                    ['heading' => 'Retur, Penukaran, dan Layanan Purna Jual', 'body' => [
                        'Pengajuan retur, penukaran, atau komplain purna jual mengikuti Kebijakan Return & Exchange Auraquina dan harus diajukan melalui kanal yang tersedia dalam batas waktu yang ditentukan.',
                        'Untuk mempercepat verifikasi, pelanggan wajib menyertakan bukti yang jelas seperti foto produk, foto label atau kemasan, nomor pesanan, serta video unboxing apabila diminta, terutama untuk klaim barang rusak, kurang, atau tidak sesuai.',
                        'Produk harus dalam kondisi bersih, belum dipakai, belum dicuci, tidak berbau, tidak terkena noda, dan label atau kelengkapan masih tersedia, kecuali kendala yang dilaporkan memang terkait cacat produksi atau kesalahan pengiriman yang terverifikasi.',
                    ]],
                    ['heading' => 'Privasi dan Perlindungan Data', 'body' => [
                        'Penggunaan data pelanggan mengikuti Kebijakan Privasi Auraquina. Data digunakan untuk memproses pesanan, pembayaran, pengiriman, layanan pelanggan, pencegahan penyalahgunaan, dan komunikasi yang relevan dengan transaksi.',
                        'Auraquina dapat membagikan data yang diperlukan kepada penyedia payment gateway, kurir, penyedia layanan teknologi, atau pihak lain yang membantu pemenuhan transaksi sepanjang relevan dengan layanan yang diberikan.',
                        'Auraquina berupaya menjaga kerahasiaan data pelanggan dan membatasi akses hanya kepada pihak yang membutuhkan untuk tujuan operasional yang sah.',
                    ]],
                    ['heading' => 'Penggunaan yang Diperbolehkan dan Ganti Rugi', 'body' => [
                        'Anda setuju untuk tidak menggunakan website Auraquina untuk tindakan yang melanggar hukum, mengganggu sistem, mencoba mengakses data tanpa izin, menyalahgunakan promo, mengirim informasi palsu, atau merugikan pelanggan lain, Auraquina, maupun mitra layanan.',
                        'Anda bertanggung jawab atas kerugian, klaim, biaya, atau tuntutan yang timbul akibat pelanggaran Syarat & Ketentuan ini, penyalahgunaan akun, pelanggaran hukum, atau penggunaan website yang tidak semestinya oleh Anda.',
                        'Auraquina berhak membatasi akses, menolak transaksi, atau mengambil langkah yang wajar apabila terdapat indikasi penyalahgunaan atau pelanggaran ketentuan.',
                    ]],
                    ['heading' => 'Pernyataan dan Batasan Tanggung Jawab', 'body' => [
                        'Website dan layanan Auraquina disediakan dengan upaya terbaik agar informasi dan transaksi berjalan akurat, aman, dan nyaman, namun gangguan teknis, kesalahan tampilan, keterlambatan pihak ketiga, atau kondisi di luar kendali Auraquina dapat terjadi.',
                        'Sepanjang diperbolehkan oleh hukum yang berlaku, tanggung jawab Auraquina atas suatu transaksi terbatas pada nilai produk atau pesanan terkait yang dibayarkan pelanggan, kecuali ditentukan lain oleh peraturan perundang-undangan.',
                        'Auraquina tidak bertanggung jawab atas kerugian tidak langsung yang timbul dari penggunaan website, keterlambatan kurir, gangguan payment gateway, atau penggunaan produk yang tidak sesuai instruksi perawatan.',
                    ]],
                    ['heading' => 'Hukum yang Berlaku', 'body' => [
                        'Syarat & Ketentuan ini diatur dan ditafsirkan berdasarkan hukum Republik Indonesia.',
                        'Apabila terjadi perselisihan, Auraquina dan pelanggan akan mengutamakan penyelesaian secara musyawarah melalui kanal customer care resmi sebelum menempuh langkah lain sesuai ketentuan hukum yang berlaku di Indonesia.',
                    ]],
                    ['heading' => 'Pertanyaan, Kontak, dan Masukan', 'body' => [
                        'Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan, pesanan, pembayaran, pengiriman, retur, atau layanan Auraquina, silakan hubungi customer care melalui WhatsApp +62 877-1151-6373 atau email hello@auraquina.com.',
                        'Mohon sertakan nomor pesanan, nama pemesan, dan bukti pendukung agar tim Auraquina dapat membantu pengecekan dengan lebih cepat dan akurat.',
                    ]],
                ],
            ],
        ];
    }
}
