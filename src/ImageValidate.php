<?php
namespace ImageConsole;

use Psr\Log\LoggerInterface;

class ImageValidate
{
    /**
     * @var LoggerInterface
     */
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * @param $path
     * @return bool
     * @throws \Exception
     */
    public function isValidImage($path)
    {
        if (!file_exists($path)) {
            $message = "Invalid local image provided as argument";
            $this->log->error($message);
            throw new \Exception($message);
        }
        if ($this->mimeValidate($path) && $this->alternativeValidate($path)) {
            return true;
        } else {
            $message = "Invalid image provided as argument";
            $this->log->error($message);
            throw new \Exception($message);
        }
    }

    /**
     * @param $path
     * @return bool
     */
    private function mimeValidate($path)
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fileInfo, $path);
        finfo_close($fileInfo);
        if (strstr($mime, "image/")) {
            return true;
        }
        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    private function alternativeValidate($path)
    {
        $size = getimagesize($path);
        if (empty($size) || ($size[0] === 0) || ($size[1] === 0)) {
            return false;
        }
        return true;
    }
}