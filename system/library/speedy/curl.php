<?php
namespace Speedy;

use Speedy\Csv as Csv;
use Speedy\Helper as Helper;

class Curl
{
    CONST CONNECTTIMEOUT = 15;
    CONST CSID = 1310221370;

    CONST BASE_URL = 'https://api.speedy.bg/v1';

    CONST CALCULATION_URL = self::BASE_URL . '/calculate';

    // Countries
    CONST COUNTRY_URL = self::BASE_URL . '/location/country';
    CONST GET_ALL_COUNTRIES_URL = self::COUNTRY_URL . '/csv';
    CONST GET_COUNTRY_URL = self::COUNTRY_URL . '/'; // URL . {countryId}
    CONST FIND_COUNTRY_URL = self::COUNTRY_URL;

    // States
    CONST STATE_URL = self::BASE_URL . '/location/state';
    CONST GET_ALL_STATES_URL = self::STATE_URL . '/csv/'; // URL . {countryId}
    CONST FIND_STATE_URL = self::STATE_URL;

    // Sites
    CONST SITE_URL = self::BASE_URL . '/location/site';
    CONST FIND_SITE_URL = self::SITE_URL;

    // Offices
    CONST OFFICE_URL = self::BASE_URL . '/location/office';
    CONST GET_OFFICE_URL = self::OFFICE_URL . '/'; // URL . {officeId}
    CONST FIND_OFFICE_URL = self::OFFICE_URL;

    // Complexes
    CONST COMPLEX_URL = self::BASE_URL . '/location/complex';
    CONST FIND_COMPLEX_URL = self::COMPLEX_URL;

    // Streets
    CONST STREET_URL = self::BASE_URL . '/location/street';
    CONST FIND_STREET_URL = self::STREET_URL;

    // Block
    CONST BLOCK_URL = self::BASE_URL . '/location/block';
    CONST FIND_BLOCK_URL = self::BLOCK_URL;

    // Validate
    CONST VALUDATION_URL = self::BASE_URL . '/validation';
    CONST VALIDATE_ADDRESS_URL = self::VALUDATION_URL . '/address';

    // Client
    CONST GET_CONTRACT_CLIENTS_URL = self::BASE_URL . '/client/contract';
    CONST GET_CLIENT_URL = self::BASE_URL . '/client/'; // URL . {clientSystemId}

    // Shipment
    CONST SHIPMENT_URL = self::BASE_URL . '/shipment';
    CONST CREATE_SHIPMENT_URL = self::SHIPMENT_URL;
    CONST SHIPMENT_INFO_URL = self::SHIPMENT_URL . '/info';
    CONST CANCEL_SHIPMENT_URL = self::SHIPMENT_URL . '/cancel';
    CONST FINALIZE_SHIPMENT_URL = self::SHIPMENT_URL . '/finalize';

    // Track
    CONST TRACK_URL = self::BASE_URL . '/track';

    // Pickup Terms
    CONST PICKUP_TERMS_URL = self::BASE_URL . '/pickup/terms';

    // Print
    CONST PRINT_URL = self::BASE_URL . '/print';
    CONST PRINT_VOUCHER_URL = self::PRINT_URL . '/voucher';

    CONST USER_NAME = 'userName';
    CONST PASSWORD = 'password';
    CONST LANGUAGE = 'language';
    CONST CLIENT_SYSTEM_ID = 'clientSystemId';

