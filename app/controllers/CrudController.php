<?php
class CrudController {
    private function storeProductPhoto(?array $file, bool $isRequired = true): ?string {
        if (!$file || !isset($file['error']) || is_array($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            if ($isRequired) {
                throw new RuntimeException('Silakan pilih foto produk.');
            }
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Foto gagal diunggah. Pastikan ukuran file tidak melebihi 5 MB.');
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024 || !is_uploaded_file($file['tmp_name'] ?? '')) {
            throw new RuntimeException('Foto tidak valid atau ukurannya melebihi 5 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $imageInfo = @getimagesize($file['tmp_name']);
        if (!$imageInfo || !isset($extensions[$mime]) || $imageInfo['mime'] !== $mime) {
            throw new RuntimeException('Format foto harus JPG, PNG, atau WEBP.');
        }

        $directory = dirname(__DIR__, 2) . '/public/uploads/produk';
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Folder penyimpanan foto tidak dapat dibuat.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
            throw new RuntimeException('Foto produk gagal disimpan ke server.');
        }
        return $filename;
    }

    private function handle(string $type): void {
        $models=['pelanggan'=>Pelanggan::class,'produk'=>Produk::class,'kategori'=>Kategori::class];
        $model=new $models[$type](); $title=ucfirst($type); $items=$model->all();
        $db=Database::connect(); $categories=$type==='produk'?$db->query("SELECT * FROM tbl_kategori ORDER BY nama_kategori")->fetchAll():[];
        $editItem = ($type==='produk' && isset($_GET['edit'])) ? $model->find((int)$_GET['edit']) : null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            if($type==='produk' && ($_POST['aksi']??'')==='update_barcode') {
                $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
                $barcodeValue=$_POST['barcode']??null;
                $barcode=is_string($barcodeValue)?strtoupper(trim($barcodeValue)):'';
                if($id===false || $id<1 || !$this->isValidBarcode($barcode)) {
                    flash('error','ID produk atau barcode tidak valid.');
                    redirect(url('produk'));
                }
                try {
                    $model->updateBarcode($id,$barcode);
                    flash('success','Barcode produk berhasil diperbarui.');
                } catch(Throwable $e) {
                    flash('error','Barcode tidak dapat disimpan: '.$e->getMessage());
                }
                redirect(url('produk'));
            }
            $id=(int)($_POST['id']??0);
            $uploadedPhoto = null;
            try {
                if($type==='pelanggan') $model->save(['nama_pelanggan'=>post('nama_pelanggan'),'kota'=>post('kota'),'no_hp'=>post('no_hp'),'alamat'=>post('alamat'),'username'=>post('username'),'rfid_uid'=>post('rfid_uid')],$id?:null);
                elseif($type==='produk') {
                    $barcodeValue=$_POST['barcode']??'';
                    if(!is_string($barcodeValue)) throw new RuntimeException('Barcode tidak valid.');
                    $barcode=strtoupper(trim($barcodeValue));
                    if($barcode!=='' && !$this->isValidBarcode($barcode)) throw new RuntimeException('Barcode harus berisi maksimal 32 karakter Code 39 yang valid.');
                    $uploadedPhoto = $this->storeProductPhoto($_FILES['foto'] ?? null, $id === 0);
                    $model->save(['id_kategori'=>(int)post('id_kategori'),'nama_produk'=>post('nama_produk'),'harga'=>(float)post('harga'),'stok'=>(int)post('stok'),'deskripsi'=>post('deskripsi'),'foto'=>$uploadedPhoto,'barcode'=>$barcode],$id?:null);
                }
                else $model->save(post('nama_kategori'),$id?:null);
                flash('success', $id > 0 ? 'Data berhasil diperbarui.' : 'Data berhasil disimpan.'); redirect(url($type));
            } catch(Throwable $e){
                if($uploadedPhoto !== null) {
                    $uploadedPath = dirname(__DIR__, 2) . '/public/uploads/produk/' . $uploadedPhoto;
                    if(is_file($uploadedPath) && !unlink($uploadedPath)) error_log('Gagal membersihkan foto produk setelah penyimpanan database gagal: ' . $uploadedPath);
                }
                $error='Gagal menyimpan data: '.$e->getMessage();
            }
        }
        if(isset($_GET['hapus'])){try{$model->delete((int)$_GET['hapus']);flash('success','Data berhasil dihapus.');}catch(Throwable $e){flash('error','Gagal menghapus data: '.$e->getMessage());}redirect(url($type));}
        $title='Data '.ucfirst($type); require APP_PATH.'/views/crud/'.$type.'.php';
    }
    private function isValidBarcode(string $barcode): bool {
        return strlen($barcode)>=1 && strlen($barcode)<=32 && preg_match('/^[A-Z0-9 .$\x2F+%-]+$/D',$barcode)===1;
    }
    public function pelanggan(): void {$this->handle('pelanggan');}
    public function produk(): void {$this->handle('produk');}
    public function kategori(): void {$this->handle('kategori');}
}
