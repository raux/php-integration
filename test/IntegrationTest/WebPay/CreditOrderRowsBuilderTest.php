<?php

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../src/Includes.php';
require_once $root . '/../../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class CreditOrderRowsBuilderIntegrationTest extends PHPUnit_Framework_TestCase {
    
    protected $invoiceIdToTest;
    protected $country;

    protected function setUp()
    {
        $this->country = "SE";
        $this->invoiceIdToTest = 583004;   // set this to the approved invoice set up by test_manual_setup_CreditOrderRows_testdata()
    }       

    // CreditCardOrderRows    
    
    function test_manual_setup_CreditInvoiceOrderRows_testdata() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_setup_CreditOrderRows_testdata -- run this first to setup order for CreditOrderRows tests to work with. 
            Run once, then make sure to approve the invoice in the admin interface. Then uncomment and run CreditOrderRows tests.'
        );    
                
        // create order
        $order = TestUtil::createOrderWithoutOrderRows( TestUtil::createIndividualCustomer($this->country) );
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );      
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(12)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );         
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("3")
            ->setQuantity( 1 )
            ->setAmountExVat( 1.00 )
            ->setVatPercent(25)
            ->setDescription("C Specification")
            ->setName('C Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        ); 
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("4")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(0)
            ->setDescription("D Specification")
            ->setName('D Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );  
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("5")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(0)
            ->setDescription("E Specification")
            ->setName('E Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );  
        $orderResponse = $order->useInvoicePayment()->doRequest();         
        $this->assertEquals(1, $orderResponse->accepted);

        // deliver order
        $deliver = WebPay::deliverOrder( Svea\SveaConfig::getDefaultConfig() );
        $deliver->setCountryCode($this->country)->setOrderId($orderResponse->sveaOrderId)->setInvoiceDistributionType(DistributionType::POST);
        $deliverResponse = $deliver->deliverInvoiceOrder()->doRequest();        
        $this->assertEquals(1, $deliverResponse->accepted); 
        
        print_r("\ntest_manual_setup_CreditOrderRows_testdata finished, now approve the following invoice: ". $deliverResponse->invoiceId . "\n");
        
    }

    function test_CreditOrderRows_creditInvoiceOrderRows_single_setRowToCredit_success() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );     
              
        $creditOrderRowsResponse = new Svea\CreditOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsResponse
                ->setInvoiceId( $this->invoiceIdToTest )
                ->setInvoiceDistributionType( DistributionType::POST )
                ->setCountryCode($this->country)
                ->setRowToCredit( 1 ) 
                ->creditInvoiceOrderRows()
                    ->doRequest();
                
        print_r("\ntest_CreditOrderRows_creditInvoiceOrderRows_single_row_success:\n");
        print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-125.00, $creditOrderRowsResponse->amount);
    }
    
    function test_CreditOrderRows_creditInvoiceOrderRows_multiple_setRowsToCredit_success() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );  
        
        $creditOrderRowsResponse = new Svea\CreditOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsResponse
                ->setInvoiceId( $this->invoiceIdToTest )
                ->setInvoiceDistributionType( DistributionType::POST )
                ->setCountryCode($this->country)
                ->setRowsToCredit( array(2,3) ) 
                ->creditInvoiceOrderRows()
                    ->doRequest();
                
        print_r("test_CreditOrderRows_creditInvoiceOrderRows_multiple_setRowToCredit_success:\n");
        print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-113.25, $creditOrderRowsResponse->amount);
    }
 
    function test_CreditOrderRows_creditInvoiceOrderRows_single_addCreditOrderRow_success() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );     

        $creditOrderRowsResponse = new Svea\CreditOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsResponse
                ->setInvoiceId( $this->invoiceIdToTest )
                ->setInvoiceDistributionType( DistributionType::POST )
                ->setCountryCode($this->country)
                ->addCreditOrderRow( WebPayItem::orderRow()
                    ->setArticleNumber("101")
                    ->setQuantity( 1 )
                    ->setAmountExVat( 10.00 )
                    ->setVatPercent(25)
                    ->setDescription("101 Specification")
                    ->setName('101 Name')
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                ) 
                ->creditInvoiceOrderRows()
                    ->doRequest();
                
        print_r("test_CreditOrderRows_creditInvoiceOrderRows_single_addCreditOrderRow_success:\n");
        print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-12.50, $creditOrderRowsResponse->amount);
    }    
    
    function test_CreditOrderRows_creditInvoiceOrderRows_multiple_addCreditOrderRow_success() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );      
    } // todo    
    
    function test_CreditOrderRows_creditInvoiceOrderRows_addCreditOrderRow_and_setRowToCredit_success() {
        //  Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );     

        $creditOrderRowsResponse = new Svea\CreditOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsResponse
                ->setInvoiceId( $this->invoiceIdToTest )
                ->setInvoiceDistributionType( DistributionType::POST )
                ->setCountryCode($this->country)
                ->setRowToCredit( 4 ) 
                ->addCreditOrderRow( WebPayItem::orderRow()
                    ->setArticleNumber("104")
                    ->setQuantity( 1 )
                    ->setAmountExVat( 10.00 )
                    ->setVatPercent(25)
                    ->setDescription("101 Specification")
                    ->setName('101 Name')
                    ->setUnit("st")
                    ->setDiscountPercent(0)
                ) 
                ->creditInvoiceOrderRows()
                    ->doRequest();
                
        print_r("test_CreditOrderRows_creditInvoiceOrderRows_addCreditOrderRow_and_setRowToCredit_success:\n");
        print_r( $creditOrderRowsResponse );
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
        $this->assertEquals(-112.50, $creditOrderRowsResponse->amount);
    }    

    // CreditCardOrderRows

    function test_manual_setup_CreditCardOrderRows_testdata() {
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            '1. test_manual_setup_CreditCardOrderRows_testdata -- run this first to setup order for CreditOrderRows tests to work with. 
            Run once, then make sure to approve the invoice in the admin interface. Then uncomment and run CreditOrderRows tests.'
                

            // 2. verktyg / confirm, use this xml w/correct transactionid => status = CONFIRMED
            //
            //<?xml version="1.0" encoding="UTF-8"? >
            //<confirm>
            //<transactionid>583004</transactionid>
            //<capturedate>2014-06-02</capturedate>
            //</confirm>

            // 3. scehmalagda jobb / dailycapture kortcert task => status = SUCCESS
                
        );          
        
        $orderLanguage = "sv";   
        $returnUrl = "http://127.0.0.1";
        $ipAddress = "127.0.0.1";
        
        // create order
        $order = WebPay::createOrder( Svea\SveaConfig::getDefaultConfig() )
                ->setCountryCode( $this->country )
                ->setCurrency("SEK")
                ->setCustomerReference("CreditCardOrderRows_testdata".date('c'))
                ->setClientOrderNumber( "CreditCardOrderRows_testdata".date('c'))
                ->setOrderDate( date('c') )
        ;
        
        $order->addCustomerDetails(
            WebPayItem::individualCustomer()
                ->setNationalIdNumber("194605092222")
                ->setBirthDate(1946, 05, 09)
                ->setName("Tess T", "Persson")
                ->setStreetAddress("Testgatan", 1)
                ->setCoAddress("c/o Eriksson, Erik")
                ->setLocality("Stan")
                ->setZipCode("99999")
                ->setIpAddress($ipAddress)
        );
        
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(25)
            ->setDescription("A Specification")
            ->setName('A Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );      
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("2")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(12)
            ->setDescription("B Specification")
            ->setName('B Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );         
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("3")
            ->setQuantity( 1 )
            ->setAmountExVat( 1.00 )
            ->setVatPercent(25)
            ->setDescription("C Specification")
            ->setName('C Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        ); 
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("4")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(0)
            ->setDescription("D Specification")
            ->setName('D Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );  
        $order->addOrderRow( WebPayItem::orderRow()
            ->setArticleNumber("5")
            ->setQuantity( 1 )
            ->setAmountExVat( 100.00 )
            ->setVatPercent(0)
            ->setDescription("E Specification")
            ->setName('E Name')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        
        $orderResponse = $order
                ->usePaymentMethod( PaymentMethod::KORTCERT )
                    ->setPayPageLanguage($orderLanguage)
                    ->setReturnUrl($returnUrl)
                    ->getPaymentURL();

        print_r( $orderResponse );
        $this->assertEquals(1, $orderResponse->accepted);
        
        print_r( "test_manual_setup_CreditCardOrderRows_testdata finished, now go to " . $orderResponse->testurl ." and complete payment.\n" );
    }
    
    function test_CreditOrderRows_CreditCardOrderRows_addCreditOrderRow_setRowToCredit_success() {
        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//            'first set up confirmed transaction and enter id in setUp()'
//        );         

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder( Svea\SveaConfig::getDefaultConfig() )
            ->setOrderId( $this->invoiceIdToTest )
            ->setCountryCode($this->country)
        ;
                
        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest(); 
        
        print_r( $queryResponse );
        $this->assertEquals(1, $queryResponse->accepted);
        
        $creditOrderRowsBuilder = new Svea\CreditOrderRowsBuilder( Svea\SveaConfig::getDefaultConfig() );
        $creditOrderRowsRequest = $creditOrderRowsBuilder
            ->setOrderId( $this->invoiceIdToTest )
            ->setCountryCode( $this->country )
            ->setRowToCredit( 1 ) 
            ->setNumberedOrderRows( $queryResponse->numberedOrderRows )
            ->creditCardOrderRows()
        ;
        $creditOrderRowsResponse = $creditOrderRowsRequest->doRequest();

        print_r("\ntest_CreditOrderRows_CreditCardOrderRows_addCreditOrderRow_setRowToCredit_success:\n");
        print_r( $creditOrderRowsResponse );
        
        $this->assertEquals(1, $creditOrderRowsResponse->accepted);
    }


    function test_CreditOrderRows_creditDirectBankOrderRows_addCreditOrderRow_setRowToCredit_success() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );  
    } // todo
    
    function test_CreditOrderRows_creditInvoiceOrderRows_addCreditOrderRow_setRowToCredit_exceeds_original_order_fails() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );  
    } // todo

    function test_CreditOrderRows_creditCardOrderRows_addCreditOrderRow_setRowToCredit_exceeds_original_order_fails() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'first set up approved invoice and enter id in setUp()'
        );  
    } // todo
}


?>