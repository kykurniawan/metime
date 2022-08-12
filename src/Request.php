<?php

namespace Kykurniawan\Metime;

class Request implements RequestInterface
{
    private App $app;
    private $get;
    private $post;
    private $raw;
    private $cookie;
    private $data = [];
    private $headers = [];

    /**
     * Construct the request object
     * 
     * @param App $app Application instance
     * @return void
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->raw = file_get_contents("php://input");
        $this->cookie = $_COOKIE;

        if (function_exists("apache_request_headers")) {
            $this->headers = apache_request_headers();
        } else {
            $HTTP_headers = array();
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) <> "HTTP_") {
                    continue;
                }
                $single_header = str_replace(" ", "-", ucwords(str_replace("_", " ", strtolower(substr($key, 5)))));
                $HTTP_headers[$single_header] = $value;
            }
            $this->headers = $HTTP_headers;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function app(): App
    {
        return $this->app;
    }

    /**
     * {@inheritDoc}
     */
    public function method(bool $upper = true)
    {
        return $this->app()->getCurrentMethod($upper);
    }

    /**
     * {@inheritDoc}
     */
    public function action()
    {
        return $this->app()->getCurrentAction();
    }

    /**
     * {@inheritDoc}
     */
    public function get(?string $key = null)
    {
        if ($key) {
            if (isset($this->get[$key])) {
                return $this->get[$key];
            }
            return null;
        }
        return $this->get;
    }

    /**
     * {@inheritDoc}
     */
    public function post(?string $key = null)
    {
        if ($key) {
            if (isset($this->post[$key])) {
                return $this->post[$key];
            }
            return null;
        }
        return $this->post;
    }

    /**
     * {@inheritDoc}
     */
    public function raw()
    {
        return $this->raw;
    }

    /**
     * {@inheritDoc}
     */
    public function cookie(?string $key = null)
    {
        if ($key) {
            if (isset($this->cookie[$key])) {
                return $this->cookie[$key];
            }
            return null;
        }
        return $this->cookie;
    }

    /**
     * {@inheritDoc}
     */
    public function addCustomData(string $key, $value): Request
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomData(string $key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function header(?string $name = null)
    {
        if ($name) {
            if (isset($this->headers[$name])) {
                return $this->headers[$name];
            }
            return null;
        }

        return $this->headers;
    }
}
