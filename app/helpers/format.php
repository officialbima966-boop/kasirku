<?php
function rupiah($angka): string { return 'Rp ' . number_format((float)$angka, 0, ',', '.'); }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function barcode_svg(string $value): string {
    $barcode=strtoupper(trim($value));
    if($barcode==='' || strlen($barcode)>32 || preg_match('/^[A-Z0-9 .$\x2F+%-]+$/D',$barcode)!==1) {
        return '<div class="barcode-image barcode-invalid">Barcode tidak valid</div>';
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
        '/' =>'nwnwnnnwn','+'=>'nwnnnwnwn','%'=>'nnnwnwnwn','*'=>'nwnnwnwnn',
    ];
    $narrow=2;
    $wide=5;
    $quietZone=$narrow*10;
    $barHeight=48;
    $position=$quietZone;
    $characters=str_split('*'.$barcode.'*');
    $bars='';

    foreach($characters as $characterIndex=>$character) {
        foreach(str_split($patterns[$character]) as $elementIndex=>$element) {
            $width=$element==='w'?$wide:$narrow;
            if($elementIndex%2===0) $bars.='<rect x="'.$position.'" y="0" width="'.$width.'" height="'.$barHeight.'"></rect>';
            $position+=$width;
        }
        if($characterIndex<count($characters)-1) $position+=$narrow;
    }

    $svg='<svg viewBox="0 0 '.($position+$quietZone).' '.$barHeight.'" preserveAspectRatio="none" role="img" aria-label="'.e('Barcode '.$barcode).'">'.$bars.'</svg>';
    return '<div class="barcode-image">'.$svg.'<span>'.e($barcode).'</span></div>';
}
function redirect(string $url): never { header('Location: '.$url); exit; }
function url(string $page, array $params=[]): string {
    return BASE_URL.'/index.php?'.http_build_query(array_merge(['page'=>$page],$params));
}
function flash(string $key, ?string $message=null): ?string {
    if ($message !== null) { $_SESSION['_flash'][$key]=$message; return null; }
    $value=$_SESSION['_flash'][$key]??null; unset($_SESSION['_flash'][$key]); return $value;
}
