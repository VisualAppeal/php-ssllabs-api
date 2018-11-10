<?php

namespace VisualAppeal;

class SslLabs
{
    CONST API_URL = "https://api.ssllabs.com/api/v3";

    private $returnObjects;

    /**
     * Create a new instance.
     *
     * @param bool $returnObjects Return objects instead of strings from api responses.
     */
    public function __construct(bool $returnObjects = true)
    {
        $this->returnObjects = $returnObjects;
    }

    /**
     * Check SSL Labs availability
     *
     * API Call: info
     * @see https://github.com/ssllabs/ssllabs-scan/blob/master/ssllabs-api-docs-v3.md#check-ssl-labs-availability
     *
     * @return string|object
     */
    public function info()
    {
        return $this->sendApiRequest('info');
    }

    /**
     * Invoke assessment and check progress
     *
     * API Call: analyze
     * @see https://github.com/ssllabs/ssllabs-scan/blob/master/ssllabs-api-docs-v3.md#invoke-assessment-and-check-progress
     *
     * @param string $host Hostname to analyze
     * @param bool $publish Publish results on ssllabs.com
     * @param bool $startNew Force new report
     * @param bool $fromCache Enable cache
     * @param int $maxAge Cached age in hours
     * @param string $all ("on"|"off"|"done")
     * @param bool $ignoreMismatch Proceed even when the hostname and certificate doesn't match
     * @return string|object
     */
    public function analyze(string $host, bool $publish = false, bool $startNew = false, bool $fromCache = false,
                            int $maxAge = null, string $all = null, bool $ignoreMismatch = false)
    {
        return $this->sendApiRequest(
            'analyze',
            [
                'host' => $host,
                'publish' => $publish,
                'startNew' => $startNew,
                'fromCache' => $fromCache,
                'maxAge' => $maxAge,
                'all' => $all,
                'ignoreMismatch' => $ignoreMismatch,
            ]
        );
    }

    /**
     * Same as analyze but prefer to receive cached information
     *
     * API Call: analyze
     *
     * @param string $host
     * @param bool $publish
     * @param int $maxAge
     * @param string $all
     * @param bool $ignoreMismatch
     * @return string|object
     */
    public function analyzeCached(string $host, bool $publish = false, int $maxAge = 24, string $all = 'done', bool $ignoreMismatch = false)
    {
        return $this->analyze($host, $publish, false, true, $maxAge, $all, $ignoreMismatch);
    }

    /**
     * Retrieve detailed endpoint information
     *
     * API Call: getEndpointData
     * @see https://github.com/ssllabs/ssllabs-scan/blob/master/ssllabs-api-docs-v3.md#retrieve-detailed-endpoint-information
     *
     * @param string $host
     * @param string $s IP Address
     * @param bool $fromCache Cached results
     * @return string|object
     */
    public function getEndpointData($host, $s, $fromCache = false)
    {
        $apiRequest = $this->sendApiRequest(
            'getEndpointData',
            [
                'host' => $host,
                's' => $s,
                'fromCache' => $fromCache,
            ]
        );

        return $apiRequest;
    }

    /**
     * Retrieve known status codes
     *
     * API Call: getStatusCodes
     * @see https://github.com/ssllabs/ssllabs-scan/blob/master/ssllabs-api-docs-v3.md#retrieve-known-status-codes
     *
     * @return string|object
     */
    public function getStatusCodes()
    {
        return $this->sendApiRequest('getStatusCodes');
    }

    /**
     * Retrieve root certificates
     *
     * API Call: getRootCertsRaw
     * @see https://github.com/ssllabs/ssllabs-scan/blob/master/ssllabs-api-docs-v3.md#retrieve-root-certificates
     *
     * @return string|object
     */
    public function getRootCertsRaw()
    {
        return $this->sendApiRequest('getRootCertsRaw');
    }

    /**
     * Send API request
     *
     * @param string $apiCall
     * @param array $parameters
     * @return string|object JSON from API
     */
    public function sendApiRequest(string $apiCall, array $parameters = [])
    {
        // We also want content from failed api responses
        $context = stream_context_create(
            [
                'http' => [
                    'ignore_errors' => true
                ],
            ]
        );

        $url = self::API_URL . '/' . $apiCall . $this->buildGetParameterString($parameters);

        $apiResponse = file_get_contents(
            $url,
            false,
            $context
        );

        if ($this->returnObjects === true) {
            return json_decode($apiResponse);
        }

        return $apiResponse;
    }

    /**
     * Set true to return all API responses as JSON object, false returns it as simple JSON strings
     *
     * @param bool $returnObjects
     */
    public function setReturnJsonObjects(bool $returnObjects)
    {
        $this->returnObjects = $returnObjects;
    }

    /**
     * Getter for returnObjects
     *
     * @return bool true returns all API responses as JSON object, false returns it as simple JSON string
     */
    public function getReturnJsonObjects()
    {
        return $this->returnObjects;
    }

    /**
     * Helper function to build get parameter string for URL
     *
     * @param array $parameters
     * @return string
     */
    private function buildGetParameterString(array $parameters)
    {
        $string = '';

        $counter = 0;
        foreach ($parameters as $name => $value) {
            if (!is_string($name) || (!is_string($value) && !is_bool($value) && !is_int($value))) {
                continue;
            }

            if (is_bool($value)) {
                $value = ($value) ? 'on' : 'off';
            }

            $string .= ($counter == 0) ? '?' : '&';
            $string .= urlencode($name) . '=' . urlencode($value);

            $counter++;
        }

        return $string;
    }
}