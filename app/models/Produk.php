<?php
class Produk extends BaseModel {
    protected string $table='tbl_produk'; protected string $primary='id_produk';
    public function all(): array { return $this->db->query("SELECT p.*,k.nama_kategori FROM tbl_produk p LEFT JOIN tbl_kategori k ON p.id_kategori=k.id_kategori ORDER BY p.id_produk DESC")->fetchAll(); }
    public function save(array $d, ?int $id=null): void {
        $barcode=strtoupper(trim((string)($d['barcode']??'')));
        if($barcode==='') $barcode='KS'.strtoupper(bin2hex(random_bytes(6)));
        if($id){$s=$this->db->prepare("UPDATE tbl_produk SET id_kategori=?,nama_produk=?,harga=?,stok=?,deskripsi=?,foto=?,barcode=? WHERE id_produk=?");$s->execute([$d['id_kategori'],$d['nama_produk'],$d['harga'],$d['stok'],$d['deskripsi'],$d['foto']??null,$barcode,$id]);}
        else {$s=$this->db->prepare("INSERT INTO tbl_produk(id_kategori,nama_produk,harga,stok,deskripsi,foto,barcode) VALUES(?,?,?,?,?,?,?)");$s->execute([$d['id_kategori'],$d['nama_produk'],$d['harga'],$d['stok'],$d['deskripsi'],$d['foto']??null,$barcode]);}
    }
    public function updateBarcode(int $id, string $barcode): void {
        $barcode=strtoupper(trim($barcode));
        $product=$this->db->prepare("SELECT id_produk FROM tbl_produk WHERE id_produk=?");
        $product->execute([$id]);
        if(!$product->fetchColumn()) throw new RuntimeException('Produk tidak ditemukan.');
        $existing=$this->db->prepare("SELECT id_produk FROM tbl_produk WHERE barcode=? AND id_produk<>?");
        $existing->execute([$barcode,$id]);
        if($existing->fetchColumn()) throw new RuntimeException('Barcode sudah digunakan produk lain.');
        $update=$this->db->prepare("UPDATE tbl_produk SET barcode=? WHERE id_produk=?");
        $update->execute([$barcode,$id]);
    }
}
