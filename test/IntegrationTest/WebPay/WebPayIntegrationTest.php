<?php
// Integration tests should not need to use the namespace

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class WebPayIntegrationTest extends PHPUnit_Framework_TestCase {

    /// WebPay::createOrder()
    public function test_createOrder_Invoice_SE_Accepted() {
        $order = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() )
            ->addOrderRow( TestUtil::createOrderRow() )
            ->addCustomerDetails( TestUtil::createIndividualCustomer("SE") )
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setCustomerReference("created by TestUtil::createOrder()")
            ->setClientOrderNumber( "clientOrderNumber:".date('c'))
            ->setOrderDate( date('c') )
        ;
        $response = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $response->accepted);
    }
    
    public function test_createOrder_Paymentplan_SE_Accepted() {

        $order = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails( TestUtil::createIndividualCustomer("SE") )
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setCustomerReference("created by TestUtil::createOrder()")
            ->setClientOrderNumber( "clientOrderNumber:".date('c'))
            ->setOrderDate( date('c') )
        ;
        $response = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();

        $this->assertEquals(1, $response->accepted);
    }    
    
    // CreateOrderBuilder card payment method
    // see CardPaymentURLIntegrationTest->test_manual_CardPayment_getPaymentURL()
        
    // CreateOrderBuilder direct bank payment method   //TODO    
       
    /// WebPay::deliverOrder()
    public function test_deliverOrder_Invoice_Accepted() {
        
        // create order, get orderid to deliver
        $createOrderBuilder = TestUtil::createOrder();
        $response = $createOrderBuilder->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);
        
        $orderId = $response->sveaOrderId;
        
        $deliverOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
                ->addOrderRow( TestUtil::createOrderRow() )
                ->setCountryCode("SE")
                ->setOrderId( $orderId )
                ->setInvoiceDistributionType(\DistributionType::POST)
        ;
        
        $response = $deliverOrderBuilder->deliverInvoiceOrder()->doRequest();

        //print_r( $response );
        $this->assertEquals(1, $response->accepted);
                
    }
   
    public function test_deliverOrder_PaymentPlan_Accepted() {
        
        $order = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails( TestUtil::createIndividualCustomer("SE") )
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setCustomerReference("created by TestUtil::createOrder()")
            ->setClientOrderNumber( "clientOrderNumber:".date('c'))
            ->setOrderDate( date('c') )
        ;
        $response = $order->usePaymentPlanPayment( TestUtil::getGetPaymentPlanParamsForTesting() )->doRequest();
        $this->assertEquals(1, $response->accepted);
        
        $orderId = $response->sveaOrderId;

        $deliverOrderBuilder = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() )
            ->addOrderRow( WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->setCountryCode("SE")
            ->setOrderId( $orderId )
        ;        
        
        $response = $deliverOrderBuilder->deliverPaymentPlanOrder()->doRequest();

        //print_r( $response );
        $this->assertEquals(1, $response->accepted);                
    }
}