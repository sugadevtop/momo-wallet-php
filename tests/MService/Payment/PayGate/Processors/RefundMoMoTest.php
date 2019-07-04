<?php

namespace MService\Payment\PayGate\Processors;

use MService\Payment\PayGate\Models\RefundMoMoRequest;
use MService\Payment\PayGate\Models\RefundMoMoResponse;
use MService\Payment\Shared\SharedModels\Environment;
use MService\Payment\Shared\SharedModels\PartnerInfo;
use PHPUnit\Framework\TestCase;

include_once "../../loader.php";

class RefundMoMoTest extends TestCase
{

    public function test__construct()
    {
        $env = new Environment("teehee", new PartnerInfo("mTCKt9W3eU1m39TW", 'MOMOIQA420180417', 'PPuDXq1KowPT1ftR8DvlQTHhC03aul17'),
            'testing');
        $refundMoMo = new RefundMoMo($env);

        $this->assertInstanceOf(Environment::class, $refundMoMo->getEnvironment(), "Wrong Data Type for RefundMoMo Environment");
        $this->assertInstanceOf(PartnerInfo::class, $refundMoMo->getPartnerInfo(), "Wrong Data Type for RefundMoMo PartnerInfo");

        $this->assertEquals("teehee", $refundMoMo->getEnvironment()->getMoMoEndpoint(), "Wrong MoMoEndpoint in RefundMoMo SetUp");
        $this->assertEquals($env->getTarget(), $refundMoMo->getEnvironment()->getTarget(), "Wrong MoMoEndpoint in RefundMoMo SetUp");

    }

    public function testCreateRefundRequest()
    {
        $env = new Environment("https://test-payment.momo.vn", new PartnerInfo("mTCKt9W3eU1m39TW", 'MOMOLRJZ20181206', 'KqBEecvaJf1nULnhPF5htpG3AMtDIOlD'),
            'development');
        $orderId = time() . "";
        $refundMoMo = new RefundMoMo($env);

        $request = $refundMoMo->createRefundMoMoRequest($orderId, $orderId, 10000, $orderId);
        $this->assertInstanceOf(RefundMoMoRequest::class, $request, "Wrong Data Type for createRefundMoMoRequest");

        $arr = $request->jsonSerialize();
        $this->assertArrayHasKey('partnerCode', $arr, "Missing partnerCode Attribute in RefundMoMoRequest");
        $this->assertArrayHasKey('accessKey', $arr, "Missing accessKey Attribute in RefundMoMoRequest");
        $this->assertArrayHasKey('requestId', $arr, "Missing requestId Attribute in RefundMoMoRequest");
        $this->assertArrayHasKey('amount', $arr, "Missing amount Attribute in RefundMoMoRequest");
        $this->assertArrayHasKey('orderId', $arr, "Missing orderId Attribute in RefundMoMoRequest");
        $this->assertArrayHasKey('transId', $arr, "Missing transId Attribute in RefundMoMoRequest");
        $this->assertArrayHasKey('requestType', $arr, "Missing requestType Attribute in RefundMoMoRequest");
        $this->assertArrayHasKey('signature', $arr, "Missing signature Attribute in RefundMoMoRequest");

        $this->assertEquals('refundMoMoWallet', $request->getRequestType(), "Wrong Request Type for RefundMoMoRequest");

    }

    public function testProcessFailure()
    {
        $env = new Environment("https://test-payment.momo.vn", new PartnerInfo("mTCKt9W3eU1m39TW", 'MOMOLRJZ20181206', 'KqBEecvaJf1nULnhPF5htpG3AMtDIOlD'),
            'development');
        $orderId = time() . "";

        $response = RefundMoMo::process($env, $orderId, $orderId, 10000, $orderId);

        $this->assertInstanceOf(RefundMoMoResponse::class, $response, "Wrong Data Type in execute in RefundMoMoProcess");

        $arr = $response->jsonSerialize();
        $this->assertArrayHasKey('partnerCode', $arr, "Missing partnerCode Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('accessKey', $arr, "Missing accessKey Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('requestId', $arr, "Missing requestId Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('requestType', $arr, "Missing requestType Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('amount', $arr, "Missing amount Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('transId', $arr, "Missing transId Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('errorCode', $arr, "Missing errorCode Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('message', $arr, "Missing message Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('localMessage', $arr, "Missing localMessage Attribute in RefundMoMoResponse");
        $this->assertArrayHasKey('signature', $arr, "Missing signature Attribute in RefundMoMoResponse");

        $this->assertNotEquals(0, $response->getErrorCode(), "Wrong Response Body from MoMo Server -- Wrong ErrorCode");
        $this->assertEmpty($response->getSignature(), "Wrong Response Body from MoMo Server -- Wrong Signature");
    }

}