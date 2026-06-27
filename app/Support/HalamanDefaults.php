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
                'eyebrow' => 'Customer Care',
                'description' => 'Saluran resmi untuk pertanyaan pesanan, ukuran, dan bantuan belanja Auraquina.',
                'sections' => [
                    ['heading' => 'Kontak Utama', 'body' => [
                        'WhatsApp admin: 0811-3662-636.',
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
                'eyebrow' => 'Shipping Policy',
                'description' => 'Informasi pengiriman, waktu proses, dan tanggung jawab penerimaan pesanan.',
                'sections' => [
                    ['heading' => 'Proses Pesanan', 'body' => [
                        'Pesanan diproses setelah pembayaran diterima atau terverifikasi.',
                        'Pesanan yang masuk pada hari kerja akan diproses secepatnya sesuai antrean.',
                    ]],
                    ['heading' => 'Pengiriman', 'body' => [
                        'Estimasi pengiriman mengikuti layanan kurir yang dipilih saat checkout.',
                        'Pastikan nama penerima, nomor telepon, dan alamat lengkap sudah benar sebelum menyelesaikan pesanan.',
                    ]],
                    ['heading' => 'Catatan Penting', 'body' => [
                        'Keterlambatan akibat kendala kurir, cuaca, atau force majeure berada di luar kendali Auraquina.',
                        'Jika paket diterima dalam kondisi bermasalah, segera hubungi customer care dengan nomor pesanan Anda.',
                    ]],
                ],
            ],
            [
                'slug' => 'return-exchange',
                'title' => 'Return & Exchange',
                'eyebrow' => 'After Sales Policy',
                'description' => 'Panduan pengajuan penukaran, retur, dan penanganan kendala pasca pembelian.',
                'sections' => [
                    ['heading' => 'Syarat Pengajuan', 'body' => [
                        'Pengajuan dilakukan maksimal 7 hari setelah pesanan diterima.',
                        'Produk harus dalam kondisi bersih, belum dipakai, dan label masih terpasang.',
                    ]],
                    ['heading' => 'Jenis Kendala', 'body' => [
                        'Anda dapat mengajukan penukaran ukuran, retur, atau komplain pesanan dari detail pesanan.',
                        'Sertakan penjelasan yang jelas agar tim kami dapat meninjau lebih cepat.',
                    ]],
                    ['heading' => 'Proses Review', 'body' => [
                        'Setelah request dikirim, tim Auraquina akan meninjau dan menghubungi Anda melalui kanal yang tersedia.',
                        'Persetujuan penggantian atau refund mengikuti hasil verifikasi kondisi barang dan riwayat pesanan.',
                    ]],
                ],
            ],
            [
                'slug' => 'faq',
                'title' => 'Frequently Asked Questions',
                'eyebrow' => 'FAQ',
                'description' => 'Jawaban ringkas untuk pertanyaan yang paling sering ditanyakan pelanggan Auraquina.',
                'sections' => [
                    ['heading' => 'Apakah bisa belanja tanpa akun?', 'body' => [
                        'Bisa. Anda tetap dapat checkout sebagai guest.',
                    ]],
                    ['heading' => 'Bagaimana mengecek status pesanan?', 'body' => [
                        'Setelah order dibuat, Anda dapat membuka detail pesanan dari tautan yang diberikan atau melalui akun jika Anda login.',
                    ]],
                    ['heading' => 'Bagaimana jika ukuran tidak sesuai?', 'body' => [
                        'Gunakan fitur after-sales pada detail pesanan untuk mengajukan penukaran atau komplain.',
                    ]],
                ],
            ],
            [
                'slug' => 'about',
                'title' => 'Tentang Auraquina',
                'eyebrow' => 'About Us',
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
                'slug' => 'size-guide',
                'title' => 'Size Guide',
                'eyebrow' => 'Panduan Ukuran',
                'description' => 'Gunakan panduan ini untuk memilih ukuran yang paling nyaman sebelum checkout.',
                'sections' => [
                    ['heading' => 'Cara Mengukur', 'body' => [
                        'Ukur lingkar dada, pinggang, pinggul, dan panjang baju dengan posisi tubuh rileks.',
                        'Bandingkan hasilnya dengan detail ukuran pada halaman produk.',
                    ]],
                    ['heading' => 'Tips Memilih Ukuran', 'body' => [
                        'Jika Anda berada di antara dua ukuran, pilih berdasarkan preferensi fit yang diinginkan.',
                        'Untuk bahan yang jatuh dan longgar, pertimbangkan ruang gerak tambahan saat memilih ukuran.',
                    ]],
                ],
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Kebijakan Privasi',
                'eyebrow' => 'Privacy Policy',
                'description' => 'Penjelasan singkat tentang data yang kami kumpulkan dan cara Auraquina menggunakannya.',
                'sections' => [
                    ['heading' => 'Data yang Dikumpulkan', 'body' => [
                        'Kami menggunakan data yang Anda berikan saat membuat akun, checkout, atau menghubungi customer care.',
                    ]],
                    ['heading' => 'Penggunaan Data', 'body' => [
                        'Data digunakan untuk memproses pesanan, membantu layanan pelanggan, dan mengirim pembaruan yang relevan dengan transaksi Anda.',
                    ]],
                    ['heading' => 'Perlindungan Data', 'body' => [
                        'Auraquina berupaya menjaga kerahasiaan data pelanggan dan membatasi akses hanya untuk kebutuhan operasional yang relevan.',
                    ]],
                ],
            ],
            [
                'slug' => 'terms-conditions',
                'title' => 'Syarat & Ketentuan',
                'eyebrow' => 'Terms & Conditions',
                'description' => 'Ketentuan dasar yang mengatur penggunaan website dan transaksi di Auraquina.',
                'sections' => [
                    ['heading' => 'Penggunaan Website', 'body' => [
                        'Dengan menggunakan website ini, Anda setuju memberikan informasi yang akurat saat membuat akun atau melakukan checkout.',
                    ]],
                    ['heading' => 'Pemesanan', 'body' => [
                        'Pesanan dianggap diterima setelah data checkout lengkap dan sistem berhasil membuat nomor pesanan.',
                        'Auraquina berhak meninjau atau membatalkan pesanan jika ditemukan kendala stok, data, atau alasan operasional lain yang sah.',
                    ]],
                    ['heading' => 'Harga dan Ketersediaan', 'body' => [
                        'Harga dan ketersediaan produk dapat berubah sewaktu-waktu mengikuti kondisi koleksi yang tersedia.',
                    ]],
                ],
            ],
        ];
    }
}