    CONST SENDER = 'sender';
    CONST CLIENT_ID = 'clientId';
    CONST PRIVATE_PERSON = 'privatePerson';
    CONST DROP_OFF_OFFICE_ID = 'dropoffOfficeId';
    CONST PICKUP_OFFICE_ID = 'pickupOfficeId';
    CONST ADDRESS_LOCATION = 'addressLocation';
    CONST COUNTRY_ID = 'countryId';
    CONST STATE_ID = 'stateId';
    CONST SITE_ID = 'siteId';
    CONST SITE_TYPE = 'siteType';
    CONST SITE_NAME = 'siteName';
    CONST POST_CODE = 'postCode';
    CONST RECIPIENT = 'recipient';
    CONST SERVICE = 'service';
    CONST PICKUP_DATE = 'pickupDate';
    CONST AUTO_ADJUST_PICKUP_DATE = 'autoAdjustPickupDate';
    CONST SERVICE_IDS = 'serviceIds';
    CONST DEFERRED_DAYS = 'deferredDays';
    CONST SATURDAY_DELIVERY = 'saturdayDelivery';
    CONST ADDITIONAL_SERVICES = 'additionalServices';
    CONST FIXED_TIME_DELIVERY = 'fixedTimeDelivery';
    CONST SPECIAL_DELIVERY_ID = 'specialDeliveryId';
    CONST DELIVERY_TO_FLOOR = 'deliveryToFloor';
    CONST COD = 'cod';
    CONST AMOUNT = 'amount';
    CONST CURRENCY_CODE = 'currencyCode';
    CONST PROCESSING_TYPE = 'processingType';
    CONST PAYOUT_TO_THIRD_PARTY = 'payoutToThirdParty';
    CONST INCLUDE_SHIPPING_PRICE = 'includeShippingPrice';
    CONST OBP_DETAILS = 'obpDetails';
    CONST OPTION = 'option';
    CONST RETURN_SHIPMENT_SERVICE_ID = 'returnShipmentServiceId';
    CONST RETURN_SHIPMENT_PAYER = 'returnShipmentPayer';
    CONST DECLARED_VALUE = 'declaredValue';
    CONST FRAGILE = 'fragile';
    CONST IGNORE_IF_NOT_APPLICABLE = 'ignoreIfNotApplicable';
    CONST RETURNS = 'returns';
    CONST ROD = 'rod';
    CONST ENABLED = 'enabled';
    CONST COMMENT = 'comment';
    CONST RETURN_TO_CLIENT_ID = 'returnToClientId';
    CONST RETURN_TO_OFFICE_ID = 'returnToOfficeId';
    CONST THIRD_PARTY_PAYER = 'thirdPartyPayer';
    CONST RETURN_RECEIPT = 'returnReceipt';
    CONST SWAP = 'swap';
    CONST SERVICE_ID = 'serviceId';
    CONST PARCELS_COUNT = 'parcelsCount';
    CONST ROP = 'rop';
    CONST PALLETS = 'pallets';
    CONST RETURN_VOUCHER = 'returnVoucher';
    CONST PAYER = 'payer';
    CONST CONTENT = 'content';
    CONST TOTAL_WEIGHT = 'totalWeight';
    CONST DOCUMENTS = 'documents';
    CONST PALLETIZED = 'palletized';
    CONST PARCELS = 'parcels';
    CONST ID = 'id';
    CONST SEQ_NO = 'seqNo';
    CONST PACKAGE_UNIQUE_NUMBER = 'packageUniqueNumber';
    CONST WEIGHT = 'weight';
    CONST EXTERNAL_CARRIER_PARCEL_NUMBER = 'externalCarrierParcelNumber';
    CONST SIZE = 'size';
    CONST WIDTH = 'width';
    CONST DEPTH = 'depth';
    CONST HEIGHT = 'height';
    CONST PAYMENT = 'payment';
    CONST COURIER_SERVICE_PAYER = 'courierServicePayer';
    CONST DECLARED_VALUE_PAYER = 'declaredValuePayer';
    CONST PACKAGE_PAYER = 'packagePayer';
    CONST THIRD_PARTY_CLIENT_ID = 'thirdPartyClientId';
    CONST DISCOUNT_CARD_ID = 'discountCardId';
    CONST CONTRACT_ID = 'contractId';
    CONST CARD_ID = 'cardId';
    CONST NAME = 'name';
    CONST TYPE = 'type';
    CONST MUNICIPALITY = 'municipality';
    CONST REGION = 'region';
    CONST LIMIT = 'limit';
    CONST ISO_ALPHA_3 = 'isoAlpha3';
    CONST ISO_ALPHA_2 = 'isoAlpha2';
    CONST ADDRESS = 'address';
    CONST STREET_ID = 'streetId';
    CONST STREET_TYPE = 'streetType';
    CONST STREET_NAME = 'streetName';
    CONST STREET_NO = 'streetNo';
    CONST COMPLEX_ID = 'complexId';
    CONST COMPLEX_TYPE = 'complexType';
    CONST COMPLEX_NAME = 'complexName';
    CONST BLOCK_NO = 'blockNo';
    CONST ENTRANCE_NO = 'entranceNo';
    CONST FLOOR_NO = 'floorNo';
    CONST APARTMENT_NO = 'apartmentNo';
    CONST POI_ID = 'poiId';
    CONST ADDRESS_NOTE = 'addressNote';
    CONST ADDRESS_LINE_1 = 'addressLine1';
    CONST ADDRESS_LINE_2 = 'addressLine2';
    CONST X = 'x';
    CONST Y = 'y';
    CONST SHIPMENT_NOTE = 'shipmentNote';
    CONST REF_1 = 'ref1';
    CONST REF_2 = 'ref2';
    CONST PHONE_1 = 'phone1';
    CONST PHONE_2 = 'phone2';
    CONST PHONE_3 = 'phone3';
    CONST NUMBER = 'number';
    CONST EXTENSION = 'extension';
    CONST CLIENT_NAME = 'clientName';
    CONST CONTACT_NAME = 'contactName';
    CONST EMAIL = 'email';
    CONST OBJECT_NAME = 'objectName';
    CONST CONTENTS = 'contents';
    CONST PACKAGE = 'package';
    CONST PENDING_PARCELS = 'pendingParcels';
    CONST STARTING_DATE = 'startingDate';
    CONST SENDER_HAS_PAYMENT = 'senderHasPayment';
    CONST SHIPMENT_ID = 'shipmentId';
    CONST FORMAT = 'format';
    CONST PAPER_SIZE = 'paperSize';
    CONST PRINTER_NAME = 'printerName';
    CONST DPI = 'dpi';
    CONST PARCEL = 'parcel';
    CONST ADDITIONAL_BARCODE = 'additionalBarcode';
    CONST VALUE = 'value';
    CONST LABEL = 'label';
    CONST SHIPMENT_IDS = 'shipmentIds';
    CONST LAST_OPEARTION_ONLY = 'lastOpeartionOnly';

