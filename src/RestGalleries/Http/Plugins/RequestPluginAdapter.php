<?php namespace RestGalleries\Http\Plugins;



/**
 * Allows normalize plugins from Http client to then be used, in a homogeneous way by request adapter.
 */
interface RequestPluginAdapter
{
    public function add();
}
