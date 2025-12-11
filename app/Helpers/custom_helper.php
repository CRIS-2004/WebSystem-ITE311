<?php

if (!function_exists('getFileIconClass')) {
    function getFileIconClass($extension) {
        $extension = strtolower($extension);
        $iconMap = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            'mp4' => 'fa-file-video',
            'mov' => 'fa-file-video',
            'avi' => 'fa-file-video',
            'txt' => 'fa-file-alt',
            'csv' => 'fa-file-csv'
        ];
        return $iconMap[$extension] ?? 'fa-file';
    }
}
if (!function_exists('formatSizeUnits')) {
    function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
}