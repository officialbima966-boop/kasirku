<?php
function is_logged_in(): bool { return isset($_SESSION['user']); }
function require_login(): void { if (!is_logged_in()) redirect(url('login')); }
function current_user(): array { return $_SESSION['user'] ?? []; }
function home_page_for_role(?string $role=null): string {
    $role=$role ?? (current_user()['role'] ?? '');
    if($role==='pelanggan') return 'member';
    if($role==='kasir') return 'kasir_pesanan';
    return 'dashboard';
}
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf']=bin2hex(random_bytes(32));
    return $_SESSION['_csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_token" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !hash_equals($_SESSION['_csrf'] ?? '', $_POST['_token'] ?? '')) {
        http_response_code(419); exit('Token keamanan tidak valid. Muat ulang halaman.');
    }
}
verify_csrf();
