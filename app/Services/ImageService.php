<?php

namespace App\Services;

class ImageService
{
    public function base64Image($path)
    {
        $path = public_path($path);
        if (!file_exists($path)) {
            return '';
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
