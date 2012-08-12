<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PowerDNS API 2.3 library. This module helps to manage domains and records hosted by PowerDNS.
 * To use this module you need to obtain an API key.
 * For more information, please visit: http://www.powerdns.net
 * PowerDNS API 2.3 documentation: https://www.powerdns.net/inc/pdf/PowerDNS%20Express%20API%202.3.pdf
 *
 * @package    Kohana-PowerDNS
 * @author     Istvan Intzoglu
 * @copyright  (c) Istvan Intzoglu, August 12, 2012
 * @license    http://kohanaframework.org/license
 */

class Kohana_PowerDNS
{
	/**
	 * API key
	 *
	 * @var string
	 */
	private $API_key;
	
	/**
	 * API base URL
	 *
	 * @var string
	 */
	protected $API_URL;
	
	/**
	 * API client
	 *
	 */
	protected $client;

	/**
	 * Constructor
	 *
	 * @param string $in_API_key API key
	 * @param string $in_API_URL API url
	 */
	public function __construct($in_API_key = null, $in_API_URL = null) {
		
		// Load PowerDNS config
		$config = Kohana::$config->load('powerdns');

		// RegEXP pattern for API key
		$pattern_API_key = '/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/';

		// RegEXP pattern for API URL		
		$pattern_API_URL = '/^(https?:\/\/+[\w\-]+\.[\w\-]+)/i';
		
		// Check if API key argument is present, if not, try to get it from the config.
		if ($in_API_key) {
		
			$this->set_API_key($in_API_key);
		
		} elseif (preg_match($pattern_API_key, $config->get('api_key'))) {
		
			$this->set_API_key($config->get('api_key'));
			
		} else {
			
			throw new Kohana_Exception('A valid API key must be set in your PowerDNS config or use the overloading method to pass a valid key.');
			
		}
		
		// Check if API URL argument is present, if not, try to get it from the config.
		if ($in_API_URL) {
			
			$this->set_API_URL($in_API_URL);
			
		} elseif (preg_match($pattern_API_URL, $config->get('api_url'))) {
		
			$this->set_API_URL($config->get('api_url'));
			
		} else {
		
			throw new Kohana_Exception('A valid API URL must be set in your PowerDNS config or use the overloading method to pass a valid URL.');
			
		}
		
		// Call the request method (SOAP client)
		$this->request();
	}

	/**
	 * Factory method
	 *
	 * @param string $in_API_key API key
	 * @param string $in_API_URL API url
	 * @return PowerDNS
	 */
	public static function factory($in_API_key = null, $in_API_URL = null) {
		
		return new Kohana_PowerDNS($in_API_key, $in_API_URL);
		
	}

	/**
	 * Request method - SOAP Client
	 *
	 */
	public function request() {
	
		$URL = $this->get_API_URL().$this->get_API_key();
		
		$this->client = new SoapClient($URL,array("trace" => 1, "exceptions" => 0, "features" => SOAP_USE_XSI_ARRAY_TYPE + SOAP_SINGLE_ELEMENT_ARRAYS));
		
		$this->client->__setLocation($URL);
	
	}

	/**
	 * Set the API key
	 *
	 * @param string $in_api_key API key
	 */
	private function set_API_key($in_API_key) {
		
		$this->API_key = $in_API_key;
		
	}

	/**
	 * Get the API key
	 *
	 * @return string API key
	 */
	private function get_API_key() {
		
		return $this->API_key;
		
	}

	/**
	 * Set the base URL
	 *
	 * @param string $in_url_key URL base
	 */
	private function set_API_URL($in_API_URL) {
	
		$this->API_URL = $in_API_URL;
	
	}

	/**
	 * Get the base URL
	 *
	 * @return string URL base
	 */
	private function get_API_URL() {
	
		return $this->API_URL;
	
	}

