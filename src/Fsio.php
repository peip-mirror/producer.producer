<?php
/**
 *
 * This file is part of Producer for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Producer;

use Producer\Exception;

/**
 *
 * Filesystem input/output.
 *
 * @package producer/producer
 *
 */
class Fsio
{
    /**
     *
     * The intended filesystem root; this is easy to subvert with '../' in file
     * names.
     *
     * @var string
     *
     */
    protected $root;

    /**
     *
     * Constructor.
     *
     * @param string $root The intended filesystem root; this is easy to
     * subvert with '../' in file names.
     *
     */
    public function __construct($root)
    {
        $root = DIRECTORY_SEPARATOR . ltrim($root, DIRECTORY_SEPARATOR);
        $root = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->root = $root;
    }

    /**
     *
     * Prefix the path to a file or directory with the root.
     *
     * @param string $spec The file or directory, relative to the root.
     *
     * @return string
     *
     */
    public function path($spec)
    {
        $spec = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $spec);
        return $this->root . trim($spec, DIRECTORY_SEPARATOR);
    }

    /**
     *
     * Equivalent of file_get_contents(), with error capture.
     *
     * @param string $file The file to read from.
     *
     * @return string
     *
     */
    public function get($file)
    {
        $file = $this->path($file);

        $level = error_reporting(0);
        $result = file_get_contents($file);
        error_reporting($level);

        if ($result !== false) {
            return $result;
        }

        $error = error_get_last();
        throw new Exception($error['message']);
    }

    /**
     *
     * Equivalent of file_put_contents(), with error capture.
     *
     * @param string $file The file to write to.
     *
     * @param string $data The data to write to the file.
     *
     */
    public function put($file, $data)
    {
        $file = $this->path($file);

        $level = error_reporting(0);
        $result = file_put_contents($file, $data);
        error_reporting($level);

        if ($result !== false) {
            return $result;
        }

        $error = error_get_last();
        throw new Exception($error['message']);
    }

    /**
     *
     * Equivalent of parse_ini_file(), with error capture.
     *
     * @param string $file The file to read from.
     *
     * @param bool $sections Process sections within the file?
     *
     * @param int $mode The INI scanner mode.
     *
     * @return array
     *
     * @see parse_ini_file()
     *
     */
    public function parseIni($file, $sections = false, $mode = INI_SCANNER_NORMAL)
    {
        $file = $this->path($file);

        $level = error_reporting(0);
        $result = parse_ini_file($file, $sections, $mode);
        error_reporting($level);

        if ($result !== false) {
            return $result;
        }

        $error = error_get_last();
        throw new Exception($error['message']);
    }

    /**
     *
     * Checks to see if one of the arguments is a readable file within the root.
     *
     * @todo Restrict this to a single file when we get repository-level
     * .producer files.
     *
     * @param string $file The file to check.
     *
     * @return string The name of the found file.
     *
     */
    public function isFile($file)
    {
        $files = func_get_args();
        foreach ($files as $file) {
            $path = $this->path($file);
            if (file_exists($path) && is_readable($path)) {
                return $file;
            }
        }
        return '';
    }

    /**
     *
     * Checks to see if the argument is a directory within the root.
     *
     * @param string $dir The directory to check.
     *
     * @return bool
     *
     */
    public function isDir($dir)
    {
        $dir = $this->path($dir);
        return is_dir($dir);
    }

    /**
     *
     * Makes a directory within the root.
     *
     * @param string $dir The directory to make.
     *
     * @param string $mode The permissions.
     *
     * @param string $deep Create intervening directories?
     *
     */
     public function mkdir($dir, $mode = 0777, $deep = true)
    {
        $dir = $this->path($dir);

        $level = error_reporting(0);
        $result = mkdir($dir, $mode, $deep);
        error_reporting($level);

        if ($result !== false) {
            return;
        }

        $error = error_get_last();
        throw new Exception($error['message']);
    }
}
