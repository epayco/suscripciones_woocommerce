<?php
$po_file = $argv[1] ?? 'subscription-epayco-es_CO.po';
$mo_file = str_replace('.po', '.mo', $po_file);

if (!file_exists($po_file)) {
    die("Error: Archivo no encontrado: $po_file\n");
}

$po_content = file_get_contents($po_file);
$entries = array();
$blocks = preg_split('/\n\s*\n/', $po_content);

foreach ($blocks as $block) {
    if (empty(trim($block))) continue;
    
    $lines = explode("\n", $block);
    $msgid = '';
    $msgstr = '';
    $current_key = null;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line) || $line[0] === '#') continue;
        
        if (preg_match('/^msgid\s+"(.*)"\s*$/', $line, $matches)) {
            $msgid = $matches[1];
            $current_key = 'msgid';
        } 
        elseif (preg_match('/^msgstr\s+"(.*)"\s*$/', $line, $matches)) {
            $msgstr = $matches[1];
            $current_key = 'msgstr';
        }
        elseif (preg_match('/^"(.*)"\s*$/', $line, $matches)) {
            if ($current_key === 'msgid') {
                $msgid .= $matches[1];
            } elseif ($current_key === 'msgstr') {
                $msgstr .= $matches[1];
            }
        }
    }
    
    if ($msgid && $msgstr) {
        $msgid = stripslashes($msgid);
        $msgstr = stripslashes($msgstr);
        
        if (!empty($msgid)) {
            $entries[$msgid] = $msgstr;
        }
    }
}

unset($entries['']);

echo "Entries found: " . count($entries) . "\n";

if (count($entries) === 0) {
    die("Error: No entries found\n");
}

$mo_content = create_mo($entries);
file_put_contents($mo_file, $mo_content);

echo "Generated: $mo_file (" . filesize($mo_file) . " bytes)\n";

function create_mo($entries) {
    $offsets = array();
    $ids = '';
    $strings = '';
    $current_offset = 28 + 8 * count($entries);
    
    foreach ($entries as $id => $string) {
        $id_bytes = $id;
        $string_bytes = $string;
        
        $id_len = strlen($id_bytes);
        $string_len = strlen($string_bytes);
        
        $offsets[] = array($id_len, $current_offset, $string_len, $current_offset + $id_len + 1);
        
        $ids .= $id_bytes . "\0";
        $strings .= $string_bytes . "\0";
        
        $current_offset += $id_len + $string_len + 2;
    }
    
    $mo = pack('Iiiiiii', 0x950412de, 0, count($entries), 28, 28 + 8 * count($entries), 0, 0);
    
    foreach ($offsets as $o) {
        $mo .= pack('ii', $o[0], $o[1]);
    }
    
    foreach ($offsets as $o) {
        $mo .= pack('ii', $o[2], $o[3]);
    }
    
    $mo .= $ids . $strings;
    
    return $mo;
}
?>