    /**
    * @keys 0|1|2
    */
    CONST DEFERRED_DAYS_VALS = array(0, 1, 2);

    /**
    * @keys CASH|POSTAL_MONEY_TRANSFER
    */
    CONST PROCESSING_TYPE_VALS = array('CASH' => 'CASH', 'POSTAL_MONEY_TRANSFER' => 'POSTAL_MONEY_TRANSFER');

    /**
    * @keys OPEN|TEST
    */
    CONST OBP_VALS = array('OPEN' => 'OPEN', 'TEST' => 'TEST');

    /**
    * @keys SENDER|RECIPIENT|THIRD_PARTY
    */
    CONST RETURN_VOUCHER_PAYER_VALS = array('SENDER' => 'SENDER', 'RECIPIENT' => 'RECIPIENT', 'THIRD_PARTY' => 'THIRD_PARTY');

    /**
    * @keys SENDER|RECIPIENT|THIRD_PARTY
    */
    CONST RETURN_SHIPMENT_PAYER_VALS = array('SENDER' => 'SENDER', 'RECIPIENT' => 'RECIPIENT', 'THIRD_PARTY' => 'THIRD_PARTY');

    /**
    * @keys SENDER|RECIPIENT|THIRD_PARTY
    */
    CONST SHIPMENT_PAYMENT_PAYER_VALS = array('SENDER' => 'SENDER', 'RECIPIENT' => 'RECIPIENT', 'THIRD_PARTY' => 'THIRD_PARTY');

    /**
    * @keys SENDER|RECIPIENT|THIRD_PARTY
    */
    CONST DECLARED_VALUE_PAYER_VALS = array('SENDER' => 'SENDER', 'RECIPIENT' => 'RECIPIENT', 'THIRD_PARTY' => 'THIRD_PARTY');

    /**
    * @keys SENDER|RECIPIENT|THIRD_PARTY
    */
    CONST COURIER_SERVICE_PAYER_VALS = array('SENDER' => 'SENDER', 'RECIPIENT' => 'RECIPIENT', 'THIRD_PARTY' => 'THIRD_PARTY');

    /**
    * @keys OFFICE|APT
    */
    CONST OFFICE_TYPE_VALS = array('OFFICE' => 'OFFICE', 'APT' => 'APT');

    /**
    * @keys PDF|ZPL
    */
    CONST PRINT_FORMAT_VALS = array('PDF' => 'pdf', 'ZPL' => 'zpl');

    /**
    * @keys CODE128|EAN13|EAN8|UPCA|UPCE
    */
    CONST BARCODE_FORMAT_VALS = array('CODE128' => 'CODE128', 'EAN13' => 'EAN13', 'EAN8' => 'EAN8', 'UPCA' => 'UPCA', 'UPCE' => 'UPCE');

    /**
    * @keys A4|A6|A4_4xA6
    */
    CONST PAPER_SIZE_VALS = array('A4' => 'A4', 'A6' => 'A6', 'A4_4xA6' => 'A4_4xA6');

    /**
    * @keys 203|300
    */
    CONST DPI_VALS = array('203' => 'dpi203', '300' => 'dpi300');

    /**
    * @keys 0|1|2
    */
    CONST SITE_ADDRESS_NOMENCLATURE_VALS = array(0 => 'NO', 1 => 'PARTIAL', 2 => 'FULL');

    /**
    * @keys FULL_ADDRESS|PARTIAL_ADDRESS
    */
    CONST COUNTRY_ADDRESS_TYPE_VALS = array('FULL_ADDRESS' => 1, 'PARTIAL_ADDRESS' => 2);

    CONST EN_FIELDS = array(
        'nameEn'         => 'name',
        'municipalityEn' => 'municipality',
        'regionEn'       => 'region',
        'typeEn'         => 'type',
        'addressEn'      => 'address',
        'actualTypeEn'   => 'actualType',
        'actualNameEn'   => 'actualName',
    );

    private $_username;
    private $_password;
    private $_language;

    private $_errors = array();

    public function __construct($username, $password, $language)
    {
        $this->login($username, $password, $language);
    }

    public function login($username, $password, $language)
    {
        $this->_username = $username;
        $this->_password = $password;
        $this->_language = $language;
    }

