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
        $filename = unique_name_image() . '.' . $image->getClientOriginalExtension();

        if ( ! in_array( strtolower( $image->getClientOriginalExtension() ), static::$supported_image ) ) {
            return '';
        }

        if ( ! is_dir( $path ) ) {
            mkdir( $path, 0777, true );
        }

        $im = Image::make($image->getRealPath());

        if ( $crop ) {
            $height = $im->height();
            $width = $im->width();

            if ( $width > $height ) {
                $im = $im->crop( $height, $height );
            } else {
                $im = $im->crop( $width, $width );
            }
            $im = $im->resize(200, 200);
        }
        $im->save( $path . '/' . $filename );

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