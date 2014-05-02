<?php namespace RestGalleries\Support\Traits;

trait Overload
{
    /**
     * Array to store news attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Update attributes array.
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes = array())
    {
        $this->attributes = $attributes;
    }

    /**
     * Get current stored attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set an attribute in an attribute array.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setAttribute($key, $value)
    {
        if (!is_null($value))
        {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get a particular attribute from the array.
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
           return $this->attributes[$key];
        } else {
            throw new \Exception('Undefined property ' . __CLASS__ . '::' . $key);
        }

    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}