    public function pickupTerms($data)
    {
        $params = array(
            self::SERVICE_ID           => Helper::get($data, 'service_id'),
            self::STARTING_DATE        => Helper::get($data, 'starting_date'),
            //self::SENDER_HAS_PAYMENT   => Helper::get($data, 'sender_has_payment'),
            self::SENDER               => array(
                self::CLIENT_ID           => Helper::get($data, 'sender_client_id'),
                self::PRIVATE_PERSON      => Helper::get($data, 'sender_private_person'),
                self::DROP_OFF_OFFICE_ID  => Helper::get($data, 'sender_office_id'),
                // self::ADDRESS_LOCATION    => array(
                    //self::COUNTRY_ID => Helper::get($data, 'sender_country_id'),
                    //self::STATE_ID   => Helper::get($data, 'sender_state_id'),
                    // self::SITE_ID    => Helper::get($data, 'sender_site_id'),
                    // self::SITE_TYPE  => Helper::get($data, 'sender_site_type'),
                    // self::SITE_NAME  => Helper::get($data, 'sender_site_name'),
                    // self::POST_CODE  => Helper::get($data, 'sender_post_code'),
                // )
            )
        );

        $params = $this->clean($params);

        $terms = $this->send(self::PICKUP_TERMS_URL, $params);

        $this->addError('PickupTermsResponse', $terms);

        return Helper::get($terms, 'cutoffs');
    }

    public function calculate($data)
    {
        $params = array(
            self::SENDER         => array(
                self::CLIENT_ID          => Helper::get($data, 'sender.client_id'),
                // privatePerson   => '',
                self::DROP_OFF_OFFICE_ID => Helper::get($data, 'sender.office_id'),
                // self::ADDRESS_LOCATION   => $this->_addressLocation($data, 'sender.address_location'),
            ),
            self::RECIPIENT => array(
                // clientId        => '',
                self::PRIVATE_PERSON     => Helper::get($data, 'recipient.private_person'),
                self::PICKUP_OFFICE_ID   => Helper::get($data, 'recipient.office_id'),
                self::ADDRESS_LOCATION   => $this->_addressLocation($data, 'recipient.address_location.'),
            ),
            self::SERVICE => array(
                self::PICKUP_DATE             => Helper::get($data, 'service.pickup_date'),
                self::AUTO_ADJUST_PICKUP_DATE => Helper::get($data, 'service.auto_adjust_pickup_date'),
                self::SERVICE_IDS             => Helper::get($data, 'service.service_ids', array()),
                // self::DEFERRED_DAYS           => '',
                // self::SATURDAY_DELIVERY       => '',
                self::ADDITIONAL_SERVICES     => $this->_shipmentAdditionalServices($data, 'service.additional_services.')
            ),
            self::CONTENT => array(
                self::PARCELS_COUNT => Helper::get($data, 'content.parcels_count'),
                self::TOTAL_WEIGHT  => Helper::get($data, 'content.total_weight'),
                self::DOCUMENTS     => Helper::get($data, 'content.documents'),
                self::PALLETIZED    => Helper::get($data, 'content.palletized'),
                self::PARCELS       => $this->_shipmentParcel($data, 'content.'),
            ),
            self::PAYMENT => $this->_shipmentPayment($data, 'payment.'),
        );

        $params = $this->clean($params);

        $calculations = $this->send(self::CALCULATION_URL, $params);

        $this->addError('CalculateResponse', $calculations);

        return Helper::get($calculations, 'calculations', array());
    }

