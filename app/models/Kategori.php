<?php
class Kategori extends BaseModel {
    protected string $table='tbl_kategori'; protected string $primary='id_kategori';
    public function save(string $nama, ?int $id=null): void {
        if($id){$s=$this->db->prepare("UPDATE tbl_kategori SET nama_kategori=? WHERE id_kategori=?");$s->execute([$nama,$id]);}
        else {$s=$this->db->prepare("INSERT INTO tbl_kategori(nama_kategori) VALUES(?)");$s->execute([$nama]);}
    }
    public function delete(int $id): void {
        $products=$this->db->prepare("SELECT id_produk FROM tbl_produk WHERE id_kategori=?");
        $products->execute([$id]);
        $prodIds=$products->fetchAll(PDO::FETCH_COLUMN);
        if(!empty($prodIds)){
            $prodModel=new Produk();
            foreach($prodIds as $pId){
                $prodModel->delete((int)$pId);
            }
        }
        parent::delete($id);
    }
}
