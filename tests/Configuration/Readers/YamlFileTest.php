<?php

namespace ApiGen\Tests\Configuration\Readers;

use ApiGen;
use ApiGen\Configuration\Readers\YamlFile;
use PHPUnit\Framework\TestCase;

class YamlFileTest extends TestCase
{

    public function testRead()
    {
        file_put_contents(TEMP_DIR . '/config.yaml', 'var: value');
        $yamlFile = new YamlFile(TEMP_DIR . '/config.yaml');

        $options = $yamlFile->read();
        $this->assertSame(['var' => 'value'], $options);
    }



    /**
     * @expectedException \ApiGen\Configuration\Readers\Exceptions\MissingFileException
     */
    public function testCreateNotExisting()
    {
        new YamlFile(TEMP_DIR . '/not-here.yaml');
    }
}