    public function createShipment($data)
    {
        $params = array(
            self::SENDER        => array(
                self::CLIENT_ID               => Helper::get($data, 'sender.client_id'),
                // self::CLIENT_NAME             => Helper::get($data, 'sender.client_name'),
                self::CONTACT_NAME            => Helper::get($data, 'sender.contact_name'),
                // self::EMAIL                   => Helper::get($data, 'sender.email'),
                // self::PRIVATE_PERSON          => Helper::get($data, 'sender.private_person'),
                self::DROP_OFF_OFFICE_ID      => Helper::get($data, 'sender.office_id'),
                self::PHONE_1                 => $this->_shipmentPhoneNumber($data, 'sender.phone_1.'),
                // self::PHONE_2                 => $this->_shipmentPhoneNumber($data, 'sender.phone_2.'),
                // self::PHONE_3                 => $this->_shipmentPhoneNumber($data, 'sender.phone_3.'),
                // self::ADDRESS                 => $this->_shipmentAddress($data, 'sender.'),
            ),
            self::RECIPIENT     => array(
                // self::CLIENT_ID               => Helper::get($data, 'recipient.client_id'),
                self::CLIENT_NAME             => Helper::get($data, 'recipient.client_name'),
                self::OBJECT_NAME             => Helper::get($data, 'recipient.object_name'),
                self::CONTACT_NAME            => Helper::get($data, 'recipient.contact_name'),
                self::EMAIL                   => Helper::get($data, 'recipient.email'),
                self::PRIVATE_PERSON          => Helper::get($data, 'recipient.private_person'),
                self::PICKUP_OFFICE_ID        => Helper::get($data, 'recipient.office_id'),
                self::PHONE_1                 => $this->_shipmentPhoneNumber($data, 'recipient.phone_1.'),
                // self::PHONE_2                 => $this->_shipmentPhoneNumber($data, 'recipient.phone_2'),
                // self::PHONE_3                 => $this->_shipmentPhoneNumber($data, 'recipient.phone_3'),
                self::ADDRESS                 => $this->_shipmentAddress($data, 'recipient.')
            ),
            self::SERVICE       => array(
                self::PICKUP_DATE             => Helper::get($data, 'service.pickup_date'),
                self::AUTO_ADJUST_PICKUP_DATE => Helper::get($data, 'service.auto_adjust_pickup_date'),
                self::SERVICE_ID              => Helper::get($data, 'service.service_id'),
                self::DEFERRED_DAYS           => Helper::get($data, 'service.deferred_days'),
                self::SATURDAY_DELIVERY       => Helper::get($data, 'service.saturday_delivery'),
                self::ADDITIONAL_SERVICES     => $this->_shipmentAdditionalServices($data, 'service.additional_services.')
            ),
            self::CONTENT       => array(
                self::PARCELS_COUNT           => Helper::get($data, 'content.parcels_count'),
                self::TOTAL_WEIGHT            => Helper::get($data, 'content.total_weight'),
                self::CONTENTS                => Helper::get($data, 'content.contents'),
                self::PACKAGE                 => Helper::get($data, 'content.package'),
                self::DOCUMENTS               => Helper::get($data, 'content.documents'),
                self::PALLETIZED              => Helper::get($data, 'content.palletized'),
                self::PENDING_PARCELS         => Helper::get($data, 'content.pending_parcels'),
                self::PARCELS                 => $this->_shipmentParcel($data, 'content.'),
            ),
            self::PAYMENT       => $this->_shipmentPayment($data, 'payment.'),
            self::SHIPMENT_NOTE => Helper::get($data, 'shipment_note'),
            self::REF_1         => Helper::get($data, 'ref1'),
            self::REF_2         => Helper::get($data, 'ref2'),
        );

        $params = $this->clean($params);

        $shipment = $this->send(self::CREATE_SHIPMENT_URL, $params);

        $this->addError('CreateShipmentResponse', $shipment);

        return $shipment;
    }

    public function shipmentInformation($data)
    {
        $params = array(
            self::SHIPMENT_IDS => Helper::get($data, 'shipment_ids', array())
        );

        $shipments = $this->send(self::SHIPMENT_INFO_URL, $params);

        $this->addError('ShipmentInformationResponse', $shipments);

        return Helper::get($shipments, 'shipments', array());
    }

    public function cancelShipment($data)
    {
        $params = array(
            self::SHIPMENT_ID => Helper::get($data, 'shipment_id'),
            self::COMMENT     => Helper::get($data, 'comment'),
        );

        $responce = $this->send(self::CANCEL_SHIPMENT_URL, $params);

        $this->addError('CancelShipmentResponse', $responce);

        return empty($responce['error']);
    }

    public function sPrint($data)
    {
        $parcels = array();

        foreach (Helper::get($data, 'parcels', array()) as $value) {
            $parcels[] =array(
                self::PARCEL => array(
                    self::ID                             => Helper::get($value, 'parcel.id'),
                    // self::EXTERNAL_CARRIER_PARCEL_NUMBER => Helper::get($value, 'parcel.external_carrier_parcel_number'),
                ),
                // self::ADDITIONAL_BARCODE  => array(
                //     self::VALUE  => Helper::get($value, 'additional_barcode.value'),
                //     self::LABEL  => Helper::get($value, 'additional_barcode.label'),
                //     self::FORMAT => Helper::get($value, 'additional_barcode.format'),
                // )
            );
        }

        $params = array(
            // self::FORMAT       => Helper::get($data, 'format'),
            self::PAPER_SIZE   => Helper::get($data, 'paper_size'),
            // self::PRINTER_NAME => Helper::get($data, 'printer_name'),
            // self::DPI          => Helper::get($data, 'dpi'),
            self::PARCELS     => $parcels,
        );

        $responce = $this->send(self::PRINT_URL, $params);

        $this->addError('PrintResponse', $responce);

        return $responce;
    }

    public function printVoucher($data)
    {
        $params = array(
            self::SHIPMENT_ID     => Helper::get($data, 'shipment_ids', array()),
            // self::PRINTER_NAME => Helper::get($data, 'printer_name'),
        );

        $responce = $this->send(self::PRINT_VOUCHER_URL, $params);

        $this->addError('PrintVoucherResponse', $responce);

        return $responce;
    }

    public function finalizePendingShipment($data)
    {
        $params = array(
            self::SHIPMENT_ID     => Helper::get($data, 'shipment_id'),
        );

        $responce = $this->send(self::FINALIZE_SHIPMENT_URL, $params);

        $this->addError('FinalizePendingShipmentResponse', $responce);

        return $responce;
    }

