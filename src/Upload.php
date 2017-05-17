<?php

namespace oangia\Upload;

use Intervention\Image\ImageManagerStatic as Image;
use oangia\CUrl\CUrl;

class Upload {

    protected static $supported_image = [
        'gif',
        'jpg',
        'jpeg',
        'png'
    ];

    public static function uploadImage( $image, $path, $crop = false ) 
    {
        $path_date = date('Y-m');
        $base_path = base_path() . '/public/uploads/' . $path . '/' . $path_date;

        if ( is_string( $image ) ) {
            try {
                if ( strpos( $image, 'http' ) !== false ) {
                    $curl = new CUrl();
                    $image = base64_encode( $curl->connect( 'GET', $image ) );
                }

                $imagedata = base64_decode( $image );

                $f = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_buffer($f, $imagedata);

                $extension = explode('/', $mime_type)[1];
                $filename = static::unique_name_image() . '.' . $extension;
            } catch ( Exception $ex ) {
                return '';
            }
        } else {
            $filename = static::unique_name_image() . '.' . $image->getClientOriginalExtension();
            $extension = $image->getClientOriginalExtension();
            $image = $image->getRealPath();
        }

        if ( ! in_array( strtolower( $extension ), static::$supported_image ) ) {
            return '';
        }

        if ( ! is_dir( $base_path ) ) {
            mkdir( $base_path, 0777, true );
        }

        $im = Image::make( $image );

        if ( $crop ) {
            $height = $im->height();
            $width = $im->width();

            if ( floor( $width * $crop[ 1 ] / $crop[ 0 ] ) > $height ) {
                $im = $im->crop( floor( $height * $crop[ 0 ] / $crop[ 1 ] ), $height );
            } else {
                $im = $im->crop( $width, floor( $width * $crop[ 1 ] / $crop[ 0 ] ) );
            }
        }
        $im->save( $base_path . '/' . $filename );

        return '/uploads/' . $path . '/' . $path_date . '/' . $filename ;
    }

    public static function upload($file, $path) {
        $path_date = date('Y-m');
        $base_path = base_path() . '/public/uploads/' . $path . '/' . $path_date;
        $filename = static::unique_name_image() . '.' . $file->getClientOriginalExtension();

        if ( ! is_dir( $base_path ) ) {
            mkdir( $base_path, 0777, true );
        }

        $file->move(
            $base_path, $filename
        );

        return '/uploads/' . $path . '/' . $path_date . '/' . $filename ;
    }

    private static function unique_name_image()
    {
        $s = strtoupper(md5(uniqid(rand(), true)));
        $guidText =
            substr($s, 0, 8) . '-' .
            substr($s, 8, 4) . '-' .
            substr($s, 12, 4). '-' .
            substr($s, 16, 4). '-' .
            substr($s, 20);
        return $guidText;
    }
}