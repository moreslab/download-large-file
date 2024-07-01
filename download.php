<?php
session_start();

$remote_url = 'http://domain.com/file.tar.gz';
$local_file = 'file.tar.gz';
$chunk_size = 10 * 1024 * 1024; // 10MB

if (!isset($_SESSION['downloaded_bytes'])) {
    $_SESSION['downloaded_bytes'] = 0;
}

function get_file_size($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    $data = curl_exec($curl);
    curl_close($curl);

    if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
        return (int)$matches[1];
    }

    return -1;
}

function download_chunk($url, $local_file, $start, $end) {
    $fp = fopen($local_file, 'a');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Range: bytes=$start-$end"]);
    $data = curl_exec($curl);
    fwrite($fp, $data);
    fclose($fp);
    curl_close($curl);
}

$total_size = get_file_size($remote_url);

if ($_SESSION['downloaded_bytes'] < $total_size) {
    $start = $_SESSION['downloaded_bytes'];
    $end = min($start + $chunk_size - 1, $total_size - 1);

    download_chunk($remote_url, $local_file, $start, $end);

    $_SESSION['downloaded_bytes'] = $end + 1;

    echo json_encode([
        'status' => 'downloading',
        'progress' => ($_SESSION['downloaded_bytes'] / $total_size) * 100,
    ]);
} else {
    echo json_encode([
        'status' => 'completed',
        'progress' => 100,
    ]);
}
?>
