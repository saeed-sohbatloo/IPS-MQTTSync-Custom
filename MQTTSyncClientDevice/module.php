<?php

declare(strict_types=1);

class MQTTSyncClientDevice extends IPSModule
{
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->ConnectParent('{F7A0DD2E-7684-95C0-64C2-D2A9DC47577B}');
        $this->RegisterPropertyString('MQTTTopic', '');
        $this->RegisterPropertyString('GroupTopic', '');
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->ConnectParent('{F7A0DD2E-7684-95C0-64C2-D2A9DC47577B}');

        $GroupTopic = $this->ReadPropertyString('GroupTopic');
        $MQTTTopic = $this->ReadPropertyString('MQTTTopic');
        $this->SetReceiveDataFilter('.*mqttsync/' . $GroupTopic . '/' . $MQTTTopic . '".*');

        $Payload = [];
        $Payload['config'] = 'variables';
        $Topic = 'mqttsync/' . $this->ReadPropertyString('GroupTopic') . '/' . $this->ReadPropertyString('MQTTTopic') . '/get';
        if ($this->HasActiveParent()) {
            $this->sendMQTTCommand($Topic, $Payload);
        }
    }

    public function ReceiveData($JSONString)
    {
        $this->SendDebug('ReceiveData JSON', $JSONString, 0);
        $Data = json_decode($JSONString);

        //Für MQTT Fix in IPS Version 6.3
        if (IPS_GetKernelDate() > 1670886000) {
            $Data->Payload = utf8_decode($Data->Payload);
        }

        if (property_exists($Data, 'Topic')) {
            // Decode the payload
            $payload = json_decode($Data->Payload, true);
            
            // Extract data from the payload
            $ObjectID = $payload['object_id'];
            $Value = $payload['value'];
            
            $Object = IPS_GetObject($ObjectID);
            if ($Object === false) {
                $this->SendDebug(__FUNCTION__, 'ObjectID ' . $ObjectID . ' not found!', 0);
                return;
            }

            if (IPS_VariableExists($ObjectID)) {
                $this->SendDebug('Value for ' . $ObjectID . ':', $Value, 0);
                SetValue($ObjectID, $Value);
            } else {
                $this->SendDebug(__FUNCTION__, 'Variable with ObjectID ' . $ObjectID . ' not found!', 0);
            }
        }
    }

    public function RequestAction($Ident, $Value)
    {
        $Payload = [];
        $Payload['ObjectIdent'] = $Ident;
        $Payload['Value'] = $Value;
        $Topic = 'mqttsync/' . $this->ReadPropertyString('GroupTopic') . '/' . $this->ReadPropertyString('MQTTTopic') . '/set';
        $this->sendMQTTCommand($Topic, $Payload);
    }

    protected function sendMQTTCommand($topic, $payload, $retain = false)
    {
        $Data['DataID'] = '{043EA491-0325-4ADD-8FC2-A30C8EEB4D3F}';
        $Data['PacketType'] = 3;
        $Data['QualityOfService'] = 0;
        $Data['Retain'] = $retain;
        $Data['Topic'] = $topic;
        $Data['Payload'] = json_encode($payload);
        $DataJSON = json_encode($Data, JSON_UNESCAPED_SLASHES);
        $this->SendDebug(__FUNCTION__ . 'MQTT Publish', $DataJSON, 0);
        $resultServer = $this->SendDataToParent($DataJSON);
    }
}
