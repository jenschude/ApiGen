<?php declare(strict_types=1);

namespace ApiGen\Utils;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Finder;

class FileSystem
{

    public function normalizePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }


    public function forceDir(string $path): string
    {
        @mkdir(dirname($path), 0755, true);
        return $path;
    }


    public function deleteDir(string $path): void
    {
        NetteFileSystem::delete($path);
    }


    public function purgeDir(string $path): void
    {
        NetteFileSystem::delete($path);
        NetteFileSystem::createDir($path);
    }


    /**
     * @param string $path
     * @param array $baseDirectories
     * @return string
     */
    public function getAbsolutePath(string $path, array $baseDirectories = []): string
    {
        foreach ($baseDirectories as $directory) {
            $fileName = $directory . '/' . $path;
            if (is_file($fileName)) {
                return $this->normalizePath(realpath($fileName));
            }
        }

        if (file_exists($path)) {
            $path = realpath($path);
        }

        if (file_exists(getcwd() . $path)) {
            $path = getcwd() . $path;
        }

        return $this->normalizePath($path);
    }


    public function isDirEmpty(string $path): bool
    {
        if (count(glob($path . '/*'))) {
            return false;
        }

        return true;
    }


    /**
     * @param array $source
     * @param string $destination
     */
    public function copy(array $source, string $destination): void
    {
        foreach ($source as $resourceSource => $resourceDestination) {
            if (is_file($resourceSource)) {
                copy($resourceSource, FileSystem::forceDir($destination  . '/' . $resourceDestination));
                continue;
            } else {
                /** @var \RecursiveDirectoryIterator $iterator */
                $iterator = Finder::findFiles('*')->from($resourceSource)->getIterator();
                foreach ($iterator as $item) {
                    /** @var \SplFileInfo $item */
                    copy($item->getPathName(), FileSystem::forceDir($destination
                        . '/' . $resourceDestination
                        . '/' . $iterator->getSubPathName()));
                }
            }
        }
    }
}
