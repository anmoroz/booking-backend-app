<?php
/*
 * This file is part of the Booking application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);


namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;
use RuntimeException;

class TemporaryFileStorage
{
    const TMP_DIR = '/var/tmp';

    public function __construct(private KernelInterface $kernel)
    {
    }

    public function create(string $fileName): File
    {
        $this->checkTmpDirectory();
        $filePath = $this->getTmpDir().DIRECTORY_SEPARATOR.$fileName;
        @touch($filePath);

        return new File($filePath);
    }

    /**
     * @return string
     */
    public function getTmpDir(): string
    {
        return $this->kernel->getProjectDir().self::TMP_DIR;
    }

    private function checkTmpDirectory(): void
    {
        $dir = $this->getTmpDir();
        if (!is_dir($dir)) {
            $this->mkdir($dir);
        } elseif (!is_writable($dir)) {
            throw new RuntimeException(sprintf('Unable to write in the tmp directory (%s).', $dir));
        }
    }

    /**
     * @param string $dir
     */
    private function mkdir(string $dir)
    {
        if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Unable to create the tmp directory (%s).', $dir));
        }
    }
}