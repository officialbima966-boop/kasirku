<?php
function rupiah($angka): string { return 'Rp ' . number_format((float)$angka, 0, ',', '.'); }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function url(string $page, array $params=[]): string {
    return BASE_URL.'/index.php?'.http_build_query(array_merge(['page'=>$page],$params));
}
function flash(string $key, ?string $message=null): ?string {
    if ($message !== null) { $_SESSION['_flash'][$key]=$message; return null; }
    $value=$_SESSION['_flash'][$key]??null; unset($_SESSION['_flash'][$key]); return $value;
}
function transaction_barcode_svg(string $value): string {
    $barcode=strtoupper(trim($value));
    if($barcode==='' || strlen($barcode)>32 || preg_match('/^[A-Z0-9 .$\x2F+%-]+$/D',$barcode)!==1) {
        return '';
    }

    $patterns=[
        '0'=>'nnnwwnwnn','1'=>'wnnwnnnnw','2'=>'nnwwnnnnw','3'=>'wnwwnnnnn',
        '4'=>'nnnwwnnnw','5'=>'wnnwwnnnn','6'=>'nnwwwnnnn','7'=>'nnnwnnwnw',
        '8'=>'wnnwnnwnn','9'=>'nnwwnnwnn','A'=>'wnnnnwnnw','B'=>'nnwnnwnnw',
        'C'=>'wnwnnwnnn','D'=>'nnnnwwnnw','E'=>'wnnnwwnnn','F'=>'nnwnwwnnn',
        'G'=>'nnnnnwwnw','H'=>'wnnnnwwnn','I'=>'nnwnnwwnn','J'=>'nnnnwwwnn',
        'K'=>'wnnnnnnww','L'=>'nnwnnnnww','M'=>'wnwnnnnwn','N'=>'nnnnwnnww',
        'O'=>'wnnnwnnwn','P'=>'nnwnwnnwn','Q'=>'nnnnnnwww','R'=>'wnnnnnwwn',
        'S'=>'nnwnnnwwn','T'=>'nnnnwnwwn','U'=>'wwnnnnnnw','V'=>'nwwnnnnnw',
        'W'=>'wwwnnnnnn','X'=>'nwnnwnnnw','Y'=>'wwnnwnnnn','Z'=>'nwwnwnnnn',
        '-'=>'nwnnnnwnw','.'=>'wwnnnnwnn',' '=>'nwwnnnwnn','$'=>'nwnwnwnnn',
        '/'=>'nwnwnnnwn','+'=>'nwnnnwnwn','%'=>'nnnwnwnwn','*'=>'nwnnwnwnn',
    ];
    $narrow=2;
    $wide=5;
    $quietZone=20;
    $barHeight=48;
    $position=$quietZone;
    $bars='';
    $characters=str_split('*'.$barcode.'*');

    foreach($characters as $characterIndex=>$character) {
        foreach(str_split($patterns[$character]) as $elementIndex=>$element) {
            $width=$element==='w'?$wide:$narrow;
            if($elementIndex%2===0) $bars.='<rect x="'.$position.'" y="0" width="'.$width.'" height="'.$barHeight.'"></rect>';
            $position+=$width;
        }
        if($characterIndex<count($characters)-1) $position+=$narrow;
    }

    $width=$position+$quietZone;
    return '<svg class="receipt-code-svg" viewBox="0 0 '.$width.' '.$barHeight.'" preserveAspectRatio="none" role="img" aria-label="'.e('Barcode kode transaksi '.$barcode).'"><rect width="100%" height="100%" fill="#fff"></rect>'.$bars.'</svg>';
}
