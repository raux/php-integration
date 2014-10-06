<?php
use Svea\HostedService\LowerTransaction as LowerTransaction;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../../../src/Includes.php';
require_once $root . '/../../../TestUtil.php';

/**
 * LowerTransactionIntegrationTest 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class LowerTransactionIntegrationTest extends \PHPUnit_Framework_TestCase {
 
   // TODO implement and move to WebPayAdminIntegrationTest as acceptance webdriver test
   /**
     * test_lowerTransaction_card_success creates an order using card payment, 
     * pays using card & receives a transaction id, then credits the transaction
     * 
     * used as acceptance criteria/smoke test for credit transaction feature
     */
    function test_lowerTransaction_card_success() { 
      
        // not yet implemented, requires webdriver support

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'not yet implemented, requires webdriver support' // TODO
        );
        
        // also, needs to have SUCCESS status set on transaction

        // set up order (from testUtil?)
        $order = TestUtil::createOrder();
        
        // pay with card, receive transactionId
        $form = $order
            ->UsePaymentMethod( PaymentMethod::KORTCERT )
            ->setReturnUrl("http://myurl.se")
            //->setCancelUrl()
            //->setCardPageLanguage("SE")
            ->getPaymentForm();
        
        $url = "https://test.sveaekonomi.se/webpay/payment";

        // do request modeled on CardPymentIntegrationTest.php
                
        // make sure the transaction has status SUCCESS at Svea
        
        // credit transcation using above the transaction transactionId
        
        // assert response from lowerTransactionAmount equals success
    }
        
    /**
     * test_lower_transaction_transaction_not_found 
     * 
     * used as initial acceptance criteria for credit transaction feature
     */  
    function test_lower_transaction_transaction_not_found() {
             
        $transactionId = 987654;
        $amountToLower = 100;
                
        $request = new LowerTransaction( Svea\SveaConfig::getDefaultConfig() );
        $request->transactionId = $transactionId;
        $request->amountToLower = $amountToLower;
        $request->countryCode = "SE";
        $response = $request->doRequest();

        $this->assertInstanceOf( "Svea\HostedService\LowerTransactionResponse", $response );
        
        // if we receive an error from the service, the integration test passes
        $this->assertEquals( 0, $response->accepted );
        $this->assertEquals( "128 (NO_SUCH_TRANS)", $response->resultcode );    
    }
    
    /**
     * test_manual_lower_transaction_amount 
     * 
     * run this test manually after you've performed a card transaction and have
     * gotten the the transaction details needed
     */  
    function test_manual_lower_transaction_amount() {
        
        // i.e. order of 117 kr => 11700 at Svea, Svea status AUTHORIZED
        // - 100 => success, 11600 at Svea, Svea status AUTHORIZED
        // - 11600 => success, Svea status ANNULLED
        // - 1 => failure, accepted = 0, resultcode = "105 (ILLEGAL_TRANSACTIONSTATUS)", errormessage = "Invalid transaction status."
        // 
        // new order of 130 kr => 13000 at Svea
        // - 13001 => failure, accepted = 0, resultcode = "305 (BAD_AMOUNT), errormessage = "Invalid value for amount."
        // - 10000 => success, success, 3000 at Svea, Svea status AUTHORIZED
        // - 3001 => failure, accepted = 0, resultcode = "305 (BAD_AMOUNT), errormessage = "Invalid value for amount."
        // - 3000 => success, Svea status ANNULLED
        
        
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'skeleton for manual test of lower transaction amount'
        );
        
        // Set the below to match the transaction, then run the test.
        $clientOrderNumber = "test_manual_recurring_payment_step_1 2014-10-06T14:17:36+02:00";
        $transactionId = 587386;
        $amountToLower = 100;   // TODO also check that status if lower by entire amount == ANNULLED
        
        $request = new LowerTransaction( Svea\SveaConfig::getDefaultConfig() );
        $request->transactionId = $transactionId;
        $request->amountToLower = $amountToLower;
        $request->countryCode = "SE";
        $response = $request->doRequest();        
        
        $this->assertInstanceOf( "Svea\HostedService\LowerTransactionResponse", $response );
        
        print_r($response);                
        $this->assertEquals( 1, $response->accepted );        
        $this->assertEquals( $clientOrderNumber, $response->clientOrderNumber );  
 
    }
    
    function test_manual_alsoDoConfim_set_to_true_does_lowerTransaction_followed_by_confirmTransaction() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_query_card_queryTransaction_returntype'
        );

        // 1. go to https://test.sveaekonomi.se/webpay-admin/admin/start.xhtml 
        // 2. go to verktyg -> betalning
        // 3. enter our test merchantid: 1130
        // 4. use the following xml, making sure to update to a unique customerrefno:
        // <paymentmethod>KORTCERT</paymentmethod><currency>SEK</currency><amount>25500</amount><vat>600</vat><customerrefno>test_manual_query_card_2</customerrefno><returnurl>https://test.sveaekonomi.se/webpay/admin/merchantresponsetest.xhtml</returnurl><orderrows><row><name>Orderrow1</name><amount>500</amount><vat>100</vat><description>Orderrow description</description><quantity>1</quantity><sku>123</sku><unit>st</unit></row><row><name>Orderrow2</name><amount>12500</amount><vat>2500</vat><description>Orderrow2 description</description><quantity>2</quantity><sku>124</sku><unit>m2</unit></row></orderrows>
        // 5. the result should be:
        // <response><transaction id="580964"><paymentmethod>KORTCERT</paymentmethod><merchantid>1130</merchantid><customerrefno>test_manual_query_card_3</customerrefno><amount>25500</amount><currency>SEK</currency><cardtype>VISA</cardtype><maskedcardno>444433xxxxxx1100</maskedcardno><expirymonth>02</expirymonth><expiryyear>15</expiryyear><authcode>898924</authcode></transaction><statuscode>0</statuscode></response>

        // 6. enter the received transaction id below and run the test
        
        // Set the below to match the transaction, then run the test.
        $transactionId = 586184;
        
        $lowerTransactionRequest = new LowerTransaction(Svea\SveaConfig::getDefaultConfig());
        $lowerTransactionRequest->countryCode = "SE";
        $lowerTransactionRequest->transactionId = $transactionId;
        $lowerTransactionRequest->amountToLower = "1";
        $lowerTransactionRequest->alsoDoConfirm = true;
        
        $response = $lowerTransactionRequest->doRequest();
        
        print_r( $response);
        
        $this->assertEquals( 1, $response->accepted );
        $this->assertInstanceOf( "Svea\HostedService\ConfirmTransactionResponse", $response );       
    }
}
?>