	/**
	 * Add Native Domain
	 *
	 * This command adds a "native" zone to your PowerDNS Express control panel. 
	 * The zone will have the default nameservers already added
	 *
	 * @param domainName varchar(255) The zone name to be added
	 * @return array Response
	 */
	public function addNativeDomain($domainName) {
	
		$args = array(
		
			"domainName" => $domainName,
			
		);
	
		try {
		
			return (array)$response = $this->client->addNativeDomain($args)->addNativeDomainResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
	
	}

	/**
	 * Add Record to Zone
	 *
	 * This command inserts a record into the specified zone
	 *
	 * @param zoneId integer Id of the zone
	 * @param Name varchar(255) Name of the record (e.g. www.example.com)
	 * @param Type varchar(5) Type of record: Allowed types are: "URL", "NS", "A", "AAAA", "CNAME", "PTR", "MX" and "TXT"
	 * @param Content varchar(255) Content of the record (e.g. for an A record: 127.0.0.1)
	 * @param TimeToLive integer Time to Live in seconds. Everything above 60 is valid
	 * @param Priority integer Used for MX records. The priority of the mail exchanger
	 * @return array Response
	 */
	public function addRecordToZone($zoneId, $name, $type, $content, $TimeToLive = 3600, $Priority = 0) {

		$args = array(
		
			"zoneId" => $zoneId,
			"Name" => $name,
			"Type" => $type,
			"Content" => $content,
			"TimeToLive" => $TimeToLive,
			"Priority" => $Priority
			
		);
	
		try {
		
			return (array)$response = $this->client->addRecordToZone($args)->addRecordToZoneResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
	
	}
	
	/**
	 * Delete All Records of a Domain
	 *
	 * This command deletes all records which are not read-only for a specified zone. 
	 * This means the nameserver records will remain.
	 *
	 * @param zoneId integer Id of the zone to delete from
	 * @return array Response
	 */
	public function deleteAllRecordsForDomain($zoneId) {
		
		$args = array(
		
			"zoneId" => $zoneId,
			
		);
		
		try {
		
			return (array)$response = $this->client->deleteAllRecordsForDomain($args)->deleteAllRecordsForDomainResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
	
	}

	/**
	 * Delete Record by Id
	 *
	 * This command deletes a specific record
	 *
	 * @param recordId integer Id of the record
	 * @return array Response
	 */
	public function deleteRecordById($recordId) {
		
		$args = array(
		
			"recordId" => $recordId,
			
		);
		
		try {
		
			return (array)$response = $this->client->deleteRecordById($args)->deleteRecordByIdResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
	
	}

	/**
	 * Delete Zone by Id
	 *
	 * This command deletes a zone from your control panel
	 *
	 * @param zoneId integer Id of the zone to be deleted
	 * @return array Response
	 */
	public function deleteZoneById($zoneId) {
		
		$args = array(
		
			"zoneId" => $zoneId,
			
		);
		
		try {
		
			return (array)$response = $this->client->deleteZoneById($args)->deleteZoneByIdResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
	
	}

	/**
	 * Delete Zone by Name
	 *
	 * This command deletes a zone from your control panel
	 *
	 * @param zoneName varchar(255) Name of the zone to be deleted
	 * @return array Response
	 */
	public function deleteZoneByName($zoneName) {
		
		$args = array(
		
			"zoneName" => $zoneName,
			
		);
		
		try {
		
			return (array)$response = $this->client->deleteZoneByName($args)->deleteZoneByNameResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
	
	}

	/**
	 * Update Record
	 *
	 * This command updates a record in a zone.
	 *
	 * @param recordId integer Id of the record to be edited
	 * @param Name varchar(255) Name of the record (e.g. www.example.com)
	 * @param Type varchar(5) Type of record: Allowed types are: "URL", "NS", "A", "AAAA", "CNAME", "PTR", "MX" and "TXT"
	 * @param Content varchar(255) Content of the record (e.g. for an A record: 127.0.0.1)
	 * @param TimeToLive integer Time to Live in seconds. Everything above 60 is valid
	 * @param Priority integer Used for MX records. The priority of the mail exchanger
	 * @return array Response
	 */
	public function updateRecord($recordId, $name, $type, $content, $TimeToLive = 3600, $Priority = 0) {

		$args = array(
		
			"recordId" => $recordId,
			"Name" => $name,
			"Type" => $type,
			"Content" => $content,
			"TimeToLive" => $TimeToLive,
			"Priority" => $Priority
			
		);
	
		try {
		
			return (array)$response = $this->client->updateRecord($args);

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
	
	}
	
	/**
	 * List Records
	 *
	 * This command list all records from a zone in your control panel
	 *
	 * @param zoneId integer Id of the zone
	 * @return array Response
	 */
	public function listRecords($zoneId) {
		
		$args = array(
		
			"zoneId" => $zoneId,
			
		);
		
		try {
		
			return (array)$response = $this->client->listRecords($args)->listRecordsResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
		
	}

	/**
	 * List Records by Type
	 *
	 * This command list all records from a zone in your control panel
	 *
	 * @param zoneId integer Id of the zone
	 * @param Type varchar(5) Type of record: Allowed types are: "URL", "NS", "A", "AAAA", "CNAME", "PTR", "MX" and "TXT"
	 * @return array Response
	 */
	public function listRecordsByType($zoneId, $type) {
		
		$args = array(
		
			"zoneId" => $zoneId,
			"type" => $type,
			
		);
		
		try {
		
			return (array)$response = $this->client->listRecordsByType($args)->listRecordsByTypeResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
		
	}

	/**
	 * List Zones
	 *
	 * This command will retrieve all zones currently in your control panel.
	 *
	 * @return array Response
	 */
	public function listZones() {
		
		try {
		
			return (array)$response = $this->client->listZones()->listZonesResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
		
	}

	/**
	 * Renew Zone
	 *
	 * This command will renew and re-activate a zone which has been disabled. 
	 * A zone will be disabled when it cannot be renewed. 
	 * This can happen when there is not enough balance in your account.
	 *
	 * NOTE: Renewing your zone will add 1 year to the renewal date. 
	 * Your account will be charged with the amount required for renewing. 
	 * Already active zones can be renewed as well.
	 *
	 * @param zoneId integer Id of the zone
	 * @return array Response
	 */
	public function renewZone($zoneId) {
		
		$args = array(
		
			"zoneId" => $zoneId,
			
		);
		
		try {
		
			return (array)$response = $this->client->renewZone($args)->renewZoneResult;

		} catch (SoapFault $fault) {

			throw new Kohana_Exception($fault->__toString());

		}
		
	}

}