<?php
namespace ImageConsole;

use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Monolog\Logger;

/**
 * Class ImageStore
 * @package ImageConsole
 */
class ImageStore
{
    /**
     * @var FilesystemInterface
     */
    private $localFileSystem;

    /**
     * @var FilesystemInterface
     */
    private $remoteFileSystem;

    /**
     * @var string
     */
    private $file;

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var ImageValidate
     */
    private $validator;

    /**
     * ImageStore constructor.
     * @param FilesystemInterface $localFileSystem
     * @param FilesystemInterface $remoteFileSystem
     * @param Logger $log
     * @param ImageValidate $validator
     */
    public function __construct(
        FilesystemInterface $localFileSystem,
        FilesystemInterface $remoteFileSystem,
        Logger $log,
        ImageValidate $validator
    ) {
        $this->localFileSystem = $localFileSystem;
        $this->remoteFileSystem = $remoteFileSystem;
        $this->log = $log;
        $this->validator = $validator;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Process file operations
     *
     * @param $method
     * @return \League\Flysystem\Handler|void
     * @throws \Exception
     */
    public function process($method)
    {
        $this->log->info($method . ' : ' . $this->getFile());
        switch ($method) {
            case 'put':
                $pathPrefix = $this->localFileSystem->getAdapter()->getPathPrefix($this->getFile());
                if ($this->validator->isValidImage($pathPrefix.$this->getFile())) {
                    $this->put();
                }
                break;

            case 'get':
                $this->get();
                break;

            case 'destroy':
                $this->destroy();
                break;

            default:
                echo "Invalid argument - valid arguments are put/get/destroy";
                break;
        }
    }

    /**
     * Put local file onto remote filesystem
     *
     * @throws \Exception
     */
    private function put()
    {
        try {
            $localFile = $this->localFileSystem->read($this->getFile());
            $this->remoteFileSystem->write($this->getFile(), $localFile);
        } catch (FileExistsException | FileNotFoundException $e) {
            $this->log->warn($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get file from remote to local
     *
     * @throws \Exception
     */
    private function get()
    {
        try {
            $remoteFile = $this->remoteFileSystem->read($this->getFile());
            $this->localFileSystem->write($this->getFile(), $remoteFile);
        } catch (FileExistsException|FileNotFoundException $e) {
            $this->log->warn($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete file at given path
     *
     * @throws \Exception
     */
    private function destroy()
    {
        try {
            $this->remoteFileSystem->delete($this->getFile());
        } catch (FileNotFoundException $e) {
            $this->log->warn($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}