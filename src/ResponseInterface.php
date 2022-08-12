<?php

namespace Kykurniawan\Metime;

interface ResponseInterface
{
    /**
     * Get application instance
     * 
     * @return Kykurniawan\Metime\App Applicaton instance
     */
    public function app(): App;

    /**
     * Set response status code
     * 
     * @param int $status Status code
     * @return Kykurniawan\Metime\Response Response object
     */
    public function status(int $status): Response;

    /**
     * Set response header
     * 
     * @param string $headerName Header name
     * @param string $headerValue Header value
     * @return Kykurniawan\Metime\Response Response object
     */
    public function header(string $headerName, string $headerValue): Response;

    /**
     * Set response body to json
     * 
     * @param any $data Data
     * @return Kykurniawan\Metime\Response Response object
     */
    public function json($data): Response;

    /**
     * Set response body
     * 
     * @param string|numeric $body Response body
     * @return Kykurniawan\Metime\Response Response object
     */
    public function body($body): Response;

    /**
     * Set response body from HTML
     * 
     * @param string $html HTML response body
     * @return Kykurniawan\Metime\Response Response object
     */
    public function html(string $html): Response;

    /**
     * Set response body from markdown
     * 
     * @param string $markdown Markdown
     * @return Kykurniawan\Metime\Response Response object
     */
    public function markdown(string $markdown): Response;

    /**
     * Set response cookie
     * 
     * @param string $name Cookie name
     * @param string $value Cookie value
     * @param int $expire Expire in
     * @param string $path Cookie path
     * @param string $domain Cookie domain
     * @param bool $secure Set to secure cookie
     * @param bool $httponly Set to http onlu cookie
     * @return Kykurniawan\Metime\Response Response object
     */
    public function cookie(string $name, string $value = "", int $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = false): Response;

    /**
     * Redirect response
     * 
     * @param string $action Action name
     * @param array $queries Query params
     * @param bool $permanent Set to permanent redirect
     * @return mixed
     */
    public function redirect(string $action, array $queries = [], bool $permanent = false);

    /**
     * Redirect away to other url or other domain
     * 
     * @param string $url Url
     * @param bool $permanent Set to permanent redirect
     * @return void
     */
    public function redirectAway(string $url, bool $permanent = false);

    /**
     * Send response object
     * 
     * You must call this method after setting response object
     * 
     * @return void
     */
    public function send(): void;
}
