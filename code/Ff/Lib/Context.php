<?php

namespace Ff\Lib;

use Ff\Api\ContextInterface;

class Context implements ContextInterface
{
    private $uri = null;

    private $params = array();

    private $post;

    private $cookie;

    private $session;

    private $server;

    public function __construct(& $params, & $post, & $cookie, & $session, & $server)
    {
        $this->params = & $params;
        $this->post = & $post;
        $this->cookie = & $cookie;
        $this->session = & $session;
        $this->server = & $server;

        $this->parseUri();
    }

    public function getParam($key = null, $default = null)
    {
        if ($key === null) {
            return $this->params;
        }
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        return isset($this->post[$key]) ? $this->post[$key] : $default;
    }

    public function getCookie($key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookie;
        }
        return isset($this->cookie[$key]) ? $this->cookie[$key] : $default;
    }

    public function getSession($key = null, $default = null)
    {
        if ($key === null) {
            return $this->session;
        }
        return isset($this->session[$key]) ? $this->session[$key] : $default;
    }

    public function getServer($key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }
        return isset($this->server[$key]) ? $this->server[$key] : $default;
    }

    public function getRequestUri()
    {
        return $this->uri;
    }

    public function getScheme()
    {
        if ($this->getServer('HTTPS') === 'on') {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }
        return $scheme;
    }

    public function getHttpHost()
    {
        $host = $this->getServer('HTTP_HOST');
        if (empty($host)) {
            $scheme = $this->getScheme();
            $name = $this->getServer('SERVER_NAME');
            $port = $this->getServer('SERVER_PORT');
            if (null === $name) {
                $host = '';
            } elseif (('http' === $scheme && $port == 80) || ('https' === $scheme && $port == 443)) {
                $host = $name;
            } else {
                $host = $name . ':' . $port;
            }
        }

        return $host;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    protected function parseUri()
    {
        $this->uri = $this->getServer('REQUEST_URI', '');
        if (!empty($this->uri)) {
            $host = $this->getScheme() . '://' . $this->getHttpHost();
            if (strpos($this->uri, $host) === 0) {
                $this->uri = substr($this->uri, strlen($host));
            }

            if (strpos($this->uri, '?') !== false) {
                list ($this->uri, $arguments) = explode('?', $this->uri);
                parse_str($arguments, $params);
                $this->params = $params;
            }
        }
    }
}
