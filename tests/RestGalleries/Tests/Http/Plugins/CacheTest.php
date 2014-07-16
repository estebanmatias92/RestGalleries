<?php namespace RestGalleries\Tests\Http\Plugins;



class CacheTest extends \RestGalleries\Tests\TestCase
{
    public function testAddArrayCacheSystem()
    {
        $plugin     = CacheStub::add('array');
        $pluginName = $plugin->name;

        assertThat($pluginName, is(equalTo('Array system')));

    }

    public function testAddArrayFileSystem()
    {
        $path       = [
            'folder' => 'C:\\\\Fake Directory...\\'
        ];
        $plugin     = CacheStub::add('file', $path);
        $pluginName = $plugin->name;

        assertThat($pluginName, is(equalTo('File system')));

    }

    public function testAddInvalidCacheSystem()
    {
        $this->setExpectedException(
            'InvalidArgumentException', 'Cache system is invalid.'
        );

        CacheStub::add('any-invalid-system');

    }

}


class CacheStub extends \RestGalleries\Http\Plugins\Cache
{
    protected function getArraySystem()
    {
        $plugin       = new \stdClass;
        $plugin->name = 'Array system';

        return $plugin;

    }

    protected function getFileSystem()
    {
        $plugin       = new \stdClass;
        $plugin->name = 'File system';

        return $plugin;
    }

}
