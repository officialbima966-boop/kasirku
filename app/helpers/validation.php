<?php
function post(string $key, $default='') { return trim((string)($_POST[$key] ?? $default)); }
function required_fields(array $data, array $fields): array {
    $errors=[]; foreach($fields as $f) if (($data[$f]??'')==='') $errors[]="$f wajib diisi.";
    return $errors;
}
