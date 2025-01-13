<?php

declare(strict_types=1);

namespace App\Service\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSaver
{
    public function __construct(
        private string|UploadedFile $file
    )
    {
    }

    public function save(): string|File
    {
        $fileSystem = new Filesystem();

        if (is_string($this->file)) {
            $extension = preg_replace('/.+[.]/', '', $this->file);
            $client = HttpClient::create();
            $response = $client->request('GET', $this->file);

            if ($response->getStatusCode() === 200) {
                $content = $response->getContent();
                $tempFile = tempnam(sys_get_temp_dir(), 'tmp_' . uniqid());
                file_put_contents($tempFile, $content);

                $this->file = new UploadedFile(
                    $tempFile,
                    "temp.{$extension}",
                    "image/{$extension}",
                    null,
                    true
                );

            } else {
                return 'File not loaded';
            }
        }

        $newFileName = md5(uniqid() . $this->file->getClientOriginalName()) . '.' . $this->file->getClientOriginalExtension();
        $pathToDir = $this->directory();

        try {
            if (!is_dir($pathToDir)) {
                $fileSystem->mkdir($pathToDir);
                $fileSystem->chown($pathToDir, 'root', true);
                $fileSystem->chmod($pathToDir, 775, 0000, true);
            }

            return $this->file->move($pathToDir, $newFileName);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }


    private function directory(): string
    {
        return '/var/www/html/public/upload/';
    }

}