    public function track($data)
    {
        $parcels = array();

        foreach (Helper::get($data, 'parcels', array()) as $value) {
            $parcels[] = array(
                self::ID                             => Helper::get($value, 'parcel.id'),
                // self::EXTERNAL_CARRIER_PARCEL_NUMBER => Helper::get($value, 'parcel.external_carrier_parcel_number'),
            );
        }

        $params = array(
            self::LAST_OPEARTION_ONLY => Helper::get($data, 'last_opeartion_only'),
            self::PARCELS             => $parcels,
        );

        $parcels = $this->send(self::TRACK_URL, $params);

        $this->addError('TrackResponse', $parcels);

        return Helper::get($parcels, 'parcels', array());
    }

    public function getContractClients()
    {
        $clients = $this->send(self::GET_CONTRACT_CLIENTS_URL);

        $this->addError('GetContractClientsResponse', $clients);

        return Helper::get($clients, 'clients', array());
    }

    public function getClient($clientSystemId)
    {
        $client = $this->send(self::GET_CLIENT_URL . $clientSystemId);

        $this->addError('GetClientResponse', $client);

        return Helper::get($client, 'client', array());
    }

    public function validateAddress($data)
    {
        $params = array(
            self::ADDRESS => $this->_shipmentAddress($data)
        );

        $params = $this->clean($params);

        $responce = $this->send(self::VALIDATE_ADDRESS_URL, $params);

        $this->addError('ValidateAddressRequest', $responce);

        return Helper::get($responce, 'valid');
    }

    public function getAllCountries()
    {
        return Csv::toArray($this->send(self::GET_ALL_COUNTRIES_URL, array(), false));
    }

    public function getCountry($countryId)
    {
        $country = $this->send(self::GET_COUNTRY_URL . $countryId);
        $country = $this->translate($country);

        $this->addError('GetCountryRequest', $country);

        return Helper::get($country, 'country', array());
    }

    public function findCountry($data)
    {
        $params = array(
            self::NAME           => Helper::get($data, 'name'),
            self::ISO_ALPHA_2    => Helper::get($data, 'isoAlpha2'),
            // self::ISO_ALPHA_3    => Helper::get($data, 'isoAlpha3'),
        );

        $countries = $this->send(self::FIND_COUNTRY_URL, $params);
        $countries = $this->translate($countries);

        $this->addError('FindCountryRequest', $countries);

        return Helper::get($countries, 'countries', array());
    }

    public function getAllStates($countryId) // 100 BG
    {
        return Csv::toArray($this->send(self::GET_ALL_STATES_URL . $countryId, array(), false)); // no data
    }

    public function findState($data)
    {
        $params = array(
            self::COUNTRY_ID   => $data['countryId'],
            self::NAME         => Helper::get($data, 'name'),
        );

        $states = $this->send(self::FIND_STATE_URL, $params);
        $states = $this->translate($states);

        $this->addError('FindStateRequest', $states);

        return Helper::get($states, 'states', array());
    }

    public function findSite($data)
    {
        $params = array(
            self::COUNTRY_ID   => $data['countryId'],
            self::NAME         => Helper::get($data, 'name'),
            self::POST_CODE    => Helper::get($data, 'postCode'),
            // self::TYPE         => $data['type'],
            // self::MUNICIPALITY => $data['municipality'],
            // self::REGION       => $data['region'],
        );

        $sites = $this->send(self::FIND_SITE_URL, $params);
        $sites = $this->translate($sites);

        $this->addError('FindSiteResponse', $sites);

        return Helper::get($sites, 'sites', array());
    }

    public function getOffice($officeId)
    {
        $office = $this->send(self::GET_OFFICE_URL . $officeId);

        $this->addError('GetOfficeResponse', $office);

        return Helper::get($office, 'office', array());
    }

    public function findOffice($data)
    {
        $params = array(
            self::COUNTRY_ID  => Helper::get($data, 'countryId'),
            self::SITE_ID     => Helper::get($data, 'siteId'),
            self::NAME        => Helper::get($data, 'name'),
            // self::LIMIT       => $data['limit'],
        );

        $offices = $this->send(self::FIND_OFFICE_URL, $params);

        $this->addError('FindOfficeResponse', $offices);

        return Helper::get($offices, 'offices', array());
    }

    public function findComplex($data)
    {
        $params = array(
            self::SITE_ID     => $data['siteId'],
            self::NAME        => Helper::get($data, 'name'),
            // self::TYPE        => $data['type'],
        );

        $complexes = $this->send(self::FIND_COMPLEX_URL, $params);
        $complexes = $this->translate($complexes);

        $this->addError('FindComplexRequest', $complexes);

        return Helper::get($complexes, 'complexes', array());
    }

    public function findStreet($data)
    {
        $params = array(
            self::SITE_ID     => $data['siteId'],
            self::NAME        => Helper::get($data, 'name'),
            // self::TYPE        => $data['type'],
        );

        $streets = $this->send(self::FIND_STREET_URL, $params);
        $streets = $this->translate($streets);

        $this->addError('FindStreetRequest', $streets);

        return Helper::get($streets, 'streets', array());
    }

