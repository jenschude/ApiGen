<?php declare(strict_types=1);

namespace ApiGen\Utils\Finder;

use Nette\Utils\Finder;
use SplFileInfo;

class NetteFinder implements FinderInterface
{
    /**
     * @param string|array $source
     * @param array $exclude
     * @param array $extensions
     * @return SplFileInfo[]
     */
    public function find($source, array $exclude = [], array $extensions = ['php']): array
    {
        $sources = $this->turnToIterator($source);
        $fileMasks = $this->turnExtensionsToMask($extensions);

        $files = [];
        foreach ($sources as $source) {
            $files = array_merge($files, $this->getFilesFromSource($source, $exclude, $fileMasks));
        }

        return $files;
    }


    /**
     * @param string $source
     * @param array $exclude
     * @param string $fileMasks
     * @return SplFileInfo[]
     */
    private function getFilesFromSource(string $source, array $exclude, string $fileMasks)
    {
        if (is_file($source)) {
            $foundFiles[$source] = new SplFileInfo($source);
            return $foundFiles;
        } else {
            $finder = Finder::findFiles($fileMasks)->exclude($exclude)
                ->from($source)->exclude($exclude);
            return $this->convertFinderToArray($finder);
        }
    }


    /**
     * @param string[]|string $source
     * @return string[]
     */
    private function turnToIterator($source): array
    {
        if (! is_array($source)) {
            return [$source];
        }

        return $source;
    }


    private function turnExtensionsToMask(array $extensions): string
    {
        $mask = '';
        foreach ($extensions as $extension) {
            $mask .= '*.' . $extension . ',';
        }

        return rtrim($mask, ',');
    }


    /**
     * @return SplFileInfo[]
     */
    private function convertFinderToArray(Finder $finder)
    {
        return iterator_to_array($finder->getIterator());
    }
}
