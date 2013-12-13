<?php

namespace Ff\Lib\Render\Html;

use Ff\Lib\Data;
use Ff\Lib\Render\Html\Template;
use Ff\Lib\Render\Html\Template\Transport;
use Ff\Lib\Render\Html\Template\Processor;

class Stream
{
    private static $renders = array();

    private static $templates = array();

    protected $pos = 0;

    protected $path;

    protected $data;

    protected $stat;

    /**
     *
     */
    public function __construct()
    {
        global $bus;
        $this->bus = $bus;
    }

    /**
     * @param $path
     * @return string
     */
    public function render($path)
    {
        $this->path = $path;
        return $this->loadTemplate($path);
    }

    private function loadTemplate($originalPath)
    {
        if (!isset(self::$templates[$this->path])) {
            $path = str_replace(DIR_CODE, '', $originalPath);

            $pathArray = explode('/', $path);
            array_shift($pathArray); // remove namespace

            $searchPath = DIR_CODE . '*/' . implode('/', $pathArray);

            $templates = glob($searchPath);

            $filteredTemplates = array_diff($templates, array($originalPath));

            $template = new Template($this->bus);
            $template->load($originalPath);

            if ($filteredTemplates) {
                foreach ($filteredTemplates as $path) {
                    $content = new Template($this->bus);
                    $content->load($path);
                    $root = $content->getRoot();
                    $template->extend($root);
                }
            }

            self::$templates[$this->path] = $template;
        }

        $processor = new Processor($this->bus);

        return $processor->process(self::$templates[$this->path]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        // get the view script source
        $this->path = str_replace('ff.template.html://', '', $path);

        $this->stat = stat($this->path);

        $this->data = $this->loadTemplate($this->path);

        return true;
    }

    /**
     * Included so that __FILE__ returns the appropriate info
     *
     * @return array
     */
    public function url_stat()
    {
        return $this->stat;
    }

    /**
     * Reads from the stream.
     */
    public function stream_read($count)
    {
        $ret = substr($this->data, $this->pos, $count);
        $this->pos += strlen($ret);
        return $ret;
    }

    /**
     * Tells the current position in the stream.
     */
    public function stream_tell()
    {
        return $this->pos;
    }


    /**
     * Tells if we are at the end of the stream.
     */
    public function stream_eof()
    {
        return $this->pos >= strlen($this->data);
    }


    /**
     * Stream statistics.
     */
    public function stream_stat()
    {
        return $this->stat;
    }


    /**
     * Seek to a specific point in the stream.
     */
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->data) && $offset >= 0) {
                $this->pos = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->pos += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen($this->data) + $offset >= 0) {
                    $this->pos = strlen($this->data) + $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            default:
                return false;
        }
    }
}
