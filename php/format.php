<?php
date_default_timezone_set('Asia/Shanghai');

$json = file_get_contents(__DIR__ . '/origin.json');
$origin = json_decode($json, true);

$list = format($origin);
file_put_contents(dirname(__DIR__) . '/list.json', json_encode($list));

function format($origin) {
    $result = [
        'name' => '/',
        'size' => 0,
        'time' => 0,
        'children' => [],
    ];
    foreach ($origin as $child) {
        $formatResult = formatRecursive($child);
        $result['size'] += $formatResult['size'];
        if ($formatResult['time'] > $result['time']) {
            $result['time'] = $formatResult['time'];
        }
        $result['children'][] = $formatResult;
    }
    return $result;
}

function formatRecursive($file)
{
    if (isset($file['children'])) {
        $result = [
            'name' => rtrim($file['name'], '/'),
            'size' => 0,
            'time' => 0,
            'children' => [],
        ];
        foreach ($file['children'] as $child) {
            $formatResult = formatRecursive($child);
            $result['size'] += $formatResult['size'];
            if ($formatResult['time'] > $result['time']) {
                $result['time'] = $formatResult['time'];
            }
            $result['children'][] = $formatResult;
        }
    } else {
        $result = [
            'name' => $file['name'],
            'size' => sizeToNumber($file['size']),
            'time' => strtotime($file['time']),
        ];
    }
    return $result;
}

function sizeToNumber($size)
{
    if (endsWith($size, 'GiB')) {
        return (float)$size * 1024 * 1024 * 1024;
    } elseif (endsWith($size, 'MiB')) {
        return (float)$size * 1024 * 1024;
    } elseif (endsWith($size, 'KiB')) {
        return (float)$size * 1024;
    } elseif (endsWith($size, 'GB')) {
        return (float)$size * 1000 * 1000 * 1000;
    } elseif (endsWith($size, 'MB')) {
        return (float)$size * 1000 * 1000;
    } elseif (endsWith($size, 'KB')) {
        return (float)$size * 1000;
    } else {
        return (float)$size;
    }
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}