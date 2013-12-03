<?php

namespace Ff\Api;

interface ContextInterface
{
    public function getParam($key = null, $default = null);

    public function getPost($key = null, $default = null);

    public function getRequestUri();

    public function getScheme();

    public function getHttpHost();
}