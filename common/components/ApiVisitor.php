<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components;

use common\exceptions;

class ApiVisitor
{
    const METHOD_GET  = 0;
    const METHOD_POST = 1;

    private $socket;

    private $query;

    public function __invoke($url, $params = null, $method = self::METHOD_GET, $needHeader = false, $port = 80)
    {

        $url = $this->resolveUrl($url);

        $this->createSocket($url, $port)
            ->prepareParams($params)
            ->setMethod($method, $url['resource'])
            ->prepare($url);

        return $this->getResponse($needHeader);
    }

    private function getResponse($needHeader)
    {
        $response = [];

        $header = '';
        while($line = fgets($this->socket, 4096)) {
            if($line == "\r\n") {
                break;
            }
            $header .= $line;
        }
 
        if($needHeader) {
           $response['header'] = trim($header);
        }

        unset($line);
        $body = '';
        while($line = fgets($this->socket, 4096)) {
            $body .= $line;
        }

        if(! empty($body)) {
            $response['body'] = trim($body);
        }

        return $response;

    }

    private function prepare($url)
    {
        $socket =& $this->socket;
        fputs($socket, "Host: {$this->getUrl($url)}\r\n");
        fputs($socket, "Connection: close\r\n");
        fputs($socket, "Content-Type: application/x-www-form-urlencoded\r\n");
        if(! empty($this->query)) {
            fputs($socket, "Content-Length: ".strlen($this->query)."\r\n");
        }

        fputs($socket, "\r\n");

        if(! empty($this->query)) {
            fputs($this->socket, $this->query . "\r\n");
        }

        return $this;
    }

    private function prepareParams($params)
    {
        if($params && is_array($params)) {
            $this->query = http_build_query($params);
            return $this;
        }
        return $this;
    }

    private function setMethod($method, $resource)
    {
        if(empty($resource) || ! is_string($resource)) {
            throw new exceptions\RuntimeException(sprintf(
                'Invalid resource given. Expected as string. %s given. In %s.',
                gettype(),
                __METHOD__
            ));
        }

        if($method == 1) {
            fputs($this->socket, "POST {$resource} HTTP/1.1\r\n");
        } elseif($method == 0) {
            fputs($this->socket, "GET {$resource} HTTP/1.1\r\n");
        } else {
            throw new exceptions\InvalidArgumentException(sprintf(
                'Invalid HTTP method given. In %s.',
                __METHOD__
            ));
        }

        return $this;
    }

    private function createSocket(array $url, $port)
    {
        try {
            $socket = fsockopen($this->getUrl($url), $port);
        } catch(\Exception $ex) {
            throw new exceptions\RuntimeException(sprintf(
                'Can not create socket. In %s. Because %s.',
                __METHOD__,
                $ex->getMessage()
            ));
        }

        $this->socket = $socket;
        return $this;
    }

    private function getUrl(array $url)
    {
        return $url['domain'];
    }

    private function resolveUrl(string $url)
    {
        $url = trim($url);
        $match = [];
        $resolved = [];

        $httpPattern = '/^http:\/\/(?<http>.*?)\//';
        $httpsPattern = '/^https:\/\/(?<https>.*?)\//';

        preg_match_all($httpPattern, $url, $match);
        if(! empty($match['http'])) {
            $resolved['potocol'] = 'tcp://';
            $resolved['domain'] = array_pop($match['http']);
        } else {
            preg_match_all($httpsPattern, $url, $match);
            if(! empty($match['https'])) {
                $resolved['potocol'] = 'ssl://';
                $resolved['domain'] = array_pop($match['https']);
            }
        }

        if(empty($resolved['potocol']) || empty($resolved['domain'])) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'Invalid URL given in %s.',
                __CLASS__
            ));
        }

        if($resource = substr($url, strlen($resolved['domain']) + strpos($url, $resolved['domain']))) {
            $resolved['resource'] = $resource;
        } else {
            $resolved['resource'] = '/';
        }

        return $resolved;
    }
}
