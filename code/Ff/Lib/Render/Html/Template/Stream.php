<?php

namespace Ff\Lib\Render\Html\Template;

use Ff\Lib\Render\Html\Template\Configuration;
use Ff\Lib\Render\Html\Template\Transport;

class Stream
{
    private static $renders = array();

    protected $pos = 0;

    protected $path;

    protected $data;

    protected $stat;

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        // get the view script source
        $this->path = str_replace('ff.template.phtml://', '', $path);

        $this->stat = stat($this->path);

        $this->data = $this->loadTemplate($this->path);

        $this->data = preg_replace_callback('#<data (.*?)>(.*?)</data>#', array($this, 'replace'), $this->data);

        return true;
    }

    private function loadTemplate($originalPath)
    {
        $path = str_replace(DIR_CODE, '', $originalPath);

        $pathArray = explode('/', $path);
        array_shift($pathArray); // remove namespace

        $searchPath = DIR_CODE . '*/' . implode('/', $pathArray);

        $templates = glob($searchPath);

        $filteredTemplates = array_diff($templates, array($originalPath));

        $original = new Configuration();
        $original->load($originalPath);

        if ($filteredTemplates) {
            foreach ($filteredTemplates as $template) {
                $content = new Configuration();
                $content->load($template);
                $original->extend($content);
            }
        }

        $resultTemplate = $original->toXml();

        $resultTemplate = str_replace('<?xml version="1.0"?>', '', $resultTemplate);

        return $resultTemplate;
    }
    
    private function replace($matches)
    {
        $arguments = $this->parseArguments($matches[1]);
        $path = $matches[2];

        $data = Transport::get($this->path);
        if ($data) {
            $uiTypeRender = $this->getUiTypeRender($arguments);
            return $uiTypeRender->render($data->get($path), $arguments);
        }
    }

    private function parseArguments($rawArguments)
    {
        $arguments = array();
        preg_match_all('#(.*?)="(.*?)"#', $rawArguments, $matches);
        if ($matches) {
            $arguments = array_combine($matches[1], $matches[2]);
        }
        return $arguments;
    }

    private function getUiTypeRender($arguments)
    {
        $uiType = isset($arguments['ui-type']) ? $arguments['ui-type'] : 'Text';
        if (!isset(self::$renders[$uiType])) {
            $uiType = str_replace('_', '\\', $uiType);
            $class = 'Ff\\Lib\\Ui\\' . $uiType;
            self::$renders[$uiType] = new $class();
        }

        return self::$renders[$uiType];
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