    public function findBlock($data)
    {
        $params = array(
            self::SITE_ID     => $data['siteId'],
            self::NAME        => Helper::get($data, 'name'),
            // self::TYPE        => $data['type'],
        );

        $blocks = $this->send(self::FIND_BLOCK_URL, $params);
        $blocks = $this->translate($blocks);

        $this->addError('FindBlockRequest', $blocks);

        return Helper::get($blocks, 'blocks', array());
    }

    private function _addressLocation($data, $prefix = '')
    {
        return array(
            self::COUNTRY_ID => Helper::get($data, $prefix . 'country_id'),
            // self::STATE_ID   => '',
            self::SITE_ID    => Helper::get($data, $prefix . 'site_id'),
            // self::SITE_TYPE  => '',
            // self::SITE_NAME  => '',
            self::POST_CODE  => Helper::get($data, $prefix . 'post_code'),
        );
    }

    private function _shipmentPayment($data, $prefix = '')
    {
        return array(
            self::COURIER_SERVICE_PAYER => Helper::get($data, $prefix . 'courier_service_payer'),
            self::DECLARED_VALUE_PAYER  => Helper::get($data, $prefix . 'declared_value_payer'),
            self::PACKAGE_PAYER         => Helper::get($data, $prefix . 'package_payer'),
            self::THIRD_PARTY_CLIENT_ID => Helper::get($data, $prefix . 'third_party_client_id'),
            self::DISCOUNT_CARD_ID      => array(
                self::CONTRACT_ID => Helper::get($data, $prefix . 'discount_card_id.contract_id'),
                self::CARD_ID     => Helper::get($data, $prefix . 'discount_card_id.card_id'),
            )
        );
    }

    private function _shipmentParcel($data, $prefix = '')
    {
        $parcels = array();

        foreach (Helper::get($data, $prefix . 'parcels', array()) as $parcel) {
            $arr = array(
                self::ID                             => Helper::get($parcel, 'id'),
                self::SEQ_NO                         => Helper::get($parcel, 'no'),
                //self::PACKAGE_UNIQUE_NUMBER          => '',
                self::WEIGHT                         => Helper::get($parcel, 'weight'),
                //self::EXTERNAL_CARRIER_PARCEL_NUMBER => '',
                self::REF_1                          => Helper::get($parcel, 'ref1'),
                self::REF_2                          => Helper::get($parcel, 'ref2'),
            );

            if (Helper::get($parcel, 'size')) {
                $size = Helper::get($parcel, 'size');

                $arr[self::SIZE] = array(
                    self::WIDTH  => Helper::get($size, 'width'),
                    self::DEPTH  => Helper::get($size, 'depth'),
                    self::HEIGHT => Helper::get($size, 'height'),
                );
            }

            $parcels[] = $arr;
        }

        return $parcels;
    }

    private function _shipmentAdditionalServices($data, $prefix = '')
    {
        return array(
            self::FIXED_TIME_DELIVERY => Helper::get($data, $prefix . 'fixed_time_delivery'),
            // self::SPECIAL_DELIVERY_ID => '',
            // self::DELIVERY_TO_FLOOR   => '',
            self::COD                 => array(
                self::AMOUNT                 => Helper::get($data, $prefix . 'cod.amount'),
                // self::CURRENCY_CODE          => '',
                self::PROCESSING_TYPE        => Helper::get($data, $prefix . 'cod.processing_type'),
                // self::PAYOUT_TO_THIRD_PARTY  => '',
                self::INCLUDE_SHIPPING_PRICE => Helper::get($data, $prefix . 'cod.include_shipping_price'),
            ),
            self::OBP_DETAILS            => array(
                self::OPTION                     => Helper::get($data, $prefix . 'obp_details.option'),
                self::RETURN_SHIPMENT_SERVICE_ID => Helper::get($data, $prefix . 'obp_details.return_shipment_service_id'),
                self::RETURN_SHIPMENT_PAYER      => Helper::get($data, $prefix . 'obp_details.return_shipment_payer'),
            ),
            self::DECLARED_VALUE      => array(
                self::AMOUNT                   => Helper::get($data, $prefix . 'declared_value.amount'),
                self::FRAGILE                  => Helper::get($data, $prefix . 'declared_value.fragile'),
                // self::IGNORE_IF_NOT_APPLICABLE => '',
            ),
            self::RETURNS => array(
                self::ROD => array(
                    self::ENABLED             => Helper::get($data, $prefix . 'returns.rod.enabled'),
            //         self::COMMENT             => '',
            //         self::RETURNTO_CLIENT_ID  => '',
            //         self::RETURN_TO_OFFICE_ID => '',
            //         self::THIRD_PARTY_PAYER   => '',
                ),
                self::RETURN_RECEIPT => array(
                     self::ENABLED                => Helper::get($data, $prefix . 'returns.return_receipt.enabled'),
            //         self::RETURN_TO_CLIENT_ID    => '',
            //         self::RETURN_TO_OFFICE_ID    => '',
            //         self::THIRD_PARTY_PAYER      => '',
                ),
            //     self::SWAP => array(
            //         self::SERVICE_ID          => '',
            //         self::PARCELS_COUNT       => '',
            //         self::DECLARED_VALUE      => '',
            //         self::FRAGILE             => '',
            //         self::RETURN_TO_CLIENT_ID => '',
            //         self::RETURN_TO_OFFICE_ID => '',
            //     ),
            //     self::ROP => array(
            //         self::PALLETS => array( // []
            //             self::SERVICE_ID    => 1,
            //             self::PARCELS_COUNT => 1,
            //         )
            //     ),
                self::RETURN_VOUCHER => array(
                    self::SERVICE_ID => Helper::get($data, $prefix . 'returns.return_voucher.service_id'),
                    self::PAYER      => Helper::get($data, $prefix . 'returns.return_voucher.payer'),
                ),
            )
        );
    }

