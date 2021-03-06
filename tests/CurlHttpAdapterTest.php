<?php

namespace Http\Adapter\Buzz\Tests;

use Buzz\Client\Curl;
use Buzz\Message\RequestInterface as BuzzRequestInterface;
use Http\Client\Exception\RequestException;

class CurlHttpAdapterTest extends HttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createBuzzClient()
    {
        $curl = new Curl();
        $curl->setMaxRedirects(0);

        return $curl;
    }

    /**
     * @dataProvider requestProvider
     * @group        integration
     */
    public function testSendRequest($method, $uri, array $headers, $body)
    {
        $validMethods = [
            BuzzRequestInterface::METHOD_POST,
            BuzzRequestInterface::METHOD_PUT,
            BuzzRequestInterface::METHOD_DELETE,
            BuzzRequestInterface::METHOD_PATCH,
            BuzzRequestInterface::METHOD_OPTIONS,
        ];

        if (!in_array($method, $validMethods, true) && $body) {
            $this->exception(RequestException::class, sprintf('Buzz\Client\Curl does not support %s requests with a body', $method));
        }

        parent::testSendRequest($method, $uri, $headers, $body);
    }

    /**
     * @dataProvider requestWithOutcomeProvider
     * @group        integration
     */
    public function testSendRequestWithOutcome($uriAndOutcome, $protocolVersion, array $headers, $body)
    {
        if ($body && '1.1' === $protocolVersion) {
            $this->exception(RequestException::class, 'Buzz\Client\Curl does not support GET requests with a body');
        }

        parent::testSendRequestWithOutcome($uriAndOutcome, $protocolVersion, $headers, $body);
    }

    /**
     * To be compatible with both PHPUnit 5 and 6.
     *
     * @param string $class
     * @param string $message
     */
    private function exception($class, $message)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($class);
            $this->expectExceptionMessage($message);
        } else {
            $this->setExpectedException($class, $message);
        }
    }
}
