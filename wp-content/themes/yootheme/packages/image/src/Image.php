<?php

namespace YOOtheme;

use YOOtheme\Image\GDResource;

/**
 * @method doCrop($width, $height, $x, $y)
 * @method doResize($width, $height, $dstWidth, $dstHeight, $background = 'transparent')
 * @method doRotate($angle, $background = 'transparent')
 * @method doCopy($width, $height, $dstX, $dstY, $srcX, $srcY, $dstWidth, $dstHeight, $srcWidth, $srcHeight, $background = 'transparent')
 */
class Image
{
    /**
     * @var string
     */
    public $file;

    /**
     * @var string|null
     */
    public $path;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $info;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $quality = 80;

    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @var string
     */
    protected $resourceClass = Image\BaseResource::class;

    /**
     * @var array
     */
    protected $operations = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor.
     *
     * @param string $file
     * @param bool   $resource
     */
    public function __construct($file, $resource = true)
    {
        $this->file = strtr($file, '\\', '/');
        $this->setAttribute('resource', $resource);

        Event::emit('image.create', $this);

        if ($file = $this->getFile()) {
            [$this->width, $this->height, $this->type, $this->info] = ImageProvider::getInfo($file);
        }

        if ($resource && extension_loaded('gd')) {
            $this->resource = GDResource::create($file, $this->type);
            $this->resourceClass = GDResource::class;
        }
    }

    /**
     * Gets the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets the height.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Gets the width.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Gets the hash.
     *
     * @return string|false
     */
    public function getHash()
    {
        if (!$this->operations) {
            return false;
        }

        return hash('crc32b', $this . (($file = $this->getFile()) ? filemtime($file) : ''));
    }

    /**
     * Gets the file path.
     *
     * @return string|false
     */
    public function getFile()
    {
        if (is_null($this->path)) {
            $this->path = is_file($file = Path::resolve('~', $this->file)) ? $file : false;
        }

        return $this->path;
    }

    /**
     * Gets the cache filename.
     *
     * @param string $path
     *
     * @return string
     */
    public function getFilename($path = null)
    {
        $hash = $this->getHash();
        $file = pathinfo($this->file);

        if ($path) {
            $path = rtrim($path, '\/') . '/';
        }

        return $path .
            ($hash
                ? sprintf('%s-%s.%s', $file['filename'], $hash, $this->type)
                : $file['basename']);
    }

    /**
     * Gets an attribute value.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Sets an attribute value on the instance.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Checks the portrait orientation.
     *
     * @return bool
     */
    public function isPortrait()
    {
        return $this->height > $this->width;
    }

    /**
     * Checks the landscape orientation.
     *
     * @return bool
     */
    public function isLandscape()
    {
        return $this->width >= $this->height;
    }

    /**
     * Convert the image.
     *
     * @param string $type
     * @param int    $quality
     *
     * @return static
     */
    public function type($type, $quality = 80)
    {
        $image = clone $this;
        $image->type = $type;
        $image->quality = $quality;

        return $image;
    }

    /**
     * Crops the image.
     *
     * @param int|string $width
     * @param int|string $height
     * @param int|string $x
     * @param int|string $y
     *
     * @return static
     */
    public function crop($width = null, $height = null, $x = 'center', $y = 'center')
    {
        $ratio = $this->width / $this->height;
        $width = $this->parseValue($width, $this->width);
        $height = $this->parseValue($height, $this->height);

        if ($ratio > $width / $height) {
            $image = $this->resize((int) round($height * $ratio), $height);
        } else {
            $image = $this->resize($width, (int) round($width / $ratio));
        }

        if ($x === 'left') {
            $x = 0;
        } elseif ($x === 'right') {
            $x = $image->width - $width;
        } elseif ($x === 'center') {
            $x = ($image->width - $width) / 2;
        }

        if ($y === 'top') {
            $y = 0;
        } elseif ($y === 'bottom') {
            $y = $image->height - $height;
        } elseif ($y === 'center') {
            $y = ($image->height - $height) / 2;
        }

        $image->doCrop($image->width = $width, $image->height = $height, intval($x), intval($y));

        return $image;
    }

    /**
     * Resizes the image.
     *
     * @param int|string $width
     * @param int|string $height
     * @param string     $background
     *
     * @return static
     */
    public function resize($width = null, $height = null, $background = 'crop')
    {
        if ($background == 'cover') {
            return $this->crop($width, $height);
        }

        $image = clone $this;
        $width = $this->parseValue($width, $this->width);
        $height = $this->parseValue($height, $this->height);

        if ($this->width != $width) {
            $scale = $this->width / $width;
        }

        if ($this->height != $height) {
            $scale = isset($scale) ? max($scale, $this->height / $height) : $this->height / $height;
        }

        if (!isset($scale) || !$scale) {
            $scale = 1.0;
        }

        $dstWidth = intval(round($this->width / $scale));
        $dstHeight = intval(round($this->height / $scale));

        if ($background == 'fill') {
            $image->doResize($image->width = $width, $image->height = $height, $width, $height);
        } elseif ($background === 'crop') {
            $image->doResize(
                $image->width = $dstWidth,
                $image->height = $dstHeight,
                $dstWidth,
                $dstHeight,
            );
        } else {
            $image->doResize(
                $image->width = $width,
                $image->height = $height,
                $dstWidth,
                $dstHeight,
                $background,
            );
        }

        return $image;
    }

