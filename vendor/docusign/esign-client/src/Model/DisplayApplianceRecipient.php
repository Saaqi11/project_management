<?php
/**
 * DisplayApplianceRecipient
 *
 * PHP version 5
 *
 * @category Class
 * @package  DocuSign\eSign
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * DocuSign REST API
 *
 * The DocuSign REST API provides you with a powerful, convenient, and simple Web services API for interacting with DocuSign.
 *
 * OpenAPI spec version: v2.1
 * Contact: devcenter@docusign.com
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace DocuSign\eSign\Model;

use \ArrayAccess;

/**
 * DisplayApplianceRecipient Class Doc Comment
 *
 * @category    Class
 * @package     DocuSign\eSign
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class DisplayApplianceRecipient implements ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'displayApplianceRecipient';

    /**
      * Array of property to type mappings. Used for (de)serialization
      * @var string[]
      */
    protected static $swaggerTypes = [
        'cfr_part11' => 'bool',
        'company' => 'string',
        'digital_signature_base64' => 'string',
        'email' => 'string',
        'full_name' => 'string',
        'initials_base64' => 'string',
        'in_person_email' => 'string',
        'is_notary' => 'bool',
        'notary_seal_base64' => 'string',
        'phone_number' => 'string',
        'recipient_complete_count' => 'int',
        'recipient_guid_id' => 'string',
        'recipient_id' => 'string',
        'recipient_status' => 'string',
        'recipient_type' => 'string',
        'row_state' => 'string',
        'signature_base64' => 'string',
        'signed' => 'bool',
        'signer_apply_tabs' => 'bool',
        'signer_attachment_base64' => 'string',
        'user_name' => 'string'
    ];

    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    protected static $attributeMap = [
        'cfr_part11' => 'cfrPart11',
        'company' => 'company',
        'digital_signature_base64' => 'digitalSignatureBase64',
        'email' => 'email',
        'full_name' => 'fullName',
        'initials_base64' => 'initialsBase64',
        'in_person_email' => 'inPersonEmail',
        'is_notary' => 'isNotary',
        'notary_seal_base64' => 'notarySealBase64',
        'phone_number' => 'phoneNumber',
        'recipient_complete_count' => 'recipientCompleteCount',
        'recipient_guid_id' => 'recipientGuidId',
        'recipient_id' => 'recipientId',
        'recipient_status' => 'recipientStatus',
        'recipient_type' => 'recipientType',
        'row_state' => 'rowState',
        'signature_base64' => 'signatureBase64',
        'signed' => 'signed',
        'signer_apply_tabs' => 'signerApplyTabs',
        'signer_attachment_base64' => 'signerAttachmentBase64',
        'user_name' => 'userName'
    ];


    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    protected static $setters = [
        'cfr_part11' => 'setCfrPart11',
        'company' => 'setCompany',
        'digital_signature_base64' => 'setDigitalSignatureBase64',
        'email' => 'setEmail',
        'full_name' => 'setFullName',
        'initials_base64' => 'setInitialsBase64',
        'in_person_email' => 'setInPersonEmail',
        'is_notary' => 'setIsNotary',
        'notary_seal_base64' => 'setNotarySealBase64',
        'phone_number' => 'setPhoneNumber',
        'recipient_complete_count' => 'setRecipientCompleteCount',
        'recipient_guid_id' => 'setRecipientGuidId',
        'recipient_id' => 'setRecipientId',
        'recipient_status' => 'setRecipientStatus',
        'recipient_type' => 'setRecipientType',
        'row_state' => 'setRowState',
        'signature_base64' => 'setSignatureBase64',
        'signed' => 'setSigned',
        'signer_apply_tabs' => 'setSignerApplyTabs',
        'signer_attachment_base64' => 'setSignerAttachmentBase64',
        'user_name' => 'setUserName'
    ];


    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    protected static $getters = [
        'cfr_part11' => 'getCfrPart11',
        'company' => 'getCompany',
        'digital_signature_base64' => 'getDigitalSignatureBase64',
        'email' => 'getEmail',
        'full_name' => 'getFullName',
        'initials_base64' => 'getInitialsBase64',
        'in_person_email' => 'getInPersonEmail',
        'is_notary' => 'getIsNotary',
        'notary_seal_base64' => 'getNotarySealBase64',
        'phone_number' => 'getPhoneNumber',
        'recipient_complete_count' => 'getRecipientCompleteCount',
        'recipient_guid_id' => 'getRecipientGuidId',
        'recipient_id' => 'getRecipientId',
        'recipient_status' => 'getRecipientStatus',
        'recipient_type' => 'getRecipientType',
        'row_state' => 'getRowState',
        'signature_base64' => 'getSignatureBase64',
        'signed' => 'getSigned',
        'signer_apply_tabs' => 'getSignerApplyTabs',
        'signer_attachment_base64' => 'getSignerAttachmentBase64',
        'user_name' => 'getUserName'
    ];

    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    public static function setters()
    {
        return self::$setters;
    }

    public static function getters()
    {
        return self::$getters;
    }

    

    

    /**
     * Associative array for storing property values
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     * @param mixed[] $data Associated array of property values initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['cfr_part11'] = isset($data['cfr_part11']) ? $data['cfr_part11'] : null;
        $this->container['company'] = isset($data['company']) ? $data['company'] : null;
        $this->container['digital_signature_base64'] = isset($data['digital_signature_base64']) ? $data['digital_signature_base64'] : null;
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['full_name'] = isset($data['full_name']) ? $data['full_name'] : null;
        $this->container['initials_base64'] = isset($data['initials_base64']) ? $data['initials_base64'] : null;
        $this->container['in_person_email'] = isset($data['in_person_email']) ? $data['in_person_email'] : null;
        $this->container['is_notary'] = isset($data['is_notary']) ? $data['is_notary'] : null;
        $this->container['notary_seal_base64'] = isset($data['notary_seal_base64']) ? $data['notary_seal_base64'] : null;
        $this->container['phone_number'] = isset($data['phone_number']) ? $data['phone_number'] : null;
        $this->container['recipient_complete_count'] = isset($data['recipient_complete_count']) ? $data['recipient_complete_count'] : null;
        $this->container['recipient_guid_id'] = isset($data['recipient_guid_id']) ? $data['recipient_guid_id'] : null;
        $this->container['recipient_id'] = isset($data['recipient_id']) ? $data['recipient_id'] : null;
        $this->container['recipient_status'] = isset($data['recipient_status']) ? $data['recipient_status'] : null;
        $this->container['recipient_type'] = isset($data['recipient_type']) ? $data['recipient_type'] : null;
        $this->container['row_state'] = isset($data['row_state']) ? $data['row_state'] : null;
        $this->container['signature_base64'] = isset($data['signature_base64']) ? $data['signature_base64'] : null;
        $this->container['signed'] = isset($data['signed']) ? $data['signed'] : null;
        $this->container['signer_apply_tabs'] = isset($data['signer_apply_tabs']) ? $data['signer_apply_tabs'] : null;
        $this->container['signer_attachment_base64'] = isset($data['signer_attachment_base64']) ? $data['signer_attachment_base64'] : null;
        $this->container['user_name'] = isset($data['user_name']) ? $data['user_name'] : null;
    }

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];
        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properteis are valid
     */
    public function valid()
    {
        return true;
    }


    /**
     * Gets cfr_part11
     * @return bool
     */
    public function getCfrPart11()
    {
        return $this->container['cfr_part11'];
    }

    /**
     * Sets cfr_part11
     * @param bool $cfr_part11 
     * @return $this
     */
    public function setCfrPart11($cfr_part11)
    {
        $this->container['cfr_part11'] = $cfr_part11;

        return $this;
    }

    /**
     * Gets company
     * @return string
     */
    public function getCompany()
    {
        return $this->container['company'];
    }

    /**
     * Sets company
     * @param string $company 
     * @return $this
     */
    public function setCompany($company)
    {
        $this->container['company'] = $company;

        return $this;
    }

    /**
     * Gets digital_signature_base64
     * @return string
     */
    public function getDigitalSignatureBase64()
    {
        return $this->container['digital_signature_base64'];
    }

    /**
     * Sets digital_signature_base64
     * @param string $digital_signature_base64 
     * @return $this
     */
    public function setDigitalSignatureBase64($digital_signature_base64)
    {
        $this->container['digital_signature_base64'] = $digital_signature_base64;

        return $this;
    }

    /**
     * Gets email
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     * @param string $email 
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets full_name
     * @return string
     */
    public function getFullName()
    {
        return $this->container['full_name'];
    }

    /**
     * Sets full_name
     * @param string $full_name 
     * @return $this
     */
    public function setFullName($full_name)
    {
        $this->container['full_name'] = $full_name;

        return $this;
    }

    /**
     * Gets initials_base64
     * @return string
     */
    public function getInitialsBase64()
    {
        return $this->container['initials_base64'];
    }

    /**
     * Sets initials_base64
     * @param string $initials_base64 
     * @return $this
     */
    public function setInitialsBase64($initials_base64)
    {
        $this->container['initials_base64'] = $initials_base64;

        return $this;
    }

    /**
     * Gets in_person_email
     * @return string
     */
    public function getInPersonEmail()
    {
        return $this->container['in_person_email'];
    }

    /**
     * Sets in_person_email
     * @param string $in_person_email 
     * @return $this
     */
    public function setInPersonEmail($in_person_email)
    {
        $this->container['in_person_email'] = $in_person_email;

        return $this;
    }

    /**
     * Gets is_notary
     * @return bool
     */
    public function getIsNotary()
    {
        return $this->container['is_notary'];
    }

    /**
     * Sets is_notary
     * @param bool $is_notary 
     * @return $this
     */
    public function setIsNotary($is_notary)
    {
        $this->container['is_notary'] = $is_notary;

        return $this;
    }

    /**
     * Gets notary_seal_base64
     * @return string
     */
    public function getNotarySealBase64()
    {
        return $this->container['notary_seal_base64'];
    }

    /**
     * Sets notary_seal_base64
     * @param string $notary_seal_base64 
     * @return $this
     */
    public function setNotarySealBase64($notary_seal_base64)
    {
        $this->container['notary_seal_base64'] = $notary_seal_base64;

        return $this;
    }

    /**
     * Gets phone_number
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->container['phone_number'];
    }

    /**
     * Sets phone_number
     * @param string $phone_number 
     * @return $this
     */
    public function setPhoneNumber($phone_number)
    {
        $this->container['phone_number'] = $phone_number;

        return $this;
    }

    /**
     * Gets recipient_complete_count
     * @return int
     */
    public function getRecipientCompleteCount()
    {
        return $this->container['recipient_complete_count'];
    }

    /**
     * Sets recipient_complete_count
     * @param int $recipient_complete_count 
     * @return $this
     */
    public function setRecipientCompleteCount($recipient_complete_count)
    {
        $this->container['recipient_complete_count'] = $recipient_complete_count;

        return $this;
    }

    /**
     * Gets recipient_guid_id
     * @return string
     */
    public function getRecipientGuidId()
    {
        return $this->container['recipient_guid_id'];
    }

    /**
     * Sets recipient_guid_id
     * @param string $recipient_guid_id 
     * @return $this
     */
    public function setRecipientGuidId($recipient_guid_id)
    {
        $this->container['recipient_guid_id'] = $recipient_guid_id;

        return $this;
    }

    /**
     * Gets recipient_id
     * @return string
     */
    public function getRecipientId()
    {
        return $this->container['recipient_id'];
    }

    /**
     * Sets recipient_id
     * @param string $recipient_id Unique for the recipient. It is used by the tab element to indicate which recipient is to sign the Document.
     * @return $this
     */
    public function setRecipientId($recipient_id)
    {
        $this->container['recipient_id'] = $recipient_id;

        return $this;
    }

    /**
     * Gets recipient_status
     * @return string
     */
    public function getRecipientStatus()
    {
        return $this->container['recipient_status'];
    }

    /**
     * Sets recipient_status
     * @param string $recipient_status 
     * @return $this
     */
    public function setRecipientStatus($recipient_status)
    {
        $this->container['recipient_status'] = $recipient_status;

        return $this;
    }

    /**
     * Gets recipient_type
     * @return string
     */
    public function getRecipientType()
    {
        return $this->container['recipient_type'];
    }

    /**
     * Sets recipient_type
     * @param string $recipient_type 
     * @return $this
     */
    public function setRecipientType($recipient_type)
    {
        $this->container['recipient_type'] = $recipient_type;

        return $this;
    }

    /**
     * Gets row_state
     * @return string
     */
    public function getRowState()
    {
        return $this->container['row_state'];
    }

    /**
     * Sets row_state
     * @param string $row_state 
     * @return $this
     */
    public function setRowState($row_state)
    {
        $this->container['row_state'] = $row_state;

        return $this;
    }

    /**
     * Gets signature_base64
     * @return string
     */
    public function getSignatureBase64()
    {
        return $this->container['signature_base64'];
    }

    /**
     * Sets signature_base64
     * @param string $signature_base64 
     * @return $this
     */
    public function setSignatureBase64($signature_base64)
    {
        $this->container['signature_base64'] = $signature_base64;

        return $this;
    }

    /**
     * Gets signed
     * @return bool
     */
    public function getSigned()
    {
        return $this->container['signed'];
    }

    /**
     * Sets signed
     * @param bool $signed 
     * @return $this
     */
    public function setSigned($signed)
    {
        $this->container['signed'] = $signed;

        return $this;
    }

    /**
     * Gets signer_apply_tabs
     * @return bool
     */
    public function getSignerApplyTabs()
    {
        return $this->container['signer_apply_tabs'];
    }

    /**
     * Sets signer_apply_tabs
     * @param bool $signer_apply_tabs 
     * @return $this
     */
    public function setSignerApplyTabs($signer_apply_tabs)
    {
        $this->container['signer_apply_tabs'] = $signer_apply_tabs;

        return $this;
    }

    /**
     * Gets signer_attachment_base64
     * @return string
     */
    public function getSignerAttachmentBase64()
    {
        return $this->container['signer_attachment_base64'];
    }

    /**
     * Sets signer_attachment_base64
     * @param string $signer_attachment_base64 
     * @return $this
     */
    public function setSignerAttachmentBase64($signer_attachment_base64)
    {
        $this->container['signer_attachment_base64'] = $signer_attachment_base64;

        return $this;
    }

    /**
     * Gets user_name
     * @return string
     */
    public function getUserName()
    {
        return $this->container['user_name'];
    }

    /**
     * Sets user_name
     * @param string $user_name 
     * @return $this
     */
    public function setUserName($user_name)
    {
        $this->container['user_name'] = $user_name;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     * @param  integer $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     * @param  integer $offset Offset
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     * @param  integer $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(\DocuSign\eSign\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        }

        return json_encode(\DocuSign\eSign\ObjectSerializer::sanitizeForSerialization($this));
    }
}


