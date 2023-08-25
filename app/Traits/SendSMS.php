<?php

namespace App\Traits;

trait SendSMS
{

    public function send_sms_process_message($dist_address, $content)
    {
        $stratus_url = "http://sxmp.gw1.vanso.com/api/sxmp/1.0"; // stratus sxmp server url
        $username = "NG.105.0220"; // your account's username. Check the email for details. This field must be in double quotes
        $password = "Axt0KWfC";  //  your account's password. Check the email for details. This field must be in double quotes


        $source_type = "alphanumeric"; //$_GET['alphanumeric'];
        $source_address = "QuickChop"; // Please replace VANSO with your preffered Source Address not MOT MORE THAN 11 CHARACTERS
        $dest_address = "{$dist_address}"; // destination address is the MSISDN in the international format
        $text = $this->strToHex("{$content}"); // Text Message to be sent
        $encoding = "ISO-8859-1";
        $dlr = "false";

        // post SubmitRequest xml to server and wait for response
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $stratus_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:text/xml"));

        // create xml and set as content
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->createSubmitRequestXML($username, $password, $source_type, $source_address, $dest_address, $text, $encoding, $dlr));

        $result = curl_exec($ch);
        curl_close($ch);

        // handle SubmitResponse xml
        if ($result == null) {
            echo "ERROR : No Response available";
        } else {
            // create xml from result
            $response = new \SimpleXMLElement($result); // select root element
            if (isset($response->submitResponse[0])) {
                $root = $response->submitResponse[0];
            } else {
                $root = $response;
            }
            if ($root->error[0]['code'] == 0) {
                // successful submit if errce code is 0
                $ticketId = $root->ticketId[0];
                /* * IMPORTANT NOTE : The TicketID value should be stored by your * application in your own database * since this value is

        important for support, troubleshooting, * and is used by

        callback/postback (DLR) operations. */
                //echo "Received TicketID : " . $ticketId;
            } else {
                // error if code is not 0 an error occured
                $message = $root->error[0]['message']; /*c * IMPORTANT NOTE : An an error occured and should be handled        here. */
                //echo "Error occured : " . $message;
            }
        }
    }

    public function strToHex($string)
    {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    public function createSubmitRequestXML($username, $password, $source_type, $source_address, $dest_address, $text, $encoding, $dlr)
    {
        $xmldoc = new \DOMDocument('1.0');
        $xmldoc->formatOutput = true;
        $root = $xmldoc->createElement('operation');
        $root = $xmldoc->appendChild($root);
        $root->setAttribute('type', 'submit');
        $account = $xmldoc->createElement('account');
        $account = $root->appendChild($account);
        $account->setAttribute('username', $username);
        $account->setAttribute('password', $password);
        $submitRequest = $xmldoc->createElement('submitRequest');
        $submitRequest = $root->appendChild($submitRequest);
        $deliveryReport = $xmldoc->createElement('deliveryReport', $dlr);
        $deliveryReport = $submitRequest->appendChild($deliveryReport);
        $sourceAddress = $xmldoc->createElement('sourceAddress', $source_address);
        $sourceAddress = $submitRequest->appendChild($sourceAddress);
        $sourceAddress->setAttribute('type', $source_type);
        $destinationAddress = $xmldoc->createElement('destinationAddress', $dest_address);
        $destinationAddress = $submitRequest->appendChild($destinationAddress); // destination address type international is mandatory
        $destinationAddress->setAttribute('type', 'international');
        $msg = $xmldoc->createElement('text', $text);
        $msg = $submitRequest->appendChild($msg);
        $msg->setAttribute('encoding', $encoding);
        return
            $xmldoc->saveXML();
    }
}
