<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Checkout.php';

use App\Checkout;

class CheckoutTest extends TestCase
{
    private $fileProduk;
    private $filePesanan;

    protected function setUp(): void
    {
        copy(
            __DIR__ . '/../data/products_seed.json',
            __DIR__ . '/../data/products_test.json'
        );

        file_put_contents(
            __DIR__ . '/../data/orders_test.json',
            json_encode([])
        );

        $this->fileProduk = __DIR__ . '/../data/products_test.json';
        $this->filePesanan = __DIR__ . '/../data/orders_test.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->fileProduk)) {
            unlink($this->fileProduk);
        }

        if (file_exists($this->filePesanan)) {
            unlink($this->filePesanan);
        }
    }

    // PATH 1
    public function testCheckoutNormal()
    {
        $checkout = new Checkout(
            $this->fileProduk,
            $this->filePesanan
        );

        $keranjang = [
            'PRD-001' => 1
        ];

        $hasil = $checkout->prosesCheckout(
            'test@mail.com',
            'Ponorogo',
            $keranjang
        );

        $this->assertEquals(
            170000,
            $hasil['total_bayar']
        );
    }

    // PATH 2 - Gratis Ongkir
    public function testGratisOngkir()
    {
        $checkout = new Checkout(
            $this->fileProduk,
            $this->filePesanan
        );

        $keranjang = [
            'PRD-001' => 2,
            'PRD-002' => 1
        ];

        $hasil = $checkout->prosesCheckout(
            'test@mail.com',
            'Ponorogo',
            $keranjang
        );

        // 300.000 + 200.000 = 500.000
        // Gratis ongkir
        $this->assertEquals(
            500000,
            $hasil['total_bayar']
        );
    }

    // PATH 3 - Diskon 10%
    public function testDiskon10Persen()
    {
        $checkout = new Checkout(
            $this->fileProduk,
            $this->filePesanan
        );

        $keranjang = [
            'PRD-002' => 5
        ];

        $hasil = $checkout->prosesCheckout(
            'test@mail.com',
            'Ponorogo',
            $keranjang
        );

        // 5 x 200.000 = 1.000.000
        // Diskon 10% = 100.000
        // Total = 900.000
        $this->assertEquals(
            900000,
            $hasil['total_bayar']
        );
    }

    // Error: keranjang kosong
    public function testKeranjangKosong()
    {
        $this->expectException(Exception::class);

        $checkout = new Checkout(
            $this->fileProduk,
            $this->filePesanan
        );

        $checkout->prosesCheckout(
            'test@mail.com',
            'Ponorogo',
            []
        );
    }

    // Error: alamat kosong
    public function testAlamatKosong()
    {
        $this->expectException(Exception::class);

        $checkout = new Checkout(
            $this->fileProduk,
            $this->filePesanan
        );

        $checkout->prosesCheckout(
            'test@mail.com',
            '',
            ['PRD-001' => 1]
        );
    }

    // Error: stok kurang
    public function testStokKurang()
    {
        $this->expectException(Exception::class);

        $checkout = new Checkout(
            $this->fileProduk,
            $this->filePesanan
        );

        $checkout->prosesCheckout(
            'test@mail.com',
            'Ponorogo',
            ['PRD-002' => 999]
        );
    }
}