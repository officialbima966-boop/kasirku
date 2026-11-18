<?php
class AuthController {
    public function login(): void {
        if(is_logged_in()) redirect(url(home_page_for_role()));
        $error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $u=(new User())->authenticate(post('username'),post('password'));
            if($u){session_regenerate_id(true);$_SESSION['user']=['id'=>$u['id_user'],'nama'=>$u['nama'],'username'=>$u['username'],'role'=>$u['role']];redirect(url(home_page_for_role($u['role'])));}
            $customer=(new Pelanggan())->authenticateAccount(post('username'),post('password'));
            if($customer){session_regenerate_id(true);$_SESSION['user']=['id'=>$customer['id_pelanggan'],'nama'=>$customer['nama_pelanggan'],'username'=>$customer['username'],'role'=>'pelanggan'];redirect(url(home_page_for_role('pelanggan')));}
            $error='Username atau password salah.';
        }
        require APP_PATH.'/views/auth/login.php';
    }
    public function register(): void {
        if(is_logged_in()) redirect(url(home_page_for_role()));
        $error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $name=post('nama_pelanggan');
            $username=post('username');
            $phone=post('no_hp');
            $password=$_POST['password']??null;
            $confirmation=$_POST['konfirmasi_password']??null;
            if($name==='' || $username==='' || !is_string($password) || !is_string($confirmation)) {
                $error='Nama, username, dan password wajib diisi.';
            } elseif(strlen($username)<3 || strlen($username)>50 || preg_match('/^[A-Za-z0-9._-]+$/D',$username)!==1) {
                $error='Username harus 3–50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
            } elseif(strlen($password)<8) {
                $error='Password minimal 8 karakter.';
            } elseif($password!==$confirmation) {
                $error='Konfirmasi password tidak sama.';
            } else {
                try {
                    $id=(new Pelanggan())->registerAccount([
                        'nama_pelanggan'=>$name,
                        'username'=>$username,
                        'no_hp'=>$phone,
                        'password'=>$password,
                    ]);
                    session_regenerate_id(true);
                    $_SESSION['user']=['id'=>$id,'nama'=>$name,'username'=>$username,'role'=>'pelanggan'];
                    redirect(url(home_page_for_role('pelanggan')));
                } catch(RuntimeException $e) {
                    $error=$e->getMessage();
                }
            }
        }
        require APP_PATH.'/views/auth/register.php';
    }
    public function logout(): void { $_SESSION=[];session_destroy();redirect(url('login')); }
}
