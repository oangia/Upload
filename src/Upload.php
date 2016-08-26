<?php

namespace oangia\Upload;

use Intervention\Image\ImageManagerStatic as Image;

class Upload {

    protected static $supported_image = [
        'gif',
        'jpg',
        'jpeg',
        'png'
    ];

    public static function uploadAvatar( $image, $path ) 
    {
        return static::uploadImage( $image, $path, true );
    }

    public static function uploadImage( $image, $path, $crop = false ) 
    {
        $base_path = base_path() . '/public/uploads/' . $path;

        if ( is_string( $image ) ) {
            try {
                $imgdata = base64_decode( $image );

                $f = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_buffer($f, $imgdata);

                $extension = explode('/', $mime_type)[1];
                $filename = unique_name_image() . '.' . $extension;

            } catch ( Exception $ex ) {
                return '';
            }
        } else {
            $filename = unique_name_image() . '.' . $image->getClientOriginalExtension();
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
                $im = $im->crop( $height * $crop[ 0 ] / $crop[ 1 ], $height );
            } else {
                $im = $im->crop( $width, $width * $crop[ 1 ] / $crop[ 0 ] );
            }
            $im = $im->resize( 360, 240 ) ;
        }
        $im->save( $base_path . '/' . $filename );

        return '/uploads/' . $path . '/' . $filename ;
    }


    public static function upload( $file, $path, $filename , $supported ) {
        if ( ! in_array( strtolower( $file->getClientOriginalExtension() ), $supported ) ) {
            return '';
        }

        if ( ! is_dir( 'uploads/' . $path ) ) {
            mkdir( 'uploads/' . $path, 0777, true );
        }

        $file->move(
            base_path() . '/public/uploads/' . $path, $filename
        );

        return '/uploads/' . $path . '/' . $filename ;
    }
}