    private function _shipmentPhoneNumber($data, $prefix = '')
    {
        return array(
            self::NUMBER    => Helper::get($data, $prefix . 'number'),
            self::EXTENSION => Helper::get($data, $prefix . 'extension'),
        );
    }

    private function _shipmentAddress($data, $prefix = '')
    {
        return array(
            self::COUNTRY_ID     => Helper::get($data, $prefix . 'country_id'),
            self::STATE_ID       => Helper::get($data, $prefix . 'state_id'),
            self::SITE_ID        => Helper::get($data, $prefix . 'site_id'),
            self::SITE_TYPE      => Helper::get($data, $prefix . 'site_type'),
            self::SITE_NAME      => Helper::get($data, $prefix . 'site_name'),
            self::POST_CODE      => Helper::get($data, $prefix . 'post_code'),
            self::STREET_ID      => Helper::get($data, $prefix . 'street_id'),
            self::STREET_TYPE    => Helper::get($data, $prefix . 'street_type'),
            self::STREET_NAME    => Helper::get($data, $prefix . 'street_name'),
            self::STREET_NO      => Helper::get($data, $prefix . 'street_no'),
            self::COMPLEX_ID     => Helper::get($data, $prefix . 'complex_id'),
            self::COMPLEX_TYPE   => Helper::get($data, $prefix . 'complex_type'),
            self::COMPLEX_NAME   => Helper::get($data, $prefix . 'complex_name'),
            self::BLOCK_NO       => Helper::get($data, $prefix . 'block_no'),
            self::ENTRANCE_NO    => Helper::get($data, $prefix . 'entrance_no'),
            self::FLOOR_NO       => Helper::get($data, $prefix . 'floor_no'),
            self::APARTMENT_NO   => Helper::get($data, $prefix . 'apartment_no'),
            self::POI_ID         => Helper::get($data, $prefix . 'poi_id'),
            self::ADDRESS_NOTE   => Helper::get($data, $prefix . 'address_note'),
            self::ADDRESS_LINE_1 => Helper::get($data, $prefix . 'address_line_1'),
            self::ADDRESS_LINE_2 => Helper::get($data, $prefix . 'address_line_2'),
            self::X              => Helper::get($data, $prefix . 'x'),
            self::Y              => Helper::get($data, $prefix . 'y'),
        );
    }

    private function send($url, $params = array(), $jsonDecode = true)
    {
        $params[self::USER_NAME] = $this->_username;
        $params[self::PASSWORD] = $this->_password;
        $params[self::LANGUAGE] = $this->_language;
        $params[self::CLIENT_SYSTEM_ID] = self::CSID;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'charset=utf-8'
        ));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::CONNECTTIMEOUT);

        $content = curl_exec($curl);

        curl_close($curl);

        if ($jsonDecode) {
            return json_decode($content, true);
        } else {
            return $content;
        }
    }

    private function clean($haystack)
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->clean($haystack[$key]);
            }

            if (!isset($haystack[$key]) || $haystack[$key] === '' || $haystack[$key] === array()) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

    private function translate($haystack)
    {
        if ($this->_language != 'BG') {
            foreach ($haystack as $key => $value) {
                if (is_array($value)) {
                    $haystack[$key] = $this->translate($haystack[$key]);
                } elseif (!empty(self::EN_FIELDS[$key])) {
                    $haystack[self::EN_FIELDS[$key]] = $value;
                }
            }

            return $haystack;
        } else {
            return $haystack;
        }
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getErrorsAsString()
    {
        $string = '';

        foreach ($this->_errors as $function => $errors) {
            foreach ($errors as $error) {
                if (is_array($error)) {
                    $string .= $function . ' : ' . implode(' : ', $error) . PHP_EOL;
                } else {
                    $string .= $function . ' : ' . $error . PHP_EOL;
                }
            }
        }

        return $string;
    }

    public function addError($function, $error)
    {
        if (!empty($error['error'])) {
            $this->_errors[$function][] = $error['error'];
        }
    }
}