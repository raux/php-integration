<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class DeliverOrdersRequestIntegrationTest extends PHPUnit_Framework_TestCase{

    /**
     * 1. create an Invoice|PaymentPlan order
     * 2. note the client credentials, order number and type, and insert below
     * 3. run the test
     */
    public function test_manual_DeliverOrdersRequest() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for test_manual_DeliverOrdersRequest' // TODO
        );
        
        $countryCode = "SE";
        $sveaOrderIdToDeliver = 346761;
        $orderType = "Invoice"; // TODO -- \ConfigurationProvider::INVOICE_TYPE is "INVOICE", need to be "Invoice"
        
        $deliverOrderBuilder = new Svea\DeliverOrderBuilder( Svea\SveaConfig::getDefaultConfig() );
        $deliverOrderBuilder->setCountryCode( $countryCode );
        $deliverOrderBuilder->setOrderId( $sveaOrderIdToDeliver );
        $deliverOrderBuilder->setInvoiceDistributionType(DistributionType::POST);
        $deliverOrderBuilder->orderType = $orderType;
          
        $request = new Svea\DeliverOrdersRequest( $deliverOrderBuilder );
        $response = $request->doRequest();
        
        //print_r( $response );        
        $this->assertEquals(0, $response->ResultCode );    // raw response
    }
}
