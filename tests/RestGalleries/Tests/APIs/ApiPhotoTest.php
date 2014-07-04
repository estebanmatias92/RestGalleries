<?php namespace RestGalleries\Tests\APIs;

class ApiPhotoTest extends \RestGalleries\Tests\TestCase
{
    public function testNewHttpReturnsHttpObject()
    {
        $model = new \RestGalleries\Tests\APIs\StubService\Photo;
        $http  = $model->newHttp();

        assertThat($http, is(anInstanceOf('RestGalleries\\Http\\Guzzle\\GuzzleHttp')));

    }

    public function testSetCredentials()
    {
        $model = new \RestGalleries\Tests\APIs\StubService\Photo;

        $model->setCredentials(['credentials']);
        $credentials = $model->getCredentials();

        assertThat($credentials, is(equalTo(['credentials'])));

    }

    public function testSetCache()
    {
        $model = new \RestGalleries\Tests\APIs\StubService\Photo;

        $model->setCache('file-system-name', ['whatever_path']);
        $cache = $model->getCache();

        assertThat($cache, is(arrayValue()));
        assertThat($cache, hasKey('file_system'));
        assertThat($cache, hasKey('path'));

    }

}
