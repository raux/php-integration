<?php
namespace Svea;

$root = realpath(dirname(__FILE__));
require_once $root . '/../../src/Includes.php';
require_once $root . '/../TestUtil.php';

/**
 * @author Kristian Grossman-Madsen for Svea Webpay
 */
class WebPayAdminUnitTest extends \PHPUnit_Framework_TestCase {

    public function test_WebPayAdmin_class_exists() {
        $adminObject = new \WebPayAdmin();        
        $this->assertInstanceOf( "WebPayAdmin", $adminObject );
    }

    //HostedRequest/HandleOrder classes
    
    public function test_annulTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $annulTransactionObject = \WebPayAdmin::annulTransaction($config);        
        $this->assertInstanceOf( "Svea\AnnulTransaction", $annulTransactionObject );
    }
    
    public function test_confirmTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $confirmTransactionObject = \WebPayAdmin::confirmTransaction($config);        
        $this->assertInstanceOf( "Svea\ConfirmTransaction", $confirmTransactionObject );
    }

    public function test_creditTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $creditTransactionObject = \WebPayAdmin::creditTransaction($config);        
        $this->assertInstanceOf( "Svea\CreditTransaction", $creditTransactionObject );
    }

    public function test_listPaymentMethods() {
        $config = SveaConfig::getDefaultConfig();
        $listPaymentMethodsObject = \WebPayAdmin::listPaymentMethods($config);        
        $this->assertInstanceOf( "Svea\ListPaymentMethods", $listPaymentMethodsObject );
    }

    public function test_lowerTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $lowerTransactionObject = \WebPayAdmin::lowerTransaction($config);        
        $this->assertInstanceOf( "Svea\LowerTransaction", $lowerTransactionObject );
    }     
    
    public function test_queryTransaction() {
        $config = SveaConfig::getDefaultConfig();
        $queryTransactionObject = \WebPayAdmin::queryTransaction($config);        
        $this->assertInstanceOf( "Svea\QueryTransaction", $queryTransactionObject );
    }      
    
}