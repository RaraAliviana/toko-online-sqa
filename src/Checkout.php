<?php
namespace App;
use Exception;

class Checkout
{
    private $fileProduk;
    private $filePesanan;

    public function __construct($fileProduk, $filePesanan) {
        $this->fileProduk = $fileProduk;
        $this->filePesanan = $filePesanan;
    }

    // Tambahan Parameter $alamat
    public function prosesCheckout($emailPelanggan, $alamat, $keranjang) {
        if (empty($keranjang)) throw new Exception("Keranjang belanja kosong.");
        if (empty($alamat)) throw new Exception("Alamat pengiriman wajib diisi.");

        $products = json_decode(file_get_contents($this->fileProduk), true);
        $totalHargaBarang = 0;

        foreach ($keranjang as $kodeProduk => $qty) {
            if ($qty <= 0) throw new Exception("Kuantitas harus lebih dari 0.");
            if (!isset($products[$kodeProduk])) throw new Exception("Produk tidak valid.");
            if ($products[$kodeProduk]['stok'] < $qty) throw new Exception("Stok " . $products[$kodeProduk]['nama'] . " tidak mencukupi.");

            $totalHargaBarang += ($products[$kodeProduk]['harga'] * $qty);
            $products[$kodeProduk]['stok'] -= $qty;
        }

        // Logika Diskon (Sama seperti sebelumnya)
        $ongkosKirim = 20000;
        $diskon = 0;
        if ($totalHargaBarang > 500000) {          
            $ongkosKirim = 0; 
            if ($totalHargaBarang > 1000000) {     
                $diskon = $totalHargaBarang * 0.10; 
            }
        } 
        $totalBayar = ($totalHargaBarang - $diskon) + $ongkosKirim;

        // Simpan Data termasuk Alamat
        $pesananBaru = [
            'id_pesanan' => uniqid('ORD-'),
            'email' => $emailPelanggan,
            'alamat' => htmlspecialchars($alamat),
            'items' => $keranjang,
            'total_bayar' => $totalBayar,
            'status' => 'Menunggu Pembayaran',
            'tanggal' => date('Y-m-d H:i:s')
        ];

        file_put_contents($this->fileProduk, json_encode($products, JSON_PRETTY_PRINT));
        $orders = json_decode(file_get_contents($this->filePesanan), true) ?? [];
        $orders[] = $pesananBaru;
        file_put_contents($this->filePesanan, json_encode($orders, JSON_PRETTY_PRINT));

        return $pesananBaru;
    }
}