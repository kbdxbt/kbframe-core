<?php

namespace Modules\Core\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\System\Enums\StorageModeEnum;

class Upload
{
    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function formatFileName($path = 'default', $filepath = '', $filename = '', $suffix = '', $format = ''): array|string
    {
        if (!$format) {
            $format = '{path}/{date}/{filename}_{filesha1}{.suffix}';

            if (\array_key_exists('format', $config = $this->filesystem->getConfig())) {
                $format = $config['format'];
            }
        }

        return self::generateFormatFileName($path, $filepath, $filename, $suffix, $format);
    }

    public static function generateFormatFileName($path = 'default', $filepath = '', $filename = '', $suffix = '', $format = ''): string
    {
        if (!$format) {
            $format = '{path}/{date}/{filename}_{random}{.suffix}';
        }

        $replaceArr = [
            '{path}' => $path,
            '{date}' => date('ymd'),
            '{datetime}' => date('ymdHis'),
            '{year}' => date('Y'),
            '{mon}' => date('m'),
            '{day}' => date('d'),
            '{hour}' => date('H'),
            '{min}' => date('i'),
            '{sec}' => date('s'),
            '{timestamp}' => time(),
            '{random}' => Str::random(),
            '{random32}' => Str::random(32),
            '{uniqid}' => uniqid(),
            '{filename}' => substr($filename, 0, 30),
            '{suffix}' => $suffix,
            '{.suffix}' => $suffix ? '.' . $suffix : '',
            '{filesha1}' => $filepath ? hash_file('sha1', $filepath) : '',
            '{filemd5}' => md5($filepath),
        ];

        return str_replace(array_keys($replaceArr), array_values($replaceArr), $format);
    }

    public function upload(UploadedFile $file, $path): array
    {
        $filePath = $this->formatFileName(
            $path,
            $file->path(),
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            $file->extension()
        );

        $uploadFileRepository = \Modules\System\Repositories\UploadRepository::make();
        $hash = md5_file($file);
        $uploadFileModel = $uploadFileRepository->query()->firstWhere('hash', $hash)?->toArray();

        if (!$uploadFileModel) {

            $url = $this->filesystem->url($this->filesystem->putFileAs('', $file, $filePath));

            $uploadFileModel = $uploadFileRepository->getModel()->fill([
                'storage_mode' => StorageModeEnum::fromValue($this->filesystem->getConfig()['driver']),
                'origin_name' => File::basename($file),
                'object_name' => File::basename($filePath),
                'hash' => $hash,
                'mime_type' => File::mimeType($file),
                'storage_path' => $filePath,
                'suffix' => File::extension($filePath),
                'size_byte' => File::size($file),
                'size_info' => format_bytes(File::size($file)),
                'url' => $url,
            ]);

            $uploadFileModel->save();
        }

        return (array) $uploadFileModel;
    }
}
