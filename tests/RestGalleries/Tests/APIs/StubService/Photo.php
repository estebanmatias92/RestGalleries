<?php namespace RestGalleries\Tests\APIs\StubService;

class Photo extends \RestGalleries\APIs\ApiPhoto
{
    protected $endPoint = 'http://www.mockservice.com/rest/';

    protected function fetchIds($galleryId)
    {
        $endPoint = 'gallery/';
        $endPoint .= $galleryId;
        $endPoint .= '/photos';

        $body = $this->newRequest()
            ->GET($endPoint)
            ->getBody('array');

        return $this->extractIdsArray($body);

    }

    private function extractIdsArray($source)
    {
        $photos = $source['photos'];

        if (! empty($photos)) {
            return array_pluck($photos, 'id');
        }

    }

    protected function fetchPhoto($id)
    {
        $body = $this->newRequest()
            ->GET('photo/' . $id)
            ->getBody();

        return $this->extractPhotoArray($body);

    }

    private function extractPhotoArray($source)
    {
        if (isset($source->error)) {
            return;
        }

        $photoData = &$source->photo;

        $photo                     = [];
        $photo['id']               = $photoData->id;
        $photo['title']            = $photoData->title;
        $photo['description']      = $photoData->description;
        $photo['url']              = $photoData->url;
        $photo['created']          = $photoData->create_at;
        $photo['views']            = $photoData->views;
        $photo['source']           = $photoData->photo_natural;
        $photo['source_thumbnail'] = $photoData->photo_thumbnail;

        return $photo;

    }

}
