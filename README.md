kohana-powerdns
===============

## Kohana - PowerDNS Express API 2.3

PowerDNS API 2.3 library for Kohana 3.2 Framework. This module helps to manage domains and records hosted by PowerDNS Hosting solution. The modules has not been tested with earlier versions, but it may work with 3.0 and 3.1 versions. Will port for request.</br></br>
To use this module **you need to obtain an API key.**</br>
For more information, please visit: [http://www.powerdns.net]</br>
PowerDNS API 2.3 documentation: [PowerDNS Express API 2.3] 

### Configuration

> In order to start use this module, copy the *modules/powerdns/classes/config/powerdns.php* configuration file to under *application/config/* directory and set a valid and working API key.

> NOTE: In both following instantiate cases you can use the overload method to dynamically set the API key or the API URL. See example.

### Instantiate Class

<pre><code>
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
		// Dynamic Way with overloading API key
		$powerdns = new Kohana_PowerDNS("00000000-0000-0000-0000-000000000000");
		$zones = $powerdns->listZones();
	
		// Static Way
		$zones = PowerDNS::factory()->listZones();
	}

}
</code></pre>

### Available methods

  - **Add Native Domain**
<code><pre>$response = PowerDNS::factory()->addNativeDomain("example.com");</code></pre>

  - **Add Record to Zone**
<code><pre>$response = PowerDNS::factory()->addRecordToZone(123456, "example.com", "mail.example.com", "MX", "127.0.0.1", "3600", 10);</code></pre>

  - **List Zones**
<code><pre>$response = PowerDNS::factory()->listZones();</code></pre>

  - **List Records**
<code><pre>$response = PowerDNS::factory()->listRecords(123456);</code></pre>

  - **List Records by type**
<code><pre>$response = PowerDNS::factory()->listRecordsByType(123456, "MX");</code></pre>

  - **Delete All Records for Domain**
<code><pre>$response = PowerDNS::factory()->deleteAllRecordsForDomain(123456);</code></pre>

  - **Delete Record by Id**
<code><pre>$response = PowerDNS::factory()->deleteRecordById(4567890);</code></pre>

  - **Delete Zone by Id**
<code><pre>$response = PowerDNS::factory()->deleteZoneById(123456);</code></pre>

  - **Delete Zone by Name**
<code><pre>$response = PowerDNS::factory()->deleteZoneByName("example.com");</code></pre>

  - **Update Record**
<code><pre>$response = PowerDNS::factory()->updateRecord(123456, "subdomain.example.com", "A", "192.168.0.1", 7200);</code></pre>


  [http://www.powerdns.net]: http://www.powerdns.net
  [PowerDNS Express API 2.3]: https://www.powerdns.net/inc/pdf/PowerDNS%20Express%20API%202.3.pdf
