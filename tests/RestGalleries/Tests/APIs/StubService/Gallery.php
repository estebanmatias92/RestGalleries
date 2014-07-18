<?php namespace RestGalleries\Tests\APIs\StubService;

class Gallery extends \RestGalleries\APIs\ApiGallery
{
    protected $endPoint = 'http://www.mockservice.com/rest/';

    protected function fetchIds()
    {
        $body = $this->newRequest()
            ->GET('galleries')
            ->getBody('array');

        return $this->extractIdsArray($body);

    }

    private function extractIdsArray($source)
    {
        $galleries = $source['galleries'];

        if (! empty($galleries)) {
            return array_pluck($galleries, 'id');
        }

    }

    protected function fetchGallery($id)
    {
        $body = $this->newRequest()
            ->GET('gallery/' . $id)
            ->getBody();

        return $this->extractGalleryArray($body);

    }

    private function extractGalleryArray($source)
    {
        if (isset($source->error)) {
            return;
        }

        $galleryData = &$source->gallery;
        $photo       = $this->newPhoto();

        $gallery                = [];
        $gallery['id']          = $galleryData->id;
        $gallery['title']       = $galleryData->title;
        $gallery['description'] = $galleryData->description;
        $gallery['photos']      = $photo->all($galleryData->id);
        $gallery['created']     = $galleryData->create_at;
        $gallery['url']         = $galleryData->url;
        $gallery['size']        = $galleryData->photos;
        $gallery['user_id']     = $galleryData->user_id;
        $gallery['thumbnail']   = $galleryData->thumbnail_id;
        $gallery['views']       = $galleryData->views;

        return $gallery;

    }

}
