<?php
class Pelanggan extends BaseModel {
    protected string $table='tbl_pelanggan'; protected string $primary='id_pelanggan';
    public function save(array $d, ?int $id=null): void {
        if($id){$s=$this->db->prepare("UPDATE tbl_pelanggan SET nama_pelanggan=?, kota=?, no_hp=?, alamat=? WHERE id_pelanggan=?");$s->execute([$d['nama_pelanggan'],$d['kota'],$d['no_hp'],$d['alamat'],$id]);}
        else {$s=$this->db->prepare("INSERT INTO tbl_pelanggan(nama_pelanggan,kota,no_hp,alamat) VALUES(?,?,?,?)");$s->execute([$d['nama_pelanggan'],$d['kota'],$d['no_hp'],$d['alamat']]);}
    }
    public function search(string $q): array {
        $s=$this->db->prepare("SELECT * FROM tbl_pelanggan WHERE nama_pelanggan LIKE ? OR kota LIKE ? ORDER BY id_pelanggan DESC");
        $s->execute(["%$q%","%$q%"]); return $s->fetchAll();
    }
}
