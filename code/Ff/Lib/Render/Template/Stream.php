<?php

namespace Ff\Lib\Render\Template;

use Ff\Lib\Render\Template\Transport;

class Stream
{
    /**
     * Current stream position.
     *
     * @var int
     */
    protected $_pos = 0;

    protected $_path;
    
    /**
     * Data for streaming.
     *
     * @var string
     */
    protected $_data;

    /**
     * Stream stats.
     *
     * @var array
     */
    protected $_stat;

    /**
     * Opens the script file and converts markup.
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        // get the view script source
        $this->_path = str_replace('ff.template://', '', $path);
        $this->_data = file_get_contents($this->_path);

        /**
         * If reading the file failed, update our local stat store
         * to reflect the real stat of the file, then return on failure
         */
        if ($this->_data === false) {
            $this->_stat = stat($this->_path);
            return false;
        }

        $this->_data = preg_replace_callback('#<data (.*?)>(.*?)</data>#', array($this, 'replace'), $this->_data);

        /**
         * file_get_contents() won't update PHP's stat cache, so we grab a stat
         * of the file to prevent additional reads should the script be
         * requested again, which will make include() happy.
         */
        $this->_stat = stat($this->_path);

        return true;
    }
    
    private function replace($matches)
    {
        $arguments = $matches[1];
        $path = $matches[2];
        
        $data = Transport::get($this->_path);
        if ($data) {
            return '<h1>' . $data->get($path) . '</h1>';
        }
    }

    /**
     * Included so that __FILE__ returns the appropriate info
     *
     * @return array
     */
    public function url_stat()
    {
        return $this->_stat;
    }

    /**
     * Reads from the stream.
     */
    public function stream_read($count)
    {
        $ret = substr($this->_data, $this->_pos, $count);
        $this->_pos += strlen($ret);
        return $ret;
    }


    /**
     * Tells the current position in the stream.
     */
    public function stream_tell()
    {
        return $this->_pos;
    }


    /**
     * Tells if we are at the end of the stream.
     */
    public function stream_eof()
    {
        return $this->_pos >= strlen($this->_data);
    }


    /**
     * Stream statistics.
     */
    public function stream_stat()
    {
        return $this->_stat;
    }


    /**
     * Seek to a specific point in the stream.
     */
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->_data) && $offset >= 0) {
                $this->_pos = $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->_pos += $offset;
                    return true;
                } else {
                    return false;
                }
                break;

            case SEEK_END:
                if (strlen($this->_data) + $offset >= 0) {
                    $this->_pos = strlen($this->_data) + $offset;
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