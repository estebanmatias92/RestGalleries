<?php namespace RestGalleries\Tests\APIs;



class ApiGalleryTest extends \RestGalleries\Tests\TestCase
{
    public function testNewHttpReturnsHttpObject()
    {
        $model = new \RestGalleries\Tests\APIs\StubService\Gallery;
        $http  = $model->newHttp();

        assertThat($http, is(anInstanceOf('RestGalleries\\Http\\Guzzle\\GuzzleHttp')));

    }

    public function testNewPhotoReturnsPhotoObject()
    {
        $model      = new \RestGalleries\Tests\APIs\StubService\Gallery;
        $modelPhoto = $model->newPhoto();

        assertThat($modelPhoto, is(anInstanceOf('RestGalleries\\Tests\\APIs\\StubService\\Photo')));

    }

    public function testSetCredentials()
    {
        $model = new \RestGalleries\Tests\APIs\StubService\Gallery;

        $model->setCredentials(['credentials']);
        $credentials = $model->getCredentials();

        assertThat($credentials, is(equalTo(['credentials'])));

    }

    public function testSetCache()
    {
        $model = new \RestGalleries\Tests\APIs\StubService\Gallery;

        $model->setCache('file-system-name', ['whatever_path']);
        $cache = $model->getCache();

        assertThat($cache, is(arrayValue()));
        assertThat($cache, hasKey('file_system'));
        assertThat($cache, hasKey('path'));

    }

}

