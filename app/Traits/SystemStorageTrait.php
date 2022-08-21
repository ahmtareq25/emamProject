<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait SystemStorageTrait
{
    public $full_url;
    public $real_path;
    public $db_directory;

    public function uploadFile($requestFile, $folder, $disk = 'bucket')
    {

        $fileName = $this->getFileNameByExtension($requestFile->getClientOriginalExtension());
        $path = rtrim($folder, '/') . '/';

        $path = $requestFile->storeAs(
            $path, $fileName, $disk
        );
        $path = preg_replace('#/+#', '/', $path);

        $this->db_directory = $path;
        $this->real_path = $this->getRealPath($path);
        $this->full_url = $this->getFullUrl($path);
    }

    public function removeFile($dbPath, $disk = 'bucket')
    {
        Storage::disk($disk)->delete($dbPath);
    }

    private function makeDir($path, $disk = 'bucket')
    {
        $storagePath = Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix();
        if (!File::exists($storagePath . "/" . $path)) {
            $dir = File::makeDirectory($storagePath . "/" . $path, $mode = 0777, true, true);
        }
    }

    public function moveFile($file, $newFolder, $disk = 'bucket'){

        Storage::disk($disk)->move($file, $this->db_directory = $this->prepareNewFilePath($file,$newFolder));
    }

    public function copyFile($file, $newFolder, $disk = 'bucket'){
        Storage::disk($disk)->copy($file, $this->db_directory = $this->prepareNewFilePath($file,$newFolder));
    }

    private function getRealPath($path, $disk='bucket'){
        return Storage::disk($disk)->getDriver()->getAdapter()->getPathPrefix() . $path;
    }

    public function getFullUrl($path, $disk='bucket'){
        return Storage::disk($disk)->url($path);
    }

    private function getFileNameByFile($file_path){
        $array = explode('.', $file_path);
        $extension = end($array);
        return $this->getFileNameByExtension($extension);

    }

    private function getFileNameByExtension($extension){
        return Str::uuid() . time() . '.' . $extension;
    }

    private function prepareNewFilePath($oldFile, $newFolder){
        return rtrim($newFolder, '/').'/'.$this->getFileNameByFile($oldFile);
    }

}
