<?php

use RestGalleries\APIs\Flickr\Gallery;

class GalleryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $endPoint = 'http://api.flickr.com/services/rest/';

        $this->auth     = Mockery::mock('RestGalleries\\Auth\\OhmyAuth\\OhmyAuth');
        $this->http     = Mockery::mock('RestGalleries\\Http\\Guzzle\\GuzzleHttp');
        $this->photo    = Mockery::mock('RestGalleries\\APIs\\Flickr\\Photo');
        $this->response = Mockery::mock('RestGalleries\\Http\\Response');

        $this->http
            ->shouldReceive('init')
            ->with($endPoint)
            ->times(1)
            ->andReturn($this->http);

        $this->gallery = new Gallery($this->auth, $this->http, $this->photo);

        // Prepare string responses
        $this->apiResponses = [
            'flickr_photosets_get_info'      => file_get_contents(__DIR__.'/responses/gallery/flickr-photosets-getinfo.json'),
            'flickr_photosets_get_list'      => file_get_contents(__DIR__.'/responses/gallery/flickr-photosets-getlist.json'),
            'flickr_photosets_get_info_fail' => file_get_contents(__DIR__.'/responses/gallery/flickr-photosets-getinfo-fail.json'),
            'flickr_photosets_get_list_fail' => file_get_contents(__DIR__.'/responses/gallery/flickr-photosets-getlist-fail.json')
        ];

    }

    public function testAll()
    {
        $query = [
            'format'               => 'json',
            'nojsoncallback'       => 1,
            'page'                 => 1,
            'per_page'             => 50,
            'primary_photo_extras' => ''
        ];

        $query2 = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'photoset_id'    => '72157633782247768'
        ];

        $query3                = $query2;
        $query3['photoset_id'] = '72157633780835561';

        $responseFlickrPhotosetsGetList                = json_decode($this->apiResponses['flickr_photosets_get_list'], true);
        $responseFlickrPhotosetsGetInfo                = json_decode($this->apiResponses['flickr_photosets_get_info']);
        $responseFlickrPhotosetsGetInfo2               = json_decode($this->apiResponses['flickr_photosets_get_info']);
        $responseFlickrPhotosetsGetInfo2->photoset->id = $query3['photoset_id'];

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
            ->andReturn($responseFlickrPhotosetsGetList);

        $this->http
            ->shouldReceive('setQuery')
            ->with($query2)
            ->times(1);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($responseFlickrPhotosetsGetInfo);

        $this->photo
            ->shouldReceive('all');

        $this->http
            ->shouldReceive('setQuery')
            ->with($query3)
            ->times(1);

        $this->response
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn($responseFlickrPhotosetsGetInfo2);

        $this->photo
            ->shouldReceive('all');

        $galleries = $this->gallery->all();

        assertThat($galleries, is(anInstanceOf('Illuminate\Support\Collection')));
        assertThat($galleries, is(nonEmptyTraversable()));

    }

    public function testAllFails()
    {
        $query = [
            'format'               => 'json',
            'nojsoncallback'       => 1,
            'page'                 => 1,
            'per_page'             => 50,
            'primary_photo_extras' => ''
        ];

        $responseFlickrPhotosetsGetListFail = json_decode($this->apiResponses['flickr_photosets_get_list_fail'], true);

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
            ->with('array')
            ->times(1)
            ->andReturn($responseFlickrPhotosetsGetListFail);

        $galleries = $this->gallery->all();

        assertThat($galleries, is(nullValue()));
    }

    public function testFind()
    {
        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'photoset_id'    => '72157633782247768'
        ];

        $responseFlickrPhotosetsGetInfo   = json_decode($this->apiResponses['flickr_photosets_get_info']);
        $responseFlickrPhotosetsGetPhotos = new Illuminate\Support\Collection(
            [
                new Illuminate\Support\Fluent([
                    'id'               => '',
                    'title'            => '',
                    'description'      => '',
                    'source'           => '',
                    'source_thumbnail' => '',
                    'created'          => '',
                ])
            ]
        );

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
            ->andReturn($responseFlickrPhotosetsGetInfo);

        $this->photo
            ->shouldReceive('all')
            ->with($query['photoset_id'])
            ->andReturn($responseFlickrPhotosetsGetPhotos);

        $gallery = $this->gallery->find($query['photoset_id']);

        assertThat($gallery, is(anInstanceOf('Illuminate\Support\Fluent')));
        assertThat($gallery->id, is(nonEmptyString()));
        assertThat($gallery->title, is(nonEmptyString()));
        assertThat($gallery->description, is(nonEmptyString()));
        assertThat($gallery->photos, is(nonEmptyTraversable()));
        assertThat($gallery->created, is(nonEmptyString()));
        assertThat($gallery->url, is(nonEmptyString()));
        assertThat($gallery->size, is(integerValue()));
        assertThat($gallery->user_id, is(nonEmptyString()));
        assertThat($gallery->thumbnail, is(nonEmptyString()));
        assertThat($gallery->views, is(integerValue()));

    }

    public function testFindFails()
    {
        $query = [
            'format'         => 'json',
            'nojsoncallback' => 1,
            'photoset_id'    => '11111111111111111'
        ];

        $responseFlickrPhotosetsGetInfoFail = json_decode($this->apiResponses['flickr_photosets_get_info_fail']);

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
            ->andReturn($responseFlickrPhotosetsGetInfoFail);

        $gallery = $this->gallery->find($query['photoset_id']);

        assertThat($gallery, is(nullValue()));

    }

}