    /**
     * Rotate the image.
     *
     * @param int    $angle
     * @param string $background
     *
     * @return static
     */
    public function rotate($angle, $background = 'transparent')
    {
        $image = clone $this;
        $image->doRotate($angle, $background);

        // update width/height for rotation
        if (in_array($angle, [90, 270], true)) {
            [$image->height, $image->width] = [$this->width, $this->height];
        }

        return $image;
    }

    /**
     * Flip the image.
     *
     * @param bool $horizontal
     * @param bool $vertical
     *
     * @return static
     */
    public function flip($horizontal, $vertical)
    {
        $srcX = $horizontal ? $this->width - 1 : 0;
        $srcY = $vertical ? $this->height - 1 : 0;
        $srcW = $horizontal ? -$this->width : $this->width;
        $srcH = $vertical ? -$this->height : $this->height;

        $image = clone $this;
        $image->doCopy(
            $this->width,
            $this->height,
            0,
            0,
            $srcX,
            $srcY,
            $this->width,
            $this->height,
            $srcW,
            $srcH,
        );

        return $image;
    }

    /**
     * Thumbnail the image.
     *
     * @param int|string    $width
     * @param int|string    $height
     * @param bool   $flip
     * @param string $x
     * @param string $y
     * @return static
     */
    public function thumbnail(
        $width = null,
        $height = null,
        $flip = false,
        $x = 'center',
        $y = 'center'
    ) {
        if ($flip) {
            $width = strpos($width, '%') ? $this->parseValue($width, $this->width) : $width;
            $height = strpos($height, '%') ? $this->parseValue($height, $this->height) : $height;

            if ($this->isPortrait() && $width > $height) {
                [$width, $height] = [$height, $width];
            } elseif ($this->isLandscape() && $height > $width) {
                [$width, $height] = [$height, $width];
            }
        }

        return is_numeric($width) && is_numeric($height)
            ? $this->crop($width, $height, $x, $y)
            : $this->resize($width, $height);
    }

    /**
     * Apply multiple operations.
     *
     * @param array $operations
     *
     * @return static
     */
    public function apply(array $operations)
    {
        $image = clone $this;

        foreach ($operations as $name => $args) {
            if (is_int($name)) {
                $name = $args[0];
                $args = $args[1];
            }

            if (is_string($args)) {
                $args = explode(',', trim($args));
            }

            if (method_exists($image, $name)) {
                $image->operations[] = [$name, $args];
            }

            $image = call_user_func_array([$image, $name], $args);
        }

        return $image;
    }

    /**
     * Saves the image.
     *
     * @param string $file
     * @param string $type
     * @param int    $quality
     * @param array  $info
     *
     * @return string|false
     */
    public function save($file, $type = null, $quality = null, array $info = [])
    {
        if (!$type) {
            $type = $this->type;
        }

        if (!$quality) {
            $quality = $this->quality;
        }

        if (!$info) {
            $info = $this->info;
        }

        return call_user_func(
            "{$this->resourceClass}::save",
            $this->resource,
            $file,
            $type,
            $quality,
            $info,
        )
            ? $file
            : false;
    }

    /**
     * Calls static resource methods.
     *
     * @param string $name
     * @param array  $args
     *
     * @return $this
     */
    public function __call($name, $args)
    {
        $method = [$this->resourceClass, $name];

        // call image resource
        if (is_callable($method)) {
            if ($this->resource) {
                $this->resource = call_user_func_array(
                    $method,
                    array_merge([$this->resource], $args),
                );
            }
        }

        return $this;
    }

    /**
     * Gets image as json string.
     *
     * @return string
     */
    public function __toString()
    {
        $query = ['file' => $this->file];

        foreach ($this->operations as [$name, $args]) {
            $query[$name] = join(',', $args);
        }

        return json_encode($query, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Parses a percent value.
     *
     * @param mixed $value
     * @param int   $baseValue
     *
     * @return int
     */
    protected function parseValue($value, $baseValue)
    {
        if (str_ends_with(strval($value), '%')) {
            $value = round($baseValue * (intval($value) / 100.0));
        }

        return intval($value) ?: $baseValue;
    }
}
