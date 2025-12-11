<?php

if (!function_exists('getFileIcon')) {
    function getFileIcon($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $icons = [
            'pdf' => 'file-pdf',
            'doc' => 'file-word',
            'docx' => 'file-word',
            'xls' => 'file-excel',
            'xlsx' => 'file-excel',
            'ppt' => 'file-powerpoint',
            'pptx' => 'file-powerpoint',
            'jpg' => 'file-image',
            'jpeg' => 'file-image',
            'png' => 'file-image',
            'gif' => 'file-image',
            'zip' => 'file-archive',
            'rar' => 'file-archive',
            'txt' => 'file-alt',
            'mp4' => 'file-video',
            'mp3' => 'file-audio'
        ];
        
        return $icons[$extension] ?? 'file';
    }
}

if (!function_exists('formatSizeUnits')) {
    function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
}