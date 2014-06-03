<?php

use RestGalleries\APIs\Flickr\Photo;
use RestGalleries\Http\Guzzle\GuzzleHttp;

class PhotoTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $endPoint = 'http://api.flickr.com/services/rest/';

        $tokenCredentials = [
            'consumer_key'    => $this->faker->md5,
            'consumer_secret' => $this->faker->sha1,
            'token'           => $this->faker->md5,
            'token_secret'    => $this->faker->sha1,
        ];

        $this->http     = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleHttp')->shouldDeferMissing();
        $this->response = Mockery::mock('RestGalleries\\Http\\Response');

        $this->http
            ->shouldReceive('init')
            ->with($endPoint)
            ->times(1)
            ->andReturn($this->http);

        $this->photo = new Photo($this->http);


        $this->http
            ->shouldReceive('setAuth')
            ->with($tokenCredentials)
            ->times(1);

        $this->http
            ->shouldReceive('setCache')
            ->with('array', array())
            ->times(1);

        $this->photo->setAuth($tokenCredentials);
        $this->photo->setCache('array', array());

        $this->currentPath = dirname(__FILE__);

        // Prepare string responses
        $this->photosetsGetPhotos     = file_get_contents($this->currentPath.'/responses/flickr-photosets-getphotos.json');
        $this->photosetsGetPhotosFail = file_get_contents($this->currentPath.'/responses/flickr-photosets-getphotos-fail.json');
        $this->photosGetInfo          = file_get_contents($this->currentPath.'/responses/flickr-photos-getinfo.json');
        $this->photosGetInfoFail      = file_get_contents($this->currentPath.'/responses/flickr-photos-getinfo-fail.json');
        $this->photosGetSizes         = file_get_contents($this->currentPath .'/responses/flickr-photos-getsizes.json');

    }

    public function testAll()
    {
        $galleryId = '72157633782247768';
        $photo1_id = '8876434399';
        $photo2_id = '8877049006';

        // Prepare responses objects
        $photoset          = json_decode($this->photosetsGetPhotos, true);
        $photo             = json_decode($this->photosGetInfo);
        $photo2            = json_decode($this->photosGetInfo);
        $photo2->photo->id = $photo2_id;
        $photoSizes        = json_decode($this->photosGetSizes);

        // First gallery request (main)
        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photosets.getPhotos',
            'photoset_id'    => $galleryId,
            'extras'         => '',
            'privacy_filter' => 1,
            'per_page'       => 50,
            'page'           => 1,
            'media'          => 'photos',
        ];

        $this->http
            ->shouldReceive('setQuery')
            ->with($query)
            ->times(1);

        $this->http
            ->shouldReceive('GET')
            ->atLeast()
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('getBody')
            ->with('array')
            ->times(1)
            ->andReturn($photoset);

        // Prepare photos requests queries
        $photoQuery = $photoQuery2 = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photos.getInfo',
            'photo_id'       => $photo1_id,
            'secret'         => '',
        ];

        $sizeQuery = $sizeQuery2 = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photos.getSizes',
            'photo_id'       => $photo1_id,
        ];

        $photoQuery2['photo_id'] = $photo2_id;
        $sizeQuery2['photo_id']  = $photo2_id;

        // Into the photos loop
        //
        // First photo
        $this->http
            ->shouldReceive('setQuery')
            ->with($photoQuery)
            ->times(1);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($photo);

        $this->http
            ->shouldReceive('setQuery')
            ->with($sizeQuery)
            ->times(1);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($photoSizes);

        // Second photo
        $this->http
            ->shouldReceive('setQuery')
            ->with($photoQuery2)
            ->times(1);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($photo2);

        $this->http
            ->shouldReceive('setQuery')
            ->with($sizeQuery2)
            ->times(1);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($photoSizes);

        // Method calling and assertions
        $photos = $this->photo->all($galleryId);

        assertThat($photos, is(anInstanceOf('Illuminate\\Support\\Collection')));
        assertThat($photos, is(nonEmptyTraversable()));
        assertThat($photos[0], is(anInstanceOf('Illuminate\Support\Fluent')));


    }

    public function testAllFails()
    {
        $galleryId = '11111111111111111';

        // Prepare responses objects
        $photosetFail = json_decode($this->photosetsGetPhotosFail, true);

        // Gallery request
        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photosets.getPhotos',
            'photoset_id'    => $galleryId,
            'extras'         => '',
            'privacy_filter' => 1,
            'per_page'       => 50,
            'page'           => 1,
            'media'          => 'photos',
        ];

        $this->http
            ->shouldReceive('setQuery')
            ->with($query)
            ->times(1);

        $this->http
            ->shouldReceive('GET')
            ->times(1)
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($photosetFail);

        // Method calling and assertions
        $photos = $this->photo->all($galleryId);

        assertThat($photos, is(emptyTraversable()));

    }

    public function testFind()
    {
        $photoId = '8876434399';

        // Prepare responses objects
        $photo = json_decode($this->photosGetInfo);
        $sizes = json_decode($this->photosGetSizes);

        // Prepare photo requests queries
        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photos.getInfo',
            'photo_id'       => $photoId,
            'secret'         => '',
        ];

        $this->http
            ->shouldReceive('setQuery')
            ->with($query)
            ->times(1);

        $this->http
            ->shouldReceive('GET')
            ->times(2)
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($photo);

        $sizesQuery = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photos.getSizes',
            'photo_id'       => $photoId,
        ];

        $this->http
            ->shouldReceive('setQuery')
            ->with($sizesQuery)
            ->times(1);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($sizes);

        // Method calling and assertions
        $photo = $this->photo->find($photoId);

        assertThat($photo, is(anInstanceOf('Illuminate\Support\Fluent')));
        assertThat($photo->id, is(nonEmptyString()));
        assertThat($photo->title, is(nonEmptyString()));
        assertThat($photo->description, is(stringValue()));
        assertThat($photo->source, is(nonEmptyString()));
        assertThat($photo->source_thumbnail, is(nonEmptyString()));
        assertThat($photo->created, is(integerValue()));

    }

    public function testFindFails()
    {
        $photoId = '1111111111';

        // Prepare responses objects
        $photoFail = json_decode($this->photosGetInfoFail);

        // Prepare photo request query
        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'method'         => 'flickr.photos.getInfo',
            'photo_id'       => $photoId,
            'secret'         => '',
        ];

        $this->http
            ->shouldReceive('setQuery')
            ->with($query)
            ->times(1);

        $this->http
            ->shouldReceive('GET')
            ->times(1)
            ->andReturn($this->response);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($photoFail);

        // Method calling and assertions
        $photo = $this->photo->find($photoId);

        assertThat($photo, is(notSet('id')));

    }

}
