<?php

namespace Kykurniawan\Metime;

interface RequestInterface
{
    /**
     * Get application instance
     * 
     * @return Kykurniawan\Metime\App Applicaton instance
     */
    public function app(): App;

    /**
     * Get current method
     * 
     * @param bool $upper Return method in uppercase letter
     * @return string Current method
     */
    public function method(bool $upper = true);

    /**
     * Get current action
     * 
     * @return string Current action
     */
    public function action();

    /**
     * Get query param
     * 
     * @param ?string $key Query param
     * @return ?string Parameter value
     */
    public function get(?string $key = null);

    /**
     * Get post data from request
     * 
     * @param ?string $key Post key
     * @return array|string Post data
     */
    public function post(?string $key = null);

    /**
     * Get raw data from request
     * 
     * @return string Raw data
     */
    public function raw();

    /**
     * Get cookie from request
     * 
     * @param ?string $key Cookie key
     * @return array|string Cookie
     */
    public function cookie(?string $key = null);

    /**
     * Add custom data to request
     * Only works in middlwware
     * 
     * @param string Data key
     * @param any $value Data value
     * @return Kykurniawan\Metime\Request Request object
     */
    public function addCustomData(string $key, $value): Request;

    /**
     * Get custom data from request
     * 
     * @param string $key Data key
     * 
     * @return any Data value
     */
    public function getCustomData(string $key);

    /**
     * Get header from request
     * 
     * @param ?string $name Header name
     * @return ?string Header value
     */
    public function header(?string $name = null);
}
