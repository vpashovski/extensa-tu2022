<?php

use Speedy\Curl as SpeedyCurl;
use Speedy\Helper as Helper;

class Speedy {
	const NEW_API = false;
	const BULGARIA = 100;
	const ROMANIA = 642;
	const OFFICE_TYPE_APT = 3;
	const OFFICE_TYPE = 0;
	const FINAL_OPERATION = array(
		-14 => 'operation_delivered',
		124 => 'operation_delivered_back_to_sender',
		125 => 'operation_destroyed',
		127 => 'operation_theft_burglary',
		128 => 'operation_canceled',
		129 => 'operation_administrative_closure'
	);

	private $error;
	protected $ePSFacade;
	protected $resultLogin;
	protected $registry;
	protected $language;

	/**
	 * @var Speedy\Curl
	 */
	protected $speedyREST;

	protected $currency;
	public $baseCurrency = 'BGN';
	public $version = '4.0.7';

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->request = $registry->get('request');
		$this->log = $registry->get('log');
		$this->cache = $registry->get('cache');
		$this->currency = $registry->get('currency');
		$this->language = $registry->get('language');
		$this->registry = $registry;

		$this->initConnection();
	}

	protected function initConnection() {
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/util/Util.class.php');
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/EPSFacade.class.php');
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/soap/EPSSOAPInterfaceImpl.class.php');
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/ResultSite.class.php');
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/AddrNomen.class.php');

		try {
			if (!empty($this->request->post['shipping_speedy_server_address'])) {
				$server_address = $this->request->post['shipping_speedy_server_address'];
			} elseif ($this->config->get('shipping_speedy_server_address')) {
				$server_address = $this->config->get('shipping_speedy_server_address');
			} else {
				$server_address = 'https://www.speedy.bg/eps/main01.wsdl';
			}

			if (isset($this->request->post['shipping_speedy_username'])) {
				$username = $this->request->post['shipping_speedy_username'];
			} else {
				$username = $this->config->get('shipping_speedy_username');
			}

			if (isset($this->request->post['shipping_speedy_password'])) {
				$password = $this->request->post['shipping_speedy_password'];
			} else {
				$password = $this->config->get('shipping_speedy_password');
			}

			$ePSSOAPInterfaceImpl = new EPSSOAPInterfaceImpl($server_address);
			$this->ePSFacade = new EPSFacade($ePSSOAPInterfaceImpl, $username, $password);

			if (self::NEW_API) {
				$lang = $this->language->get('code') == 'bg' ? 'BG' : 'EN';
				$this->speedyREST = new Speedy\Curl($username, $password, $lang);

				$this->resultLogin = !empty($this->speedyREST->getContractClients());
			} else {
				$this->resultLogin = $this->ePSFacade->getResultLogin();
			}
		} catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->log->write('Speedy :: initConnection :: ' . $e->getMessage());
		}
	}

	public function getServices($lang = 'bg') {
		$this->error = '';
		$services = array();
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		if (isset($this->resultLogin)) {
			try {
				$services = $this->cache->get('speedy.listServices.' . md5($lang));

				if (empty($services)) {
					$listServices = $this->ePSFacade->listServices(time(), strtoupper($lang));

					if ($listServices) {
						foreach ($listServices as $service) {
							if ($service->getTypeId() == 26 || $service->getTypeId() == 36) {
								continue;
							}

							// Remove pallet services
							if ($service->getCargoType() == 2) {
								continue;
							}

							$services[$service->getTypeId()] = $service->getName();
						}
					}

					$this->cache->set('speedy.listServices.' . md5($lang), $services);
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getServices :: ' . $e->getMessage());
			}
		}

		return $services;
	}

	public function getOffices($name = null, $city_id = null, $lang = 'bg', $country_id = self::BULGARIA) {
		$this->error = '';
		$offices = array();
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$listOffices = $this->speedyREST->findOffice(array('countryId' => $country_id, 'siteId' => $city_id, 'name' => $name));

					if ($listOffices) {
						foreach ($listOffices as $office) {
							$offices[] = array(
								'id'     => $office['id'],
								'label'  => $office['id'] . ' ' . $office['name'] . ', ' . $office['address']['fullAddressString'],
								'value'  => $office['name'],
								'is_apt' => ($office['type'] == SpeedyCurl::OFFICE_TYPE_VALS['APT']) ? 1 : 0,
							);
						}
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$listOffices = $this->ePSFacade->listOfficesEx($name, $city_id, strtoupper($lang), $country_id);

					if ($listOffices) {
						foreach ($listOffices as $office) {
							$offices[] = array(
								'id'     => $office->getId(),
								'label'  => $office->getId() . ' ' . $office->getName() . ', ' . $office->getAddress()->getFullAddressString(),
								'value'  => $office->getName(),
								'is_apt' => ($office->getOfficeType() == self::OFFICE_TYPE_APT) ? 1 : 0,
							);
						}
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getOffices :: ' . $e->getMessage());
			}
		}

		return $offices;
	}

	public function getOfficeById($officeId, $city_id = null, $lang = 'bg') {
		$this->error = '';
		$result = '';
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$office = $this->speedyREST->getOffice($officeId);

					$result = array(
						'id'      => !empty($office['id']) ? $office['id']: '',
						'name'    => !empty($office['name']) ? $office['name']: '',
						'type'    => !empty($office['type']) ? $office['type']: '',
						'address' => array(
							'fullAddressString' => !empty($office['address']['fullAddressString']) ? $office['address']['fullAddressString'] : ''
						)
					);
				} else {
					$listOffices = $this->ePSFacade->listOfficesEx(null, $city_id, strtoupper($lang));
					if ($listOffices) {
						foreach ($listOffices as $val) {
							if ($val->getId() == $officeId) {
								$office = $val;
								break;
							}
						}
					}

					if (!empty($office)) {
						$result = array(
							'id'      => $office->getId(),
							'name'    => $office->getName(),
							'type'    => $office->getOfficeType() !== 3 ? SpeedyCurl::OFFICE_TYPE_VALS['OFFICE'] : SpeedyCurl::OFFICE_TYPE_VALS['APT'],
							'address' => array(
								'fullAddressString' => $office->getAddress()->getFullAddressString()
							)
						);
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getOfficeById :: ' . $e->getMessage());
			}
		}

		return $result;
	}

	public function getRandomOffice($city_id = null, $lang = 'bg', $is_apt = false, $country_id = self::BULGARIA) {
		$offices = $this->cache->get('speedy.offices.' . md5($city_id . $lang . $country_id));

		if (empty($offices)) {
			$offices = $this->getOffices(null, $city_id, $lang, $country_id);
			$this->cache->set('speedy.offices.' . md5($city_id . $lang . $country_id), $offices);
		}

		foreach ($offices as $value) {
			if ($value['is_apt'] && $is_apt) {
				return $value;
			}

			if (!$value['is_apt'] && !$is_apt) {
				return $value;
			}
		}
		return false;
	}

	public function getCities($name = null, $postcode = null, $country_id = null, $lang = 'bg') {
		$this->error = '';
		$cities = array();
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		if (isset($this->resultLogin)) {
			try {
				$data = array(
					'countryId' => $country_id,
					'postCode'  => $postcode,
					'name'      => $name
				);

				if (self::NEW_API) {
					$listSites = $this->speedyREST->findSite($data);
				} else {
					require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/ParamFilterSite.class.php');

					$paramFilterSite = new ParamFilterSite();

					if ($postcode) {
						$paramFilterSite->setName($name);
						$paramFilterSite->setPostCode($postcode);
					} else {
						$paramFilterSite->setSearchString($name);
					}

					if ($country_id) {
						$paramFilterSite->setCountryId($country_id);
					}

					$listSitesEx = $this->ePSFacade->listSitesEx($paramFilterSite, strtoupper($lang));
					$listSites = array();

					foreach ($listSitesEx as $result) {
						if ($result->isExactMatch()) {
							$listSites[] = $result->getSite();
						}
					}
				}

				if ($listSites) {
					$texts['bg'] = array(
						'mun' => 'общ.',
						'area' => 'обл.',
					);
					$texts['en'] = array(
						'mun' => 'Mun.',
						'area' => 'Area',
					);

					if (self::NEW_API) {
						foreach ($listSites as $city) {
							$label = $city['type'] . ' ' . $city['name'];
							$label .= $city['postCode'] ? ' (' . $city['postCode'] . ')' : '';
							$label .= ($city['municipality'] && $city['municipality'] != '-') ? ', ' . $texts[$lang]['mun'] . ' ' . $city['municipality'] : '';
							$label .= ($city['region'] && $city['region'] != '-') ? ', ' . $texts[$lang]['area'] . ' ' . $city['region'] : '';

							$cities[] = array(
								'id' => $city['id'],
								'label' => $label,
								'value' => $label,
								'postcode' => $city['postCode'],
								'nomenclature' => SpeedyCurl::SITE_ADDRESS_NOMENCLATURE_VALS[$city['addressNomenclature']]
							);
						}

						$this->error = $this->speedyREST->getErrorsAsString();
					} else {
						foreach ($listSites as $city) {
							$label = $city->getType() . ' ' . $city->getName();
							$label .= $city->getPostCode() ? ' (' . $city->getPostCode() . ')' : '';
							$label .= ($city->getMunicipality() && $city->getMunicipality() != '-') ? ', ' . $texts[$lang]['mun'] . ' ' . $city->getMunicipality() : '';
							$label .= ($city->getRegion() && $city->getRegion() != '-') ? ', ' . $texts[$lang]['area'] . ' ' . $city->getRegion() : '';

							$cities[] = array(
								'id' => $city->getId(),
								'label' => $label,
								'value' => $label,
								'postcode' => $city->getPostCode(),
								'nomenclature' => $city->getAddrNomen()->getValue()
							);
						}
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getCities :: ' . $e->getMessage());
			}
		}

		return $cities;
	}

	public function getQuarters($name = null, $city_id = null, $lang = 'bg') {
		$this->error = '';
		$quarters = array();
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$listQuarters = $this->speedyREST->findComplex(array('siteId' => $city_id, 'name' => $name));

					if ($listQuarters) {
						foreach ($listQuarters as $quarter) {
							$quarters[] = array(
								'id' => $quarter['id'],
								'label' => ($quarter['type'] ? $quarter['type'] . ' ' : '') . $quarter['name'],
								'value' => ($quarter['type'] ? $quarter['type'] . ' ' : '') . $quarter['name'],
							);
						}
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$listQuarters = $this->ePSFacade->listQuarters($name, $city_id, strtoupper($lang));

					if ($listQuarters) {
						foreach ($listQuarters as $quarter) {
							$quarters[] = array(
								'id' => $quarter->getId(),
								'label' => ($quarter->getType() ? $quarter->getType() . ' ' : '') . $quarter->getName(),
								'value' => ($quarter->getType() ? $quarter->getType() . ' ' : '') . $quarter->getName()
							);
						}
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getQuarters :: ' . $e->getMessage());
			}
		}

		return $quarters;
	}

	public function getStreets($name = null, $city_id = null, $lang = 'bg') {
		$this->error = '';
		$streets = array();
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$listStreets = $this->speedyREST->findStreet(array('siteId' => $city_id, 'name' => $name));

					if ($listStreets) {
						foreach ($listStreets as $street) {
							$streets[] = array(
								'id' => $street['id'],
								'label' => ($street['type'] ? $street['type'] . ' ' : '') . $street['name'],
								'value' => ($street['type'] ? $street['type'] . ' ' : '') . $street['name'],
							);
						}
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$listStreets = $this->ePSFacade->listStreets($name, $city_id, strtoupper($lang));

					if ($listStreets) {
						foreach ($listStreets as $street) {
							$streets[] = array(
								'id' => $street->getId(),
								'label' => ($street->getType() ? $street->getType() . ' ' : '') . $street->getName(),
								'value' => ($street->getType() ? $street->getType() . ' ' : '') . $street->getName()
							);
						}
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getStreets :: ' . $e->getMessage());
			}
		}

		return $streets;
	}

	public function getBlocks($name = null, $city_id = null, $lang = 'bg') {
		$this->error = '';
		$blocks = array();
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$listBlocks = $this->speedyREST->findBlock(array('siteId' => $city_id, 'name' => $name));

					if ($listBlocks) {
						foreach ($listBlocks as $block) {
							$blocks[] = array(
								'label' => $block['name'],
								'value' => $block['name']
							);
						}
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$listBlocks = $this->ePSFacade->listBlocks($name, $city_id, strtoupper($lang));

					if ($listBlocks) {
						foreach ($listBlocks as $block) {
							$blocks[] = array(
								'label' => $block,
								'value' => $block
							);
						}
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getBlocks :: ' . $e->getMessage());
			}
		}

		return $blocks;
	}

	public function getCountries($filter = null, $lang = 'bg') {
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/ParamFilterCountry.class.php');
		$this->error = '';
		$listCountries = array();
		$countries = array();
		$nomenclature = array(
			0 => 'NO',
			1 => 'FULL',
			2 => 'PARTIAL',
		);

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					if (!empty($filter['country_id'])) {
						$listCountries[] = $this->speedyREST->getCountry($filter['country_id']);
					} else {
						$data = array();

						if (!empty($filter['name'])) {
							$data['name'] = $filter['name'];
						} else {
							$data['name'] = '';
						}

						if (!empty($filter['iso_code_2'])) {
							$data['isoAlpha2'] = $filter['iso_code_2'];
						} else {
							$data['isoAlpha2'] = '';
						}

						$listCountries = $this->speedyREST->findCountry($data);
					}

					if ($listCountries) {
						foreach ($listCountries as $country) {
							$countries[] = array(
								'id'                   => $country['id'],
								'name'                 => $country['name'],
								'iso_code_2'           => $country['isoAlpha2'],
								'iso_code_3'           => $country['isoAlpha3'],
								'nomenclature'         => !empty($country['streetTypes']) && !empty($country['complexTypes']) ? 'FULL' : 'NO',
								'address_nomenclature' => ($country['addressType'] == SpeedyCurl::COUNTRY_ADDRESS_TYPE_VALS['FULL_ADDRESS']),
								'required_state'       => $country['requireState'],
								'required_postcode'    => !in_array('', $country['postCodeFormats']),
								'active_currency_code' => !empty($country['currencyCode']) ? $country['currencyCode'] : '',
							);
						}
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$paramFilterCountry = new ParamFilterCountry();

					if (!is_array($filter)) {
						$paramFilterCountry->setName($filter);
					} else {
						if (isset($filter['country_id'])) {
							$paramFilterCountry->setCountryId($filter['country_id']);
						}
						if (isset($filter['name'])) {
							$paramFilterCountry->setName($filter['name']);
						}
						if (isset($filter['iso_code_2'])) {
							$paramFilterCountry->setIsoAlpha2($filter['iso_code_2']);
						}
					}

					if (strtolower($lang) != 'bg') {
						$lang = 'en';
					}

					$listCountries = $this->ePSFacade->listCountriesEx($paramFilterCountry, strtoupper($lang));

					if ($listCountries) {
						foreach ($listCountries as $country) {
							$addressTypeParams = explode(';', $country->getAddressTypeParams());

							$countries[] = array(
								'id'                   => $country->getCountryId(),
								'name'                 => $country->getName(),
								'iso_code_2'           => $country->getIsoAlpha2(),
								'iso_code_3'           => $country->getIsoAlpha3(),
								'nomenclature'         => $nomenclature[$country->getSiteNomen()],
								'address_nomenclature' => ($country->getAddressTypeParams() && strtotime($addressTypeParams[0]) <= time() && $addressTypeParams[1] == 1) ? 1 : 0,
								'required_state'       => (int)$country->isRequireState(),
								'required_postcode'    => (int)$country->isRequirePostCode(),
								'active_currency_code' => $country->getActiveCurrencyCode(),
							);
						}
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getCountries :: ' . $e->getMessage());
			}
		}

		return $countries;
	}

	public function getStates($country_id, $name = null) {
		$this->error = '';
		$states = array();

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$listStates = $this->speedyREST->findState(array('countryId' => $country_id, 'name' => $name));

					if ($listStates) {
						foreach ($listStates as $state) {
							$states[] = array(
								'id'               => $state['id'],
								'name'             => $state['name'],
								'code'             => $state['stateAlpha'],
								'country_id'       => $state['countryId'],
							);
						}
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$listStates = $this->ePSFacade->listStates($country_id, $name);

					if ($listStates) {
						foreach ($listStates as $state) {
							$states[] = array(
								'id'               => $state->getStateId(),
								'name'             => $state->getName(),
								'code'             => $state->getStateAlpha(),
								'country_id'       => $state->getCountryId(),
							);
						}
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getStates :: ' . $e->getMessage());
			}
		}

		return $states;
	}

	public function getListContractClients() {
		$return = array();

		if (isset($this->resultLogin)) {
			if (self::NEW_API) {
				$clients = $this->speedyREST->getContractClients();

				foreach ($clients as $client) {
					$address = $client['address'];
					$address_string = $address['siteType']
						. $address['siteName'] . ', '
						. $address['streetType']
						. $address['streetName'] . ' '
						. $address['postCode'];

					$name = array();

					if (!empty($client['clientName'])) {
						$name[] = $client['clientName'];
					}

					if (!empty($client['objectName'])) {
						$name[] = $client['objectName'];
					}

					$return[$client['clientId']] = array(
						'clientId'   => $client['clientId'],
						'name'       => implode(', ', $name),
						'address'    => $address_string
					);
				}
			} else {
				$clients = $this->ePSFacade->listContractClients();

				foreach ($clients as $client) {
					$address = $client->getAddress();
					$address_string = $address->getSiteType()
						. $address->getSiteName() . ', '
						. $address->getRegionName() . ', '
						. $address->getStreetType()
						. $address->getStreetName() . ' '
						. $address->getPostCode();

					$name = array();

					if (!empty($client->getPartnerName())) {
						$name[] = $client->getPartnerName();
					}

					if (!empty($client->getObjectName())) {
						$name[] = $client->getObjectName();
					}

					$return[$client->getClientId()] = array(
						'clientId'   => $client->getClientId(),
						'name'       => implode(', ', $name),
						'address'    => $address_string
					);
				}
			}
		}

		return $return;
	}

	public function calculate($data) {
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/ParamCalculation.class.php');

		$this->error = '';
		$resultCalculation = array();

		if (isset($this->resultLogin)) {
			try {
				if (!isset($data['to_office'])) {

					if (!empty($data["city_id"])) {
						$hasOffice = $this->getRandomOffice($data["city_id"]);
					}
					$data['to_office'] = $hasOffice ? 1 : 0;
				}

				if ($data['active_currency_code'] && $this->currency->has($data['active_currency_code'])) {
					$data['total'] = $this->currency->convert($data['total'], $data['order_currency_code'], $data['active_currency_code']);
					$data['totalNoShipping'] = $this->currency->convert($data['totalNoShipping'], $data['order_currency_code'], $data['active_currency_code']);
				}

				$paramCalculation = new ParamCalculation();
				$paramCalculation->setSenderId((float)$data['client_id']);
				$paramCalculation->setBroughtToOffice($this->config->get('shipping_speedy_from_office') && $this->config->get('shipping_speedy_office_id'));
				$paramCalculation->setToBeCalled(!$data['abroad'] && !empty($data['to_office']) && $data['office_id']);
				$paramCalculation->setParcelsCount($data['count']);
				$paramCalculation->setWeightDeclared($data['weight']);
				$paramCalculation->setDocuments($this->config->get('shipping_speedy_documents'));
				$paramCalculation->setPalletized(false);
				$paramCalculation->setCheckTBCOfficeWorkDay(!(bool)$this->config->get('shipping_speedy_check_office_work_day'));

				if (!empty($data['parcels_size'])) {
					$parcel_sizes = array();
					$parcel_weight = 0;

					foreach ($data['parcels_size'] as $seqNo => $parcels_size) {
						$paramParcelInfo = new ParamParcelInfo();
						$paramParcelInfo->setSeqNo($seqNo);
						$paramParcelInfo->setParcelId(-1);

						if (!empty($parcels_size['depth']) || !empty($parcels_size['height']) || !empty($parcels_size['width'])) {
							$size = new Size();

							if ($parcels_size['depth']) {
								$size->setDepth($parcels_size['depth']);
							}

							if ($parcels_size['height']) {
								$size->setHeight($parcels_size['height']);
							}

							if ($parcels_size['width']) {
								$size->setWidth($parcels_size['width']);
							}

							$paramParcelInfo->setSize($size);
						} elseif (!empty($data['parcel_size'])) {
							$paramParcelInfo->setPredefinedSize($data['parcel_size']);
						}

						if (!empty($parcels_size['weight'])) {
							$paramParcelInfo->setWeight($parcels_size['weight']);

							$parcel_weight += $parcels_size['weight'];
						}

						$parcel_sizes[] = $paramParcelInfo;
					}

					if (count($parcel_sizes) == 1 && empty($parcel_sizes[0]->getWeight())) {
						$parcel_sizes[0]->setWeight($data['weight']);
					}

					if ($parcel_weight) {
						$paramCalculation->setWeightDeclared($parcel_weight);
					}

					$paramCalculation->setParcels($parcel_sizes);
				}

				if (!empty($data['fixed_time'])) {
					$paramCalculation->setFixedTimeDelivery($data['fixed_time']);
				} else {
					$paramCalculation->setFixedTimeDelivery(null);
				}

				if ($this->config->get('shipping_speedy_pricing') == 'free' || $this->config->get('shipping_speedy_pricing') == 'fixed' || $this->config->get('shipping_speedy_pricing') == 'table_rate') {
					$payerType = ParamCalculation::PAYER_TYPE_SENDER;
				} elseif (isset($data['payer_type'])) {
					$payerType = $data['payer_type'];
				} elseif ($this->config->get('shipping_speedy_pricing') == 'calculator' || $this->config->get('shipping_speedy_pricing') == 'calculator_fixed') {
					if (isset($data['abroad']) && $data['abroad']) {
						$payerType = ParamCalculation::PAYER_TYPE_SENDER;
					} else {
						$payerType = ParamCalculation::PAYER_TYPE_RECEIVER;
					}
				} else {
					$payerType = ParamCalculation::PAYER_TYPE_RECEIVER;
				}

				if (isset($data['loading'])) {
					if ($data['insurance']) {
						if ($data['fragile']) {
							$paramCalculation->setFragile(true);
						} else {
							$paramCalculation->setFragile(false);
						}

						if ($this->currency->has('BGN')) {
							$amountInsuranceBase = $this->currency->convert($data['totalNoShipping'], $data['active_currency_code'], 'BGN');
						} else {
							$amountInsuranceBase = $data['totalNoShipping'];
						}

						$paramCalculation->setAmountInsuranceBase(round($amountInsuranceBase, 2));
						$paramCalculation->setPayerTypeInsurance($payerType);
					} else {
						$paramCalculation->setFragile(false);
					}
				} elseif ($this->config->get('shipping_speedy_insurance')) {
					if ($this->config->get('shipping_speedy_fragile')) {
						$paramCalculation->setFragile(true);
					} else {
						$paramCalculation->setFragile(false);
					}

					if ($this->currency->has('BGN')) {
						$amountInsuranceBase = $this->currency->convert($data['totalNoShipping'], $data['active_currency_code'], 'BGN');
					} else {
						$amountInsuranceBase = $data['totalNoShipping'];
					}

					$paramCalculation->setAmountInsuranceBase(round($amountInsuranceBase, 2));
					$paramCalculation->setPayerTypeInsurance($payerType);
				} else {
					$paramCalculation->setFragile(false);
				}

				if (!(!$data['abroad'] && !empty($data['to_office']) && $data['office_id']) && (empty($data['is_apt']) || empty($data['to_office']))) {
					$paramCalculation->setReceiverSiteId($data['city_id']);
				}

				$paramCalculation->setPayerType($payerType);

				if (isset($data['cod']) && $data['cod'] && (!$this->config->get('shipping_speedy_money_transfer') || ($this->config->get('shipping_speedy_money_transfer') && $data['abroad']))) {
					$paramCalculation->setAmountCodBase($data['total']);
				} else {
					$paramCalculation->setAmountCodBase(0);
				}

				$paramCalculation->setTakingDate($data['taking_date']);
				$paramCalculation->setAutoAdjustTakingDate(true);

				if ($this->config->get('shipping_speedy_from_office') && $this->config->get('shipping_speedy_office_id')) {
					$paramCalculation->setWillBringToOfficeId($this->config->get('shipping_speedy_office_id'));
				}

				$aptAllowedCountries = array(self::BULGARIA, self::ROMANIA);

				if (in_array($data['country_id'], $aptAllowedCountries) && !empty($data['to_office'])) {
					if (!empty($data['office_id'])) {
						$paramCalculation->setOfficeToBeCalledId($data['office_id']);
					} else {
						$lang = ($data['abroad']) ? 'en' : $this->registry->get('language')->get('code');

						if (!empty($data['is_apt'])) {
							$office = $this->getRandomOffice($data['city_id'], $lang, true, $data['country_id']);
						} else {
							$paramCalculation->setToBeCalled(true);
							$paramCalculation->setReceiverSiteId(null);
							$office = $this->getRandomOffice($data['city_id'], $lang, false, $data['country_id']);
						}

						if (!empty($office)) {
							$paramCalculation->setOfficeToBeCalledId($office['id']);
						}
					}
				} else {
					$paramCalculation->setOfficeToBeCalledId(null);
				}

				if (isset($data['country_id']) && !in_array($data['country_id'], [self::BULGARIA, self::ROMANIA])) {
					$paramCalculation->setReceiverCountryId($data['country_id']);
					$paramCalculation->setReceiverPostCode($data['postcode']);
				}

				if (isset($data['abroad']) && $data['abroad'] && isset($data['cod']) && $data['cod'] && ($this->config->get('shipping_speedy_pricing') == 'calculator' || $this->config->get('shipping_speedy_pricing') == 'calculator_fixed')) {
					$paramCalculation->setIncludeShippingPriceInCod(true);
				}

				if (!empty($data['cod']) && ($this->config->get('shipping_speedy_money_transfer') && !$data['abroad'])) {
					$paramCalculation->setRetMoneyTransferReqAmount($data['total']);
					$paramCalculation->setAmountCodBase(null);
				}

				$obp = !empty($data['option_before_payment']) ? $data['option_before_payment'] : $this->config->get('shipping_speedy_option_before_payment');

				if ($obp != 'no_option' && !(!empty($data['is_apt']) && $this->config->get('shipping_speedy_ignore_obp'))) {
					$optionBeforePayment = new ParamOptionsBeforePayment();

					if ($obp == 'open') {
						$optionBeforePayment->setOpen(true);
					} elseif ($obp == 'test') {
						$optionBeforePayment->setTest(true);
					}

					$optionBeforePayment->setReturnPayerType($this->config->get('shipping_speedy_return_payer_type'));
					$optionBeforePayment->setReturnServiceTypeId($this->getReturnPackageServiceTypeId($paramCalculation));

					$paramCalculation->setOptionsBeforePayment($optionBeforePayment);
				}

				$result = array();

				if (self::NEW_API) {
					$data = $this->extractRESTCalculationParams($paramCalculation, $this->config->get('shipping_speedy_allowed_methods'));
					$resultCalculation = $this->speedyREST->calculate($data);

					foreach ($resultCalculation as $key => $service) {
						if (!empty($service['error'])) {
							unset($resultCalculation[$key]);
						} else {
							$result[] = array(
								'error'     => !empty($service['error']) ? $service['error'] : '',
								'serviceId' => !empty($service['serviceId']) ? $service['serviceId'] : '',
								'price'     => array(
									'total' => !empty($service['price']['total']) ? $service['price']['total'] : ''
								)
							);
						}
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$resultCalculation = $this->ePSFacade->calculateMultipleServices($paramCalculation, $this->config->get('shipping_speedy_allowed_methods'));

					foreach ($resultCalculation as $key => $service) {
						if (!$service->getErrorDescription()) {
							$result[] = array(
								'error'     => '',
								'serviceId' => $service->getServiceTypeId(),
								'price'     => array(
									'total' => $service->getResultInfo()->getAmounts()->getTotal()
								)
							);
						}
					}

					$resultCalculation = array_values($result);
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: calculate :: ' . $e->getMessage());
			}
		}

		return $resultCalculation;
	}

	private function extractRESTCalculationParams($paramCalculation, $serviceIds) {
		$return = array();
		$parcels = array();

		foreach ($paramCalculation->getParcels() as $parcel) {
			$arr = array(
				'id'     => $parcel->getParcelId(),
				'no'     => $parcel->getSeqNo(),
				'weight' => $parcel->getWeight(),
			);

			if ($parcel->getSize()) {
				$arr['size'] = array(
					'width'  => $parcel->getSize()->getWidth(),
					'depth'  => $parcel->getSize()->getDepth(),
					'height' => $parcel->getSize()->getHeight(),
				);
			}

			$parcels[] = $arr;
		}

		$return['sender.client_id'] = $paramCalculation->getSenderId();
		$return['sender.office_id'] = $paramCalculation->getWillBringToOfficeId();

		$return['recipient.private_person'] = true;
		$return['recipient.office_id'] = $paramCalculation->getOfficeToBeCalledId();
		$return['recipient.address_location.country_id'] = $paramCalculation->getReceiverCountryId();
		$return['recipient.address_location.site_id'] = $paramCalculation->getReceiverSiteId();
		$return['recipient.address_location.post_code'] = $paramCalculation->getReceiverPostCode();

		if (is_numeric($paramCalculation->getTakingDate())) {
			$return['service.pickup_date'] = date('Y-m-d', $paramCalculation->getTakingDate());
		} else {
			$return['service.pickup_date'] = date('Y-m-d', strtotime($paramCalculation->getTakingDate()));
		}

		$return['service.auto_adjust_pickup_date'] = true;
		$return['service.service_ids'] = $serviceIds;

		$return['service.additional_services.fixed_time_delivery'] = $paramCalculation->getFixedTimeDelivery();

		if ($paramCalculation->getAmountCodBase()) {
			$return['service.additional_services.cod.amount'] = $paramCalculation->getAmountCodBase();
			$return['service.additional_services.cod.processing_type'] = SpeedyCurl::PROCESSING_TYPE_VALS['CASH'];
		} elseif ($paramCalculation->getRetMoneyTransferReqAmount()) {
			$return['service.additional_services.cod.amount'] = $paramCalculation->getRetMoneyTransferReqAmount();
			$return['service.additional_services.cod.processing_type'] = SpeedyCurl::PROCESSING_TYPE_VALS['POSTAL_MONEY_TRANSFER'];
		}

		$return['service.additional_services.cod.include_shipping_price'] = $paramCalculation->getIncludeShippingPriceInCod();
		$return['service.additional_services.declared_value.amount'] = $paramCalculation->getAmountInsuranceBase();
		$return['service.additional_services.declared_value.fragile'] = (bool)$paramCalculation->isFragile(); 

		if ($paramCalculation->getOptionsBeforePayment()) {
			$obp = $paramCalculation->getOptionsBeforePayment();

			if ($obp->isOpen()) {
				$return['service.additional_services.obp_details.option'] = SpeedyCurl::OBP_VALS['OPEN'];
			} elseif ($obp->isTest()) {
				$return['service.additional_services.obp_details.option'] = SpeedyCurl::OBP_VALS['TEST'];
			}

			$return['service.additional_services.obp_details.return_shipment_service_id'] = $obp->getReturnServiceTypeId();

			if ($obp->getReturnPayerType() == 0) {
				$return['service.additional_services.obp_details.return_shipment_payer'] = SpeedyCurl::RETURN_SHIPMENT_PAYER_VALS['SENDER'];
			} elseif ($obp->getReturnPayerType() == 1) {
				$return['service.additional_services.obp_details.return_shipment_payer'] = SpeedyCurl::RETURN_SHIPMENT_PAYER_VALS['RECIPIENT'];
			} elseif($obp->getReturnPayerType() == 2) {
				$return['service.additional_services.obp_details.return_shipment_payer'] = SpeedyCurl::RETURN_SHIPMENT_PAYER_VALS['THIRD_PARTY'];
			}
		}

		$return['content.parcels_count'] = $paramCalculation->getParcelsCount();
		$return['content.total_weight'] = $paramCalculation->getWeightDeclared();
		$return['content.documents'] = (bool)$paramCalculation->isDocuments();
		$return['content.palletized'] = (bool)$paramCalculation->isPalletized();
		$return['content.parcels'] = $parcels;

		if (\ParamCalculation::PAYER_TYPE_SENDER == $paramCalculation->getPayerType()) {
			$return['payment.courier_service_payer'] = SpeedyCurl::SHIPMENT_PAYMENT_PAYER_VALS['SENDER'];
		} else {
			$return['payment.courier_service_payer'] = SpeedyCurl::SHIPMENT_PAYMENT_PAYER_VALS['RECIPIENT'];
		}

		return $return;
	}

	public function getAllowedDaysForTaking($data) {
		$this->error = '';
		$firstAvailableDate = '';

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$data = array(
						'service_id'             => $data['shipping_method_id'],
						'starting_date'          => date("Y-m-d", $data['taking_date']),
					);

					if ($this->config->get('shipping_speedy_from_office') && $this->config->get('shipping_speedy_office_id')) {
						$data['sender_office_id'] = $this->config->get('shipping_speedy_office_id');
						$data['sender_private_person'] = true;
					} else {
						$data['sender_client_id'] = $this->config->get('shipping_speedy_client_id');
					}

					$takingTime = $this->speedyREST->pickupTerms($data);

					if ($takingTime) {
						$firstAvailableDate = $takingTime[0];
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					if ($this->config->get('shipping_speedy_from_office') && $this->config->get('shipping_speedy_office_id')) {
						$senderSiteId = null;
						$senderOfficeId = $this->config->get('shipping_speedy_office_id');
					} else {
						$resultClientData = $this->ePSFacade->getClientById($this->resultLogin->getClientId());
						$senderSiteId = $resultClientData->getAddress()->getSiteId();
						$senderOfficeId = null;
					}

					$takingTime = $this->ePSFacade->getAllowedDaysForTaking($data['shipping_method_id'], $senderSiteId, $senderOfficeId, $data['taking_date']);

					if ($takingTime) {
						$firstAvailableDate = $takingTime[0];
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: getAllowedDaysForTaking :: ' . $e->getMessage());
			}
		}

		return $firstAvailableDate;
	}

	public function createBillOfLading($data, $order) {
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/ParamCalculation.class.php');

		$this->error = '';
		$bol = array();

		if (isset($this->resultLogin)) {
			try {
				$sender = new ParamClientData();
				$sender->setClientId((float)$data['client_id']);

				if ($this->config->get('shipping_speedy_telephone')) {
					$senderPhone = new ParamPhoneNumber();
					$senderPhone->setNumber($this->config->get('shipping_speedy_telephone'));
					$sender->setPhones(array(0 => $senderPhone));
				}

				if ($this->config->get('shipping_speedy_name')) {
					$sender->setContactName($this->config->get('shipping_speedy_name'));
				}

				$receiverAddress = new ParamAddress();
				if ($data['city_id']) {
					$receiverAddress->setSiteId($data['city_id']);
				} else {
					$receiverAddress->setSiteName($data['city']);
				}

				if (isset($data['quarter']) && $data['quarter']) {
					$receiverAddress->setQuarterName($data['quarter']);
				}

				if (isset($data['quarter_id']) && $data['quarter_id']) {
					$receiverAddress->setQuarterId($data['quarter_id']);
				}

				if (isset($data['street']) && $data['street']) {
					$receiverAddress->setStreetName($data['street']);
				}

				if (isset($data['street_id']) && $data['street_id']) {
					$receiverAddress->setStreetId($data['street_id']);
				}

				if (isset($data['street_no']) && $data['street_no']) {
					$receiverAddress->setStreetNo($data['street_no']);
				}

				if (isset($data['block_no']) && $data['block_no']) {
					$receiverAddress->setBlockNo($data['block_no']);
				}

				if (isset($data['entrance_no']) && $data['entrance_no']) {
					$receiverAddress->setEntranceNo($data['entrance_no']);
				}

				if (isset($data['floor_no']) && $data['floor_no']) {
					$receiverAddress->setFloorNo($data['floor_no']);
				}

				if (isset($data['apartment_no']) && $data['apartment_no']) {
					$receiverAddress->setApartmentNo($data['apartment_no']);
				}

				if (isset($data['note']) && $data['note']) {
					$receiverAddress->setAddressNote($data['note']);
				}

				if (isset($data['state_id']) && $data['state_id']) {
					$receiverAddress->setStateId($data['state_id']);
				}

				if (isset($data['country_id']) && $data['country_id']) {
					$receiverAddress->setCountryId($data['country_id']);
				}

				if (isset($data['postcode']) && $data['postcode']) {
					$receiverAddress->setPostCode($data['postcode']);
				}

				if (isset($data['address_1']) && $data['address_1']) {
					$receiverAddress->setFrnAddressLine1($data['address_1']);
				}

				if (isset($data['address_2']) && $data['address_2']) {
					$receiverAddress->setFrnAddressLine2($data['address_2']);
				}

				if (isset($data['state_id']) && $data['state_id']) {
					$receiverAddress->setStateId($data['state_id']);
				}

				$receiver = new ParamClientData();
				$receiverPhone = new ParamPhoneNumber();
				$receiverPhone->setNumber($order['telephone']);
				$receiver->setPhones(array(0 => $receiverPhone));

				if (!empty($order['payment_company']) && mb_strlen($order['payment_company']) >= 3) {
					$receiver->setContactName($order['shipping_firstname'] . ' ' . $order['shipping_lastname']);
					$receiver->setPartnerName($order['payment_company']);
				} else {
					$receiver->setPartnerName($order['shipping_firstname'] . ' ' . $order['shipping_lastname']);
				}

				$receiver->setEmail($order['email']);

				$picking = new ParamPicking();
				$picking->setClientSystemId(1710068407); //OpenCart
				$picking->setRef1($order['order_id']);
				$picking->setParcelsCount($data['count']);
				$picking->setWeightDeclared($data['weight']);

				if ($data['active_currency_code'] && $this->currency->has($data['active_currency_code'])) {
					$data['total'] = $this->currency->convert($data['total'], $order['currency_code'], $data['active_currency_code']);
					$data['totalNoShipping'] = $this->currency->convert($data['totalNoShipping'], $order['currency_code'], $data['active_currency_code']);
				}

				if (!empty($data['convertion_to_win1251'])) {
					$picking->setAutomaticConvertionToWin1251(true);
				}

				if (!empty($data['parcels_size'])) {
					$parcel_sizes = array();
					$parcel_weight = 0;

					foreach ($data['parcels_size'] as $seqNo => $parcels_size) {
						$paramParcelInfo = new ParamParcelInfo();
						$paramParcelInfo->setSeqNo($seqNo);
						$paramParcelInfo->setParcelId(-1);

						if (!empty($parcels_size['depth']) || !empty($parcels_size['height']) || !empty($parcels_size['width'])) {
							$size = new Size();

							if ($parcels_size['depth']) {
								$size->setDepth($parcels_size['depth']);
							}

							if ($parcels_size['height']) {
								$size->setHeight($parcels_size['height']);
							}

							if ($parcels_size['width']) {
								$size->setWidth($parcels_size['width']);
							}

							$paramParcelInfo->setSize($size);
						} elseif (!empty($data['parcel_size'])) {
							$paramParcelInfo->setPredefinedSize($data['parcel_size']);
						}

						if (!empty($parcels_size['weight'])) {
							$paramParcelInfo->setWeight($parcels_size['weight']);

							$parcel_weight += $parcels_size['weight'];
						}

						$parcel_sizes[] = $paramParcelInfo;
					}

					if (count($parcel_sizes) == 1 && empty($parcel_sizes[0]->getWeight())) {
						$parcel_sizes[0]->setWeight($data['weight']);
					}

					if ($parcel_weight) {
						$picking->setWeightDeclared($parcel_weight);
					}

					$picking->setParcels($parcel_sizes);
				}

				if (!empty($data['fixed_time'])) {
					$picking->setFixedTimeDelivery($data['fixed_time']);
				}

				$picking->setServiceTypeId($data['shipping_method_id']);

				if ($data['to_office'] && $data['office_id']) {
					$picking->setOfficeToBeCalledId($data['office_id']);

					$office = $this->getOfficeById($data['office_id']);
				} else {
					$receiver->setAddress($receiverAddress);
					$picking->setOfficeToBeCalledId(null);
					$office = array();
				}

				$service = $this->getServiceById($data['shipping_method_id']);

				if((empty($office) || $office['type'] != SpeedyCurl::OFFICE_TYPE_VALS['APT']) && !empty($service)) {
					if ($service->getAllowanceBackDocumentsRequest()->getValue() == 'ALLOWED') {
						$picking->setBackDocumentsRequest($this->config->get('shipping_speedy_back_documents'));
					}

					if ($service->getAllowanceBackReceiptRequest()->getValue() == 'ALLOWED') {
						$picking->setBackReceiptRequest((bool)$this->config->get('shipping_speedy_back_receipt'));
					}
				}

				if ($this->config->get('shipping_speedy_from_office') && $this->config->get('shipping_speedy_office_id')) {
					$picking->setWillBringToOffice(true);
					$picking->setWillBringToOfficeId($this->config->get('shipping_speedy_office_id'));
				} else {
					$picking->setWillBringToOffice(false);
				}

				$picking->setContents($data['contents']);
				$picking->setPacking($data['packing']);
				$picking->setPackId(null);
				$picking->setDocuments($this->config->get('shipping_speedy_documents'));
				$picking->setPalletized(false);

				if (($this->config->get('shipping_speedy_pricing') == 'free' && $this->config->get('shipping_speedy_free_shipping_total') <= $data['total']) || $this->config->get('shipping_speedy_pricing') == 'fixed' || $this->config->get('shipping_speedy_pricing') == 'table_rate') {
					$payerType = ParamCalculation::PAYER_TYPE_SENDER;
				} else {
					$payerType = $data['payer_type'];
				}

				if ($data['insurance']) {
					if ($data['fragile']) {
						$picking->setFragile(true);
					} else {
						$picking->setFragile(false);
					}

					$picking->setAmountInsuranceBase($data['totalNoShipping']);

					$picking->setPayerTypeInsurance($payerType);
				} else {
					$picking->setFragile(false);
				}

				$picking->setSender($sender);
				$picking->setReceiver($receiver);

				$picking->setPayerType($payerType);

				$picking->setTakingDate($data['taking_date']);

				if ($data['deffered_days']) {
					$picking->setDeferredDeliveryWorkDays($data['deffered_days']);
				}

				if ($data['client_note']) {
					$picking->setNoteClient($data['client_note']);
				}

				if ($this->config->get('shipping_speedy_pricing') == 'table_rate') {
					$data['total'] += $data['shipping_method_cost'];
				}

				if ($data['cod']) {
					$picking->setAmountCodBase($data['total']);
				} else {
					$picking->setAmountCodBase(0);
				}

				if ($data['cod'] && ($this->config->get('shipping_speedy_money_transfer') && !$data['abroad'])) {
					$picking->setRetMoneyTransferReqAmount($data['total']);
					$picking->setAmountCodBase(0);
				}

				$optionBeforePayment = new ParamOptionsBeforePayment();

				if (isset($data['option_before_payment']) && $data['option_before_payment'] != 'no_option' && (empty($office) || $office['type'] != SpeedyCurl::OFFICE_TYPE_VALS['APT'])) {
						if ($data['option_before_payment'] == 'open') {
							$optionBeforePayment->setOpen(true);
						} elseif ($data['option_before_payment'] == 'test') {
							$optionBeforePayment->setTest(true);
						}

						$optionBeforePayment->setReturnPayerType($this->config->get('shipping_speedy_return_payer_type'));
						$optionBeforePayment->setReturnServiceTypeId($this->getReturnPackageServiceTypeId($picking));
				}
				$picking->setOptionsBeforePayment($optionBeforePayment);

				if (isset($data['abroad']) && $data['abroad'] && $data['cod'] && ($data['price_gen_method'] == 'calculator' || $data['price_gen_method'] == 'calculator_fixed')) {
					$picking->setIncludeShippingPriceInCod(true);
				}

				if ($this->config->get('shipping_speedy_return_voucher') && (!isset($data['abroad']) || !$data['abroad'])) {
					$returnVoucher = new ParamReturnVoucher();
					$returnVoucher->setServiceTypeId($this->getReturnVoucherServiceTypeId($picking));
					$returnVoucher->setPayerType($this->config->get('shipping_speedy_return_voucher_payer_type'));

					$picking->setReturnVoucher($returnVoucher);
				}

				if (self::NEW_API) {
					$params = $this->extractRESTPickingParams($picking, $data);
					$result = $this->speedyREST->createShipment($params);

					$this->error = $this->speedyREST->getErrorsAsString();

					if (!$this->error) {
						$bol['bol_id'] = $result['parcels'][0]['id'];
						$bol['total'] = $result['price']['total'];
					}
				} else {
					$result = $this->ePSFacade->createBillOfLading($picking);
					$parcels = $result->getGeneratedParcels();
					$parcels = $parcels[0];
					$bol['bol_id'] = $parcels->getParcelId();
					$bol['total'] = $result->getAmounts()->getTotal();
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: createBillOfLading :: ' . $e->getMessage());
			}
		}

		return $bol;
	}

	private function extractRESTPickingParams($picking, $data) {
		$return = array();
		$parcels = array();

		foreach ($picking->getParcels() as $parcel) {
			$arr = array(
				'id'      => $parcel->getParcelId(),
				'no'      => $parcel->getSeqNo(),
				'weight'  => $parcel->getWeight(),
			);

			if ($parcel->getSize()) {
				$arr['size'] = array(
					'width' => $parcel->getSize()->getWidth(),
					'depth' => $parcel->getSize()->getDepth(),
					'height' => $parcel->getSize()->getHeight(),
				);
			}

			$parcels[] = $arr;
		}

		if (!empty($picking->getSender()->getPhones())) {
			$phone = $picking->getSender()->getPhones();
			$phone = $phone[0];
			$sender_phone_number = $phone->getNumber();
		} else {
			$sender_phone_number = '';
		}

		if (!empty($picking->getReceiver()->getPhones())) {
			$phone = $picking->getReceiver()->getPhones();
			$phone = $phone[0];
			$receiver_phone_number = $phone->getNumber();
		} else {
			$receiver_phone_number = '';
		}

		$return['sender.client_id'] = $picking->getSender()->getClientId();
		$return['sender.contact_name'] = $picking->getSender()->getContactName();
		$return['sender.office_id'] = $picking->getWillBringToOfficeId();
		$return['sender.phone_1.number'] = $sender_phone_number;

		$return['recipient.phone_1.number'] = $receiver_phone_number;
		$return['recipient.contact_name'] = $picking->getReceiver()->getContactName();
		$return['recipient.client_name'] = $picking->getReceiver()->getPartnerName();
		$return['recipient.email'] = $picking->getReceiver()->getEmail();
		$return['recipient.office_id'] = $picking->getOfficeToBeCalledId();
		$return['recipient.private_person'] = true;

		if ($picking->getReceiver()->getAddress()) {
			$return['recipient.country_id']     = $picking->getReceiver()->getAddress()->getCountryId();
			$return['recipient.state_id']       = $picking->getReceiver()->getAddress()->getStateId();
			$return['recipient.site_id']        = $picking->getReceiver()->getAddress()->getSiteId();
			$return['recipient.site_name']      = $picking->getReceiver()->getAddress()->getSiteName();
			$return['recipient.post_code']      = $picking->getReceiver()->getAddress()->getPostCode();
			$return['recipient.street_id']      = $picking->getReceiver()->getAddress()->getStreetId();
			$return['recipient.street_name']    = $picking->getReceiver()->getAddress()->getStreetName();
			$return['recipient.street_no']      = $picking->getReceiver()->getAddress()->getStreetNo();
			$return['recipient.complex_id']     = $picking->getReceiver()->getAddress()->getQuarterId();
			$return['recipient.complex_name']   = $picking->getReceiver()->getAddress()->getQuarterName();
			$return['recipient.block_no']       = $picking->getReceiver()->getAddress()->getBlockNo();
			$return['recipient.entrance_no']    = $picking->getReceiver()->getAddress()->getEntranceNo();
			$return['recipient.floor_no']       = $picking->getReceiver()->getAddress()->getFloorNo();
			$return['recipient.apartment_no']   = $picking->getReceiver()->getAddress()->getApartmentNo();
			$return['recipient.address_note']   = $picking->getReceiver()->getAddress()->getAddressNote();
			$return['recipient.address_line_1'] = $picking->getReceiver()->getAddress()->getFrnAddressLine1();
			$return['recipient.address_line_2'] = $picking->getReceiver()->getAddress()->getFrnAddressLine2();
		}

		$return['service.additional_services.fixed_time_delivery'] = $picking->getFixedTimeDelivery();
		$return['service.additional_services.declared_value.amount'] = $picking->getAmountInsuranceBase();
		$return['service.additional_services.declared_value.fragile'] = (bool)$picking->isFragile();
		$return['service.additional_services.cod.amount'] = $picking->getAmountCodBase();
		$return['service.additional_services.cod.include_shipping_price'] = $picking->getIncludeShippingPriceInCod();
		$return['service.additional_services.returns.return_receipt.enabled'] = (bool)$picking->isBackReceiptRequest();
		$return['service.additional_services.returns.rod.enabled'] = (bool)$picking->isBackDocumentsRequest();

		if ($picking->getReturnVoucher()) {
			$return['service.additional_services.returns.return_voucher.service_id'] = $picking->getReturnVoucher()->getServiceTypeId();

			if ($picking->getReturnVoucher()->getPayerType() == 0) {
				$return['service.additional_services.returns.return_voucher.payer'] = SpeedyCurl::RETURN_VOUCHER_PAYER_VALS['SENDER'];
			} elseif ($picking->getOptionsBeforePayment()->getPayerType() == 1) {
				$return['service.additional_services.returns.return_voucher.payer'] = SpeedyCurl::RETURN_VOUCHER_PAYER_VALS['RECIPIENT'];
			} else {
				$return['service.additional_services.returns.return_voucher.payer'] = SpeedyCurl::RETURN_VOUCHER_PAYER_VALS['THIRD_PARTY'];
			}
		}

		if ($picking->getRetMoneyTransferReqAmount()) {
			$return['service.additional_services.cod.processing_type'] = SpeedyCurl::PROCESSING_TYPE_VALS['POSTAL_MONEY_TRANSFER'];
		} else {
			$return['service.additional_services.cod.processing_type'] = SpeedyCurl::PROCESSING_TYPE_VALS['CASH'];
		}

		if ($picking->getOptionsBeforePayment()->isOpen()) {
			$return['service.additional_services.obp_details.option'] = SpeedyCurl::OBP_VALS['OPEN'];
		} elseif ($picking->getOptionsBeforePayment()->isTest()) {
			$return['service.additional_services.obp_details.option'] = SpeedyCurl::OBP_VALS['TEST'];
		}

		$return['service.additional_services.obp_details.return_shipment_service_id'] = $picking->getOptionsBeforePayment()->getReturnServiceTypeId();

		if ($picking->getOptionsBeforePayment()->getReturnPayerType() == 0) {
			$return['payment.declared_value_payer'] = SpeedyCurl::RETURN_SHIPMENT_PAYER_VALS['SENDER'];
		} elseif ($picking->getOptionsBeforePayment()->getReturnPayerType() == 1) {
			$return['payment.declared_value_payer'] = SpeedyCurl::RETURN_SHIPMENT_PAYER_VALS['RECIPIENT'];
		} else {
			$return['payment.declared_value_payer'] = SpeedyCurl::RETURN_SHIPMENT_PAYER_VALS['THIRD_PARTY'];
		}

		$return['service.service_id'] = $picking->getServiceTypeId();
		$return['service.deferred_days'] = $picking->getDeferredDeliveryWorkDays();

		if (is_numeric($data['taking_date'])) {
			$return['service.pickup_date'] = date('Y-m-d', $data['taking_date']);
		} else {
			$return['service.pickup_date'] = date('Y-m-d', strtotime($data['taking_date']));
		}

		$return['content.parcels_count'] = $picking->getParcelsCount();
		$return['content.total_weight'] = $picking->getWeightDeclared();
		$return['content.documents'] = (bool)$picking->isDocuments();
		$return['content.contents'] = $picking->getContents();
		$return['content.package'] = $picking->getPacking();
		$return['content.palletized'] = (bool)$picking->isPalletized();
		$return['content.parcels'] = $parcels;

		if ($picking->getPayerTypeInsurance() == 0) {
			$return['payment.declared_value_payer'] = SpeedyCurl::DECLARED_VALUE_PAYER_VALS['SENDER'];
		} elseif ($picking->getPayerTypeInsurance() == 1) {
			$return['payment.declared_value_payer'] = SpeedyCurl::DECLARED_VALUE_PAYER_VALS['RECIPIENT'];
		} else {
			$return['payment.declared_value_payer'] = SpeedyCurl::DECLARED_VALUE_PAYER_VALS['THIRD_PARTY'];
		}

		if ($picking->getPayerType() == 0) {
			$return['payment.courier_service_payer'] = SpeedyCurl::COURIER_SERVICE_PAYER_VALS['SENDER'];
		} elseif ($picking->getPayerType() == 1) {
			$return['payment.courier_service_payer'] = SpeedyCurl::COURIER_SERVICE_PAYER_VALS['RECIPIENT'];
		} else {
			$return['payment.courier_service_payer'] = SpeedyCurl::COURIER_SERVICE_PAYER_VALS['THIRD_PARTY'];
		}

		$return['shipment_note'] = $picking->getNoteClient();
		$return['ref1'] = $picking->getRef1();

		return $return;
	}

	public function createPDF($bol_id, $additional_copy_for_sender_value = 0) {
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/ParamPDF.class.php');

		$this->error = '';
		$pdf = '';

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$data = array();

					$shpment_information = $this->speedyREST->shipmentInformation(array(
						'shipment_ids' => array($bol_id)
					));

					if (!empty($shpment_information[0]['content']['parcels'])) {
						$parcels = $shpment_information[0]['content']['parcels'];

						foreach ($parcels as $parcel) {
							$data['parcels'][]['parcel.id'] = $parcel['id'];
						}
					}

					$data['paper_size'] = SpeedyCurl::PAPER_SIZE_VALS['A4'];
					$pdf = $this->speedyREST->sPrint($data);

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$paramPDF = new ParamPDF();

					if ($this->config->get('shipping_speedy_label_printer')) {
						$pickingParcels = $this->ePSFacade->getPickingParcels((float)$bol_id);

						$ids = array();

						foreach ($pickingParcels as $parcel) {
							$ids[] = $parcel->getParcelId();
						}

						$paramPDF->setIds($ids);
						$paramPDF->setType(ParamPDF::PARAM_PDF_TYPE_LBL);
					} else {
						$paramPDF->setIds((float)$bol_id);
						$paramPDF->setType(ParamPDF::PARAM_PDF_TYPE_BOL);
					}

					$paramPDF->setIncludeAutoPrintJS(true);

					$paramPDF->setAdditionalCopyForSender((bool)$additional_copy_for_sender_value);

					$pdf = $this->ePSFacade->createPDF($paramPDF);
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: createPDF :: ' . $e->getMessage());
			}
		}

		return $pdf;
	}

	public function createReturnVoucher($bol_id) {
		require_once(DIR_SYSTEM . 'library/speedy-eps-lib/ver01/ParamPDF.class.php');
		$this->error = '';
		$pdf = '';

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$data['shipment_ids'] = array($bol_id);

					$pdf = $this->speedyREST->printVoucher($data);

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$paramPDF = new ParamPDF();

					if ($this->config->get('shipping_speedy_label_printer')) {
						$pickingParcels = $this->ePSFacade->getPickingParcels((float)$bol_id);

						$ids = array();

						foreach ($pickingParcels as $parcel) {
							$ids[] = $parcel->getParcelId();
						}

						$paramPDF->setIds($ids);
					} else {
						$paramPDF->setIds((float)$bol_id);
					}

					$paramPDF->setType(30); // ParamPDF::PARAM_PDF_TYPE_VOUCHER

					$paramPDF->setIncludeAutoPrintJS(true);

					$pdf = $this->ePSFacade->createPDF($paramPDF);
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: createReturnVoucher :: ' . $e->getMessage());
			}
		}

		return $pdf;
	}

	public function requestCourier($bol_ids) {
		$this->error = '';
		$result = array();

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					foreach ($bol_ids as $bol_id) {
						$result[] = $this->speedyREST->finalizePendingShipment(array('shipment_id' => $bol_id));
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$paramOrder = new ParamOrder();
					$paramOrder->setBillOfLadingsList(array_map('floatval', $bol_ids));
					$paramOrder->setBillOfLadingsToIncludeType(ParamOrder::ORDER_BOL_INCLUDE_TYPE_EXPLICIT);

					if ($this->config->get('shipping_speedy_telephone')) {
						$paramPhoneNumber = new ParamPhoneNumber();
						$paramPhoneNumber->setNumber($this->config->get('shipping_speedy_telephone'));
						$paramOrder->setPhoneNumber($paramPhoneNumber);
					}

					$paramOrder->setWorkingEndTime($this->config->get('shipping_speedy_workingtime_end_hour') . $this->config->get('shipping_speedy_workingtime_end_min'));
					$paramOrder->setContactName($this->config->get('shipping_speedy_name'));

					$resp = $this->ePSFacade->createOrder($paramOrder);

					foreach ($resp as $value) {
						$result[] = array(
							'id'    => $value->getBillOfLading(),
							'error' => $value->getErrorDescriptions(),
						);
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: requestCourier :: ' . $e->getMessage());
			}
		}

		return $result;
	}

	public function cancelBol($bol_id) {
		$this->error = '';
		$cancelled = false;

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$cancelled = $this->speedyREST->cancelShipment(array(
						'shipment_id' => $bol_id,
						'comment'     => 'Cancel shipment'
					));

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$this->ePSFacade->invalidatePicking((float)$bol_id);
					$cancelled = true;
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: cancelBol :: ' . $e->getMessage());
			}
		}

		return $cancelled;
	}

	public function getError($type = null) {
		if ($type) {
			if (isset($this->error[$type])) {
				return $this->error[$type];
			} else {
				return false;
			}
		} else {
			return $this->error;
		}
	}

	public function setError($error, $type = null) {
		if ($type) {
			$this->error[$type] = $error;
		} else {
			$this->error = $error;
		}
	}

	public function checkCredentials($username, $password) {
		if (self::NEW_API) {
			$lang = $this->language->get('code') == 'bg' ? 'BG' : 'EN';
			$this->speedyREST->login($username, $password, $lang);

			try {
				$this->resultLogin = !empty($this->speedyREST->getContractClients());

				return !empty($this->resultLogin);
			} catch (Exception $e) {
				return false;
			}
		} else {
			$this->ePSFacade->setUsername($username);
			$this->ePSFacade->setPassword($password);

			try {
				return $this->ePSFacade->login();
			} catch (ClientException $ce) {
				return false;
			} catch (ServerException $se) {
				return false;
			}
		}
	}

	public function isAvailableMoneyTransfer() {
		if (self::NEW_API) {
			return true;
		} else {
			if (isset($this->resultLogin)) {
				try {
					return in_array('101', $this->ePSFacade->getAdditionalUserParams(time()));
				} catch (ClientException $ce) {
					return false;
				} catch (ServerException $se) {
					return false;
				}
			} else {
				return false;
			}
		}
	}

	public function checkReturnVoucherRequested($bol_id) {
		$this->error = '';
		$voucherRequested = false;

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$shpment_information = $this->speedyREST->shipmentInformation(array(
						'shipment_ids' => array($bol_id)
					));

					if (!empty($shpment_information[0]['service']['additionalServices']['returns']['returnVoucher'])) {
						$voucherRequested = true;
					}

					$this->error = $this->speedyREST->getErrorsAsString();
				} else {
					$pickingExtendedInfo = $this->ePSFacade->getPickingExtendedInfo((float)$bol_id);

					if (!is_null($pickingExtendedInfo->getReturnVoucher()) && ($pickingExtendedInfo->getReturnVoucher() instanceof ResultReturnVoucher)) {
						$voucherRequested = true;
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: checkReturnVoucherRequested :: ' . $e->getMessage());
			}
		}

		return $voucherRequested;
	}

	public function track($bol_id) {
		$this->error = '';

		if (isset($this->resultLogin)) {
			try {
				if (self::NEW_API) {
					$data = array(
						'parcels' => array(
							array('parcel.id' => $bol_id),
						),
						'last_opeartion_only' => true
					);

					$pickingExtendedInfo = $this->speedyREST->track($data);

					if (!empty($pickingExtendedInfo[0])) {
						$operation = $pickingExtendedInfo[0]['operations'][0];

						return array(
							'dateTime'      => !empty($operation['dateTime'])      ? $operation['dateTime']      : '',
							'operationCode' => !empty($operation['operationCode']) ? $operation['operationCode'] : '',
							'place'         => !empty($operation['place'])         ? $operation['place']         : '',
							'description'   => !empty($operation['description'])   ? $operation['description']   : '',
							'comment'       => !empty($operation['comment'])       ? $operation['comment']       : '',
							'consignee'     => !empty($operation['consignee'])     ? $operation['consignee']     : '',
						);
					}
				} else {
					$pickingExtendedInfo = $this->ePSFacade->trackPickingEx($bol_id, null, true);

					if (!empty($pickingExtendedInfo[0])) {
						$operation = $pickingExtendedInfo[0];

						return array(
							'dateTime'      => $operation->getMoment(),
							'operationCode' => $operation->getOperationCode(),
							'place'         => $operation->getSiteName(),
							'description'   => $operation->getOperationDescription(),
							'comment'       => $operation->getOperationComment(),
							'consignee'     => $operation->getConsignee(),
						);
					}
				}
			} catch (Exception $e) {
				$this->error = $e->getMessage();
				$this->log->write('Speedy :: track :: ' . $e->getMessage());
			}
		}
	}

	public function getServiceById($service_id, $lang = 'bg') {
		$services = array();
		if (strtolower($lang) != 'bg') {
			$lang = 'en';
		}

		try {
			$servises = $this->ePSFacade->listServices(time(), strtoupper($lang));

			foreach ($servises as $servise) {
				if ($servise->getTypeId() == $service_id) {
					return $servise;
				}
			}
		} catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->log->write('Speedy :: getServiceById :: ' . $e->getMessage());
		}
	}

	public function getReturnPackageServiceTypeId($picking) {
		$this->error = '';
		$services = array();
		$returnVoucherServiceTypeId = null;

		try {
			if ($this->config->get('shipping_speedy_from_office') && $this->config->get('shipping_speedy_office_id')) {
				$senderOfficeId = $this->config->get('shipping_speedy_office_id');
				$senderSiteId = null;
			} else {
				if (self::NEW_API) {
					if (method_exists($picking, 'getSenderId')) {
						$senderData = $this->speedyREST->getClient($picking->getSenderId());
						$senderSiteId = $senderData['address']['siteId'];
					} elseif (method_exists($picking, 'getSender')) {
						$senderData = $this->speedyREST->getClient($picking->getSender()->getClientId());
						$senderSiteId = $senderData['address']['siteId'];
					}
				} else {
					if (method_exists($picking, 'getSenderId')) {
						$senderData = $this->ePSFacade->getClientById($picking->getSenderId());
						$senderSiteId = $senderData->getAddress()->getSiteId();
					} elseif (method_exists($picking, 'getSender')) {
						$senderData = $this->ePSFacade->getClientById($picking->getSender()->getClientId());
						$senderSiteId = $senderData->getAddress()->getSiteId();
					}
				}

				$senderOfficeId = null;
			}

			if ($picking->getOfficeToBeCalledId()) {
				$receiverOfficeId = $picking->getOfficeToBeCalledId();
				$receiverSiteId = null;
			} else {
				$receiverOfficeId = null;

				if (method_exists($picking, 'getReceiver')) {
					$receiverSiteId = $picking->getReceiver()->getAddress()->getSiteId();
				} elseif (method_exists($picking, 'getReceiverSiteId')) {
					$receiverSiteId = $picking->getReceiverSiteId();
				} else {
					$receiverSiteId = null;
				}
			}

			// Reverse sender and receiver data
			$listServices = $this->ePSFacade->listServicesForSites(time(), $receiverSiteId, $senderSiteId, null, null, null, null, null, null, null, $receiverOfficeId, $senderOfficeId);

			foreach ($listServices as $listService) {
				$services[] = $listService->getTypeId();
			}

			if (in_array($this->config->get('shipping_speedy_return_package_city_service_id'), $services)) {
				$returnVoucherServiceTypeId = $this->config->get('shipping_speedy_return_package_city_service_id');
			} elseif (in_array($this->config->get('shipping_speedy_return_package_intercity_service_id'), $services)) {
				$returnVoucherServiceTypeId = $this->config->get('shipping_speedy_return_package_intercity_service_id');
			}

		} catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->log->write('Speedy :: getReturnPackageServiceTypeId :: ' . $e->getMessage());
		}

		return $returnVoucherServiceTypeId;
	}

	public function getReturnVoucherServiceTypeId($picking) {
		$this->error = '';
		$services = array();
		$returnVoucherServiceTypeId = null;

		$sender = $picking->getSender();
		$receiver = $picking->getReceiver();

		try {
			if ($this->config->get('shipping_speedy_from_office') && $this->config->get('shipping_speedy_office_id')) {
				$senderSiteId = null;
				$senderOfficeId = $this->config->get('shipping_speedy_office_id');
			} else {
				if (self::NEW_API) {
					$senderData = $this->speedyREST->getClient($sender->getClientId());
					$senderSiteId = $senderData['address']['siteId'];
				} else {
					$senderData = $this->ePSFacade->getClientById($picking->getSender()->getClientId());
					$senderSiteId = $senderData->getAddress()->getSiteId();
				}

				$senderOfficeId = null;
			}

			if ($receiver->getAddress()) {
				$receiverSiteId = $receiver->getAddress()->getSiteId();
				$receiverOfficeId = null;
			} else {
				$receiverSiteId = null;
				$receiverOfficeId = $picking->getOfficeToBeCalledId();
			}

			// Reverse sender and receiver data
			$listServices = $this->ePSFacade->listServicesForSites(time(), $receiverSiteId, $senderSiteId, null, null, null, null, null, null, null, $receiverOfficeId, $senderOfficeId);

			foreach ($listServices as $listService) {
				$services[] = $listService->getTypeId();
			}

			if (in_array($this->config->get('shipping_speedy_return_voucher_city_service_id'), $services)) {
				$returnVoucherServiceTypeId = $this->config->get('shipping_speedy_return_voucher_city_service_id');
			} elseif (in_array($this->config->get('shipping_speedy_return_voucher_intercity_service_id'), $services)) {
				$returnVoucherServiceTypeId = $this->config->get('shipping_speedy_return_voucher_intercity_service_id');
			}

		} catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->log->write('Speedy :: getReturnVoucherServiceTypeId :: ' . $e->getMessage());
		}

		return $returnVoucherServiceTypeId;
	}

	public function getPayerType($order_id, $shippingCost) {
		$payerType = null;

		$db = $this->registry->get('db');
		$session = $this->registry->get('session');
		$query = $db->query("SELECT data FROM " . DB_PREFIX . "speedy_order WHERE order_id = '" . (int) $order_id . "'");

		$data = unserialize($query->row['data']);

		if ($data['price_gen_method'] && !$session->data['is_speedy_bol_recalculated']) {
			if ($data['price_gen_method'] == 'fixed' || $data['price_gen_method'] == 'free') {
				if ($data['price_gen_method'] == 'free') {
					$delta = 0.0001;

					if (abs($data['shipping_method_cost'] - 0.0000) < $delta) {
						$payerType = ParamCalculation::PAYER_TYPE_SENDER;
					} else {
						$payerType = ParamCalculation::PAYER_TYPE_RECEIVER;
					}
				} else {
					$payerType = ParamCalculation::PAYER_TYPE_SENDER;
				}
			} else {
				$payerType = ParamCalculation::PAYER_TYPE_RECEIVER;
			}
		} elseif ($data['price_gen_method'] && $session->data['is_speedy_bol_recalculated']) {
			if ($this->config->get('shipping_speedy_pricing') == 'free' || $this->config->get('shipping_speedy_pricing') == 'fixed' || $this->config->get('shipping_speedy_pricing') == 'table_rate') {
				if ($this->config->get('shipping_speedy_pricing') == 'free') {
					$delta = 0.0001;

					if (($shippingCost - 0.0000) < $delta) {
						$payerType = ParamCalculation::PAYER_TYPE_SENDER;
					} else {
						$payerType = ParamCalculation::PAYER_TYPE_RECEIVER;
					}
				} else {
					$payerType = ParamCalculation::PAYER_TYPE_SENDER;
				}
			} else {
				$payerType = ParamCalculation::PAYER_TYPE_RECEIVER;
			}
		} elseif (!$data['price_gen_method']) {
			if ($this->config->get('shipping_speedy_pricing') == 'free' || $this->config->get('shipping_speedy_pricing') == 'fixed' || $this->config->get('shipping_speedy_pricing') == 'table_rate') {
				$payerType = ParamCalculation::PAYER_TYPE_SENDER;
			} else {
				$payerType = ParamCalculation::PAYER_TYPE_RECEIVER;
			}
		}

		$allowed_pricings = array(
			'calculator',
			'free',
			'calculator_fixed'
		);

		if ($this->config->get('shipping_speedy_invoice_courier_sevice_as_text') && 
			in_array($this->config->get('shipping_speedy_pricing'), $allowed_pricings) &&
			(isset($data['cod']) && !$data['cod'])
		) {
			$payerType = ParamCalculation::PAYER_TYPE_SENDER;
		}

		// International Shipping
		if (isset($data['abroad']) && $data['abroad']) {
			$payerType = ParamCalculation::PAYER_TYPE_SENDER;
		}

		return $payerType;
	}

	public function validateAddress($address) {
		if (self::NEW_API) {
			$data = array(
				'country_id'       => Helper::get($address, 'country_id'),
				'state_id'         => Helper::get($address, 'state_id'),
				'site_id'          => Helper::get($address, 'city_id'),
				'site_type'        => '',
				'site_name'        => Helper::get($address, 'city'),
				'post_code'        => Helper::get($address, 'postcode'),
				'street_id'        => Helper::get($address, 'street_id'),
				'street_type'      => '',
				'street_name'      => Helper::get($address, 'street'),
				'street_no'        => Helper::get($address, 'street_no'),
				'complex_id'       => Helper::get($address, 'quarter_id'),
				'complex_type'     => '',
				'complex_name'     => Helper::get($address, 'quarter'),
				'block_no'         => Helper::get($address, 'block_no'),
				'entrance_no'      => Helper::get($address, 'entrance_no'),
				'floor_no'         => Helper::get($address, 'floor_no'),
				'apartment_no'     => Helper::get($address, 'apartment_no'),
				'poi_id'           => '',
				'address_note'     => Helper::get($address, 'note'),
				'address_line_2'   => Helper::get($address, 'address_2'),
				'x'                => '',
				'y'                => '',
			);

			if (!empty($address['address_1'])) {
				$data['address_line_1'] = $address['address_1'];
			} elseif (!empty($address['note'])) {
				$data['address_line_1'] = $data['note'];
			}
		} else {
			$paramAddress = new ParamAddress();

			$paramAddress->setSiteId(trim($address['city_id']));
			if (!isset($address['city_id']) || !$address['city_id']) {
				$paramAddress->setSiteName(trim($address['city']));
			}
			$paramAddress->setPostCode(trim($address['postcode']));
			$paramAddress->setCountryId(trim($address['country_id']));
			$paramAddress->setStateId(trim($address['state_id']));

			if (!empty($address['quarter'])) {
				$paramAddress->setQuarterName(trim($address['quarter']));
			}

			if (!empty($address['quarter_id'])) {
				$paramAddress->setQuarterId(trim($address['quarter_id']));
			}

			if (!empty($address['street'])) {
				$paramAddress->setStreetName(trim($address['street']));
			}

			if (!empty($address['street_id'])) {
				$paramAddress->setStreetId(trim($address['street_id']));
			}

			if (!empty($address['street_no'])) {
				$paramAddress->setStreetNo(trim($address['street_no']));
			}

			if (!empty($address['block_no'])) {
				$paramAddress->setBlockNo(trim($address['block_no']));
			}

			if (!empty($address['entrance_no'])) {
				$paramAddress->setEntranceNo(trim($address['entrance_no']));
			}

			if (!empty($address['floor_no'])) {
				$paramAddress->setFloorNo(trim($address['floor_no']));
			}

			if (!empty($address['apartment_no'])) {
				$paramAddress->setApartmentNo(trim($address['apartment_no']));
			}

			if (!empty($address['note'])) {
				$paramAddress->setAddressNote(trim($address['note']));
			}

			if (!empty($address['address_1'])) {
				$paramAddress->setFrnAddressLine1(trim($address['address_1']));
			} elseif (!empty($address['note'])) {
				$paramAddress->setFrnAddressLine1(trim($address['note']));
			}

			if (!empty($address['address_2'])) {
				$paramAddress->setFrnAddressLine2(trim($address['address_2']));
			}
		}

		try {
			if (self::NEW_API) {
				$valid = $this->speedyREST->validateAddress($data);
			} else {
				$valid = $this->ePSFacade->validateAddress($paramAddress, 0);
			}
		} catch (Exception $e) {
			$valid = $e->getMessage();
		}

		return $valid;
	}
}