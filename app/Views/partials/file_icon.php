<?php
/**
 * Returns an icon based on file extension
 * 
 * @param string $extension File extension
 * @return string HTML for the icon
 */
$icon = 'fa-file'; // Default icon

// Map of file extensions to Font Awesome icons
$iconMap = [
    // Documents
    'pdf' => 'file-pdf text-danger',
    'doc' => 'file-word text-primary',
    'docx' => 'file-word text-primary',
    'xls' => 'file-excel text-success',
    'xlsx' => 'file-excel text-success',
    'ppt' => 'file-powerpoint text-warning',
    'pptx' => 'file-powerpoint text-warning',
    'txt' => 'file-alt',
    'csv' => 'file-csv',
    
    // Images
    'jpg' => 'file-image',
    'jpeg' => 'file-image',
    'png' => 'file-image',
    'gif' => 'file-image',
    'bmp' => 'file-image',
    'svg' => 'file-image',
    
    // Archives
    'zip' => 'file-archive',
    'rar' => 'file-archive',
    '7z' => 'file-archive',
    'tar' => 'file-archive',
    'gz' => 'file-archive',
    
    // Code
    'php' => 'file-code',
    'html' => 'file-code',
    'css' => 'file-code',
    'js' => 'file-code',
    'json' => 'file-code',
    
    // Audio/Video
    'mp3' => 'file-audio',
    'wav' => 'file-audio',
    'mp4' => 'file-video',
    'mov' => 'file-video',
    'avi' => 'file-video'
];

// Get the icon class or use default
$extension = strtolower($extension);
$iconClass = $iconMap[$extension] ?? $icon;
?>

<i class="fas fa-<?= $iconClass ?> me-2"></i>