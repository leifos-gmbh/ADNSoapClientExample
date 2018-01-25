<?php

/*  Im folgenden müssen die Webadresse, die ID des ILIAS Mandanten
    und ein Benutzer/Passwort für den Zugriff auf das ILIAS System angegeben
    werden. Wichtig ist, daß es sich bei dem Benutzer um einen normalen
    ADN/ILIAS Account handelt. Mit diesem muß sich zumindest einmal über
    den Browser authentifiziert und die Benutzervereinbarung akzeptiert werden.

    Elwis sollte für jede Elwis Session die auf den Online Test zugreifen
    möchte eine ILIAS Session anfordern (über $client->login(...)). Die
    zurückgegebene $sid kann und sollte dann innerhalb der Elwis Session
    für weitere Zugriffe genutzt werden.

    Eine für Nutzer lesbare Dokumentation des Webservices kann unter
    http://<URL der ADN Installation>/webservice/soap/server.php
    eingesehen werden.

    Das WSDL kann unter
    http://<URL der ADN Installation>/webservice/soap/server.php?wsdl
    abgerufen werden.

*/

define('WSDL','http://scott.local/adn/dev/webservice/soap/server.php?wsdl');
define('CLIENT','adn_dev');
define('USER','soap_user');
define('PASS','soap_pw');

try
{
	$client = new SoapClient(WSDL, array('trace'=>1));
	$sid = $client->login(CLIENT,USER,PASS);

	// get all subject areas
	$sa = $client->getSubjectAreas($sid);

	// create a test for subject area "dm"
	$t = $client->createTest($sid, "dm");

	// get question overview of current test
	$q = $client->getQuestionOverview($sid);

	// process questions
	$pc = $client->processQuestion($sid, 0,0,0);
	$pc = $client->processQuestion($sid, $pc->q_id, 1, $pc->next_q_id);
	$pc = $client->processQuestion($sid, $pc->q_id, 1, $pc->next_q_id);
	$pc = $client->processQuestion($sid, $pc->q_id, 1, $pc->next_q_id);

	$finish = $client->finishTest($sid, $pc->q_id, 1);

	$scoresheet = $client->getScoringSheet($sid);

	$info_sheets = $client->getInformationSheets($sid);

	echo "<p>Finished</p>";
}
catch(SoapFault $e)
{
	echo "Last Response: ".htmlentities($client->__getLastResponse());
	echo "Last Response: ".$client->__getLastResponse();
	var_dump("ERROR",$e->getMessage(),$e->getTraceAsString());
}
?>