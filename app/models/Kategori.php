<?php
class Kategori extends BaseModel {
    protected string $table='tbl_kategori'; protected string $primary='id_kategori';
    public function save(string $nama, ?int $id=null): void {
        if($id){$s=$this->db->prepare("UPDATE tbl_kategori SET nama_kategori=? WHERE id_kategori=?");$s->execute([$nama,$id]);}
        else {$s=$this->db->prepare("INSERT INTO tbl_kategori(nama_kategori) VALUES(?)");$s->execute([$nama]);}
    }
}
