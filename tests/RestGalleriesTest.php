<?php

use RestGalleries\RestGalleries;

/**
 * Rest-model test.
 */
class RestGalleriesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Add your api and account(s) data for the test
     *
     * @return   array           Api and account data.
     */
    public function apiProvider()
    {
        return [
            /*[
                '[api-name]',
                '[api-key]',
                '[secret-key]',
                '[user]',
                '[user-id]',
                '[gallery-id]',
            ],*/
            [
                'flickr',
                '83e6915af3ba89ab2166a69f7dde4c07',
                '',
                'flickr',
                '66956608@N06',
                '72157640799982165',
            ],
        ];
    }

    public function setUp()
    {
        $this->apisNamespace = 'RestGalleries\\APIs\\';
    }

    /**
     * @dataProvider apiProvider
     */
    public function testFindUserReturnApiUserObject($api, $apiKey, $secretKey, $user)
    {

        $stub = $this->getMockForAbstractClass('RestGalleries\\RestGalleries');

        $stub->__set('api', $api);
        $stub->__set('apiKey', $apiKey);
        $stub->__set('secretKey', $secretKey);
        $stub->__set('developmentMode', true);

        $api       = ucfirst($api);
        $apiClass  = $this->apisNamespace . $api . '\\' . $api .  'User';
        $apiObject = new $apiClass($apiKey, $secretKey, $developmentMode = true);

        $stub::staticExpects($this->any())
             ->method('findUser')
             ->with($user)
             ->will($this->returnValue($apiObject));

        $this->assertInstanceOf($apiClass, $stub->findUser($user));
    }

    /**
     * @dataProvider apiProvider
     */
    public function testAllReturnArrayOfApiGalleryObjects($api, $apiKey, $secretKey, $user, $userId)
    {
        $stub = $this->getMockForAbstractClass('RestGalleries\\RestGalleries');

        $stub->__set('api', $api);
        $stub->__set('apiKey', $apiKey);
        $stub->__set('secretKey', $secretKey);
        $stub->__set('developmentMode', true);

        $api       = ucfirst($api);
        $apiClass  = $this->apisNamespace . $api . '\\' . $api .  'Gallery';
        $apiObject = new $apiClass($apiKey, $secretKey, $developmentMode = true);

        $args = [
            'user_id' => $userId,
        ];

        $stub::staticExpects($this->any())
             ->method('all')
             ->with($args)
             ->will($this->returnValue(array()));

        $galleries = $stub->all($args);

        $this->assertInstanceOf($apiClass, $galleries[0]);
    }

    /**
     * @dataProvider apiProvider
     */
    public function testFindReturnApiGalleryObject($api, $apiKey, $secretKey, $user, $userId, $galleryId)
    {

        $stub = $this->getMockForAbstractClass('RestGalleries\\RestGalleries');

        $stub->__set('api', $api);
        $stub->__set('apiKey', $apiKey);
        $stub->__set('secretKey', $secretKey);
        $stub->__set('developmentMode', true);

        $api       = ucfirst($api);
        $apiClass  = $this->apisNamespace . $api . '\\' . $api .  'Gallery';
        $apiObject = new $apiClass($apiKey, $secretKey, $developmentMode = true);

        $args = [
            'user_id'    => $userId,
        ];

        $stub::staticExpects($this->any())
             ->method('find')
             ->with($args)
             ->will($this->returnValue($apiObject));

        $this->assertInstanceOf($apiClass, $stub->find($args, $galleryId));
    }

}
