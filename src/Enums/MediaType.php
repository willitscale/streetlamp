<?php declare(strict_types=1);

namespace willitscale\Streetlamp\Enums;

/**
 * Partial list of most common
 * @source https://www.iana.org/assignments/media-types/media-types.xhtml
 */
enum MediaType: string
{
    case APPLICATION_GZIP = 'application/gzip';
    case APPLICATION_JSON = 'application/json';
    case APPLICATION_PDF = 'application/pdf';
    case APPLICATION_PHP = 'application/x-httpd-php';
    case APPLICATION_XML = 'application/xml';

    case IMAGE_WEBP = 'image/webp';
    case IMAGE_TIFF = 'image/tiff';
    case IMAGE_PNG = 'image/png';
    case IMAGE_JPEG = 'image/jpeg';
    case IMAGE_GIF = 'image/gif';
    case IMAGE_BMP = 'image/bmp';

    case TEXT_CSS = 'text/css';
    case TEXT_HTML = 'text/html';
    case TEXT_JS = 'text/javascript';
    case TEXT_PLAIN = 'text/plain';
    case TEXT_CSV = 'text/csv';

    case VIDEO_AVI = 'video/x-msvideo';
    case VIDEO_MP4 = 'video/mp4';
    case VIDEO_MPEG = 'video/mpeg';
    case VIDEO_WEBM = 'video/webm';
}
