<?php

namespace ApiGen\Tests\Configuration\Readers;

use ApiGen;
use ApiGen\Configuration\Readers\NeonFile;
use PHPUnit\Framework\TestCase;

class NeonFileTest extends TestCase
{

    public function testRead()
    {
        file_put_contents(TEMP_DIR . '/config.neon', 'var: value');
        $neonFile = new NeonFile(TEMP_DIR . '/config.neon');

        $options = $neonFile->read();
        $this->assertSame(['var' => 'value'], $options);
    }


    /**
     * @expectedException \ApiGen\Configuration\Readers\Exceptions\MissingFileException
     */
    public function testCreateNotExisting()
    {
        new NeonFile(TEMP_DIR . '/not-here.neon');
    }
}