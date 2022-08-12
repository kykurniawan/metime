<?php

namespace Kykurniawan\Metime;

use Kykurniawan\Metime\Exceptions\ResponseException;
use League\CommonMark\CommonMarkConverter;

class Response implements ResponseInterface
{
    private App $app;
    private int $status = 200;
    private array $headers = [];
    private $body = "";
    private $markdownConverter;

    /**
     * Construct the request object
     * 
     * @param App $app Application instance
     * @return void
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->markdownConverter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
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
    public function status(int $status): Response
    {
        $this->status = $status;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function header(string $headerName, string $headerValue): Response
    {
        array_push($this->headers, $headerName . ": " . $headerValue);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function json($data): Response
    {
        $this->header("Content-Type", "application/json; charset=utf-8");
        $this->body = json_encode($data);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function body($body): Response
    {
        if (is_string($body) || is_numeric($body)) {
            $this->body = $body;
            return $this;
        } else {
            throw new ResponseException("Unsuported response body. Its only accept string or numeric");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function html(string $html): Response
    {
        $this->header("Content-Type", "text/html; charset=utf-8");
        $this->body = $html;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function markdown(string $markdown): Response
    {
        try {
            $this->body = $this->markdownConverter->convert($markdown);
            $this->header("Content-Type", "text/html; charset=utf-8");
            return $this;
        } catch (\Throwable $th) {
            throw new ResponseException("Markdown convertion error: ", 0, $th);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function cookie(string $name, ?string $value = "", int $expire = 0, string $path = "", ?string $domain = "", bool $secure = false, bool $httponly = false): Response
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function redirect(
        string $action,
        array $queries = [],
        bool $permanent = false
    ) {
        $queries[$this->app()->getActionKey()] = $action;

        $url = $this->app()->getBaseUrl() . "?" . http_build_query($queries);

        header("Location:" . $url, true, $permanent ? 301 : 302);
        exit();
    }

    /**
     * {@inheritDoc}
     */
    public function redirectAway(string $url, bool $permanent = false)
    {
        header("Location:" . $url, true, $permanent ? 301 : 302);
        exit();
    }

    /**
     * {@inheritDoc}
     */
    public function send(): void
    {
        foreach ($this->headers as $header) {
            header($header);
        }
        http_response_code($this->status);
        echo $this->body;
        return;
    }
}
