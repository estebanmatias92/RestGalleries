<?php namespace RestGalleries\Interfaces;

/**
 * ApiGallery description.
 */
interface ApiGallery
{
    public function setAccount(array $data);
    public function all();
    public function find($id);
}
