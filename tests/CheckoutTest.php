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
        $this->fileProduk = __DIR__ . '/../data/products.json';
        $this->filePesanan = __DIR__ . '/../data/orders.json';
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

    // PATH 2
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

        $this->assertEquals(
            550000,
            $hasil['total_bayar']
        );
    }

    // PATH 3
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

        $this->assertEquals(
            1125000,
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