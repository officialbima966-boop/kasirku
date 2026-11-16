<?php
class AuthController {
    public function login(): void {
        if(is_logged_in()) redirect(url('dashboard'));
        $error=null;
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $u=(new User())->authenticate(post('username'),post('password'));
            if($u){session_regenerate_id(true);$_SESSION['user']=['id'=>$u['id_user'],'nama'=>$u['nama'],'username'=>$u['username'],'role'=>$u['role']];redirect(url('dashboard'));}
            $error='Username atau password salah.';
        }
        require APP_PATH.'/views/auth/login.php';
    }
    public function logout(): void { $_SESSION=[];session_destroy();redirect(url('login')); }
}
