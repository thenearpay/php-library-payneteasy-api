<?php

namespace PaynetEasy\PaynetEasyApi\Transport;

use PaynetEasy\PaynetEasyApi\PaymentData\QueryConfig;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-06-25 at 15:51:46.
 */
class GatewayClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PublicGatewayClient
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PublicGatewayClient;

        CurlData::$response     = null;
        CurlData::$httpCode     = null;
        CurlData::$errorCode    = null;
        CurlData::$errorMessage = null;
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     */
    public function testValidateRequest()
    {
        $this->object->validateRequest(new Request);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Request end point is empty and request end point group is empty. Set one of them.
     */
    public function testValidateRequestWithoutEndpointAndEndpointGroup()
    {
        $request = new Request(array
        (
            'client_orderid'    => 2121
        ));

        $request->setApiMethod('sale');
        $request->setGatewayUrl('http://example.com');

        $this->object->validateRequest($request);
    }

    /**
     * @expectedException \PaynetEasy\PaynetEasyApi\Exception\ValidationException
     * @expectedExceptionMessage Request end point was set and request end point group was set. Set only one of them.
     */
    public function testValidateRequestWithEndpointAndEndpointGroup()
    {
        $request = new Request(array
        (
            'client_orderid'    => 2121
        ));

        $request->setApiMethod('sale');
        $request->setGatewayUrl('http://example.com');
        $request->setEndPoint(121);
        $request->setEndPointGroup(121);

        $this->object->validateRequest($request);
    }

    public function testSuccessMakeRequest()
    {
        CurlData::$httpCode = 200;
        CurlData::$response = 'type=validation-error &serial-number=00000000-0000-0000-0000-000002231f99 &merchant-order-id=2121 &error-message=Parameter+credit_card_number+is+required &error-code=1';

        $response = $this->object->makeRequest($this->getRequest());

        $this->assertEquals('validation-error', $response->getType());
    }

    /**
     * @expectedException PaynetEasy\PaynetEasyApi\Exception\RequestException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage error message
     */
    public function testMakeRequestCurlError()
    {
        CurlData::$errorCode    = 1;
        CurlData::$errorMessage = 'error message';

        $this->object->makeRequest($this->getRequest());
    }

    /**
     * @expectedException PaynetEasy\PaynetEasyApi\Exception\RequestException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Error occurred. HTTP code: '500'
     */
    public function testMakeRequestHttpError()
    {
        CurlData::$httpCode     = 500;
        CurlData::$errorMessage = 'error message';

        $this->object->makeRequest($this->getRequest());
    }

    /**
     * @expectedException PaynetEasy\PaynetEasyApi\Exception\ResponseException
     * @expectedExceptionMessage PaynetEasy response is empty
     */
    public function testMakeRequestEmptyResponse()
    {
        CurlData::$httpCode     = 200;

        $this->object->makeRequest($this->getRequest());
    }

    public function testGetUrlForEndPoint()
    {
        $request = new Request(array
        (
            'client_orderid'    => 2121
        ));

        $request->setApiMethod('sale');
        $request->setEndPoint(121);
        $request->setGatewayUrl('http://example.com');

        $url = $this->object->getUrl($request);

        $this->assertEquals("http://example.com/sale/121", $url);
    }

    public function testGetUrlForEndPointGroup()
    {
        $request = new Request;

        $request->setApiMethod('sale');
        $request->setEndPointGroup(121);
        $request->setGatewayUrl('http://example.com');

        $url = $this->object->getUrl($request);

        $this->assertEquals("http://example.com/sale/group/121", $url);
    }

    public function testGetUrlForSyncMethod()
    {
        $request = new Request(array
        (
            'client_orderid'    => 2121
        ));

        $request->setApiMethod('sync-account-verification');
        $request->setEndPoint(121);
        $request->setGatewayUrl('http://example.com');

        $url = $this->object->getUrl($request);

        $this->assertEquals("http://example.com/sync/account-verification/121", $url);
    }

    public function testIntegrationBetweenQueryConfigAndRequest() {
        $queryConfig = new QueryConfig(array(
            'end_point'         => 123,
            'login'             => '_',
            'redirect_url'      => 'http://example.com',
            'signing_key'       => 'key'
        ));

        $request = new Request(array
        (
            'client_orderid'    => 2121
        ));

        $request->setEndPoint($queryConfig->getEndPoint());
        $request->setEndPointGroup($queryConfig->getEndPointGroup());

        $request->setApiMethod('sale');
        $request->setGatewayUrl('http://example.com');

        $this->object->validateRequest($request);
    }

    protected function getRequest()
    {
        $request = new Request(array
        (
            'client_orderid'    => 2121
        ));

        $request->setApiMethod('sale');
        $request->setEndPoint(121);
        $request->setGatewayUrl('http://example.com');

        return $request;
    }
}

class PublicGatewayClient extends GatewayClient
{
    public $curlOptions;

    public function validateRequest(Request $request)
    {
        parent::validateRequest($request);
    }

    public function getUrl(Request $request) {
        return parent::getUrl($request);
    }
}

class CurlData
{
    static public $response;
    static public $httpCode;
    static public $errorCode    = 1;
    static public $errorMessage = 'error message';
}

function curl_getinfo()
{
    return CurlData::$httpCode;
}

function curl_exec()
{
    return CurlData::$response;
}

function curl_error()
{
    return CurlData::$errorMessage;
}

function curl_errno()
{
    return CurlData::$errorCode;
}

function curl_init()
{
}

function curl_setopt_array()
{
}

function curl_setopt()
{
}

function curl_close()
{
}