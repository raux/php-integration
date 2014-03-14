<?php
namespace Svea;

/* 
 * TODO document attributes
 */
class SveaAddress {

    public $Auth;
    public $IsCompany;
    public $CountryCode;
    public $SecurityNumber;

    /**
     * 
     * @param string $auth
     * @param boolean $isCompany
     * @param string $countryCode
     * @param string $securityNumber
     */
    function __construct( $auth, $isCompany, $countryCode, $securityNumber ) {
        $this->Auth = $auth;
        $this->IsCompany = $isCompany;
        $this->CountryCode = $countryCode;
        $this->SecurityNumber = $securityNumber;
    }    
}