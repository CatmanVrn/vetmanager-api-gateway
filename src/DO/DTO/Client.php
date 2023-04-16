<?php

declare(strict_types=1);

namespace VetmanagerApiGateway\DO\DTO;

use DateTime;
use Otis22\VetmanagerRestApi\Query\Builder;
use VetmanagerApiGateway\ApiGateway;
use VetmanagerApiGateway\DO\DateTimeContainer;
use VetmanagerApiGateway\DO\Enum;
use VetmanagerApiGateway\DO\Enum\ApiRoute;
use VetmanagerApiGateway\DO\Enum\Client\Status;
use VetmanagerApiGateway\Exception\VetmanagerApiGatewayException;

/**
 * @property-read DAO\Client $self
 * @property-read DAO\MedicalCardsByClient[] $medcards
 * @property-read DAO\Pet[] $petsAlive
 * @property-read ?DAO\City $city
 * @property-read ?DAO\Street $street
 */
class Client extends AbstractDTO
{
    public int $id;
    public string $address;
    public string $homePhone;
    public string $workPhone;
    public string $note;
    public ?int $typeId;
    public ?int $howFind;
    /** Default: '0.0000000000' */
    public float $balance;
    /** Default: '' */
    public string $email;
    public string $cityTitle;
    public ?int $cityId;
    /** В БД бывает дефолтное значение: '0000-00-00 00:00:00' - переводится в null */
    public ?DateTime $dateRegister;
    public string $cellPhone;
    public string $zip;
    public ?string $registrationIndex;
    /** Default: 0 */
    public bool $isVip;
    public string $lastName;
    public string $firstName;
    public string $middleName;
    /** Default: Active */
    public Status $status;
    /** Default: 0 */
    public int $discount;
    public string $passportSeries;
    public string $labNumber;
    /** Default: 0 */
    public int $streetId;
    /** Default: '' */
    public string $apartment;
    /** Default: 0 */
    public bool $isUnsubscribed;
    /** Default: 0 */
    public bool $isBlacklisted;
    /** В БД бывает дефолтное значение: '0000-00-00 00:00:00' - переводится в null */
    public ?DateTime $lastVisitDate;
    /** Default: '' */
    public string $numberOfJournal;
    public string $phonePrefix;
    /** @var array{
     *      "id": string,
     *      "address": string,
     *      "home_phone": string,
     *      "work_phone": string,
     *      "note": string,
     *      "type_id": ?string,
     *      "how_find": ?string,
     *      "balance": string,
     *      "email": string,
     *      "city": string,
     *      "city_id": ?string,
     *      "date_register": string,
     *      "cell_phone": string,
     *      "zip": string,
     *      "registration_index": ?string,
     *      "vip": string,
     *      "last_name": string,
     *      "first_name": string,
     *      "middle_name": string,
     *      "status": string,
     *      "discount": string,
     *      "passport_series": string,
     *      "lab_number": string,
     *      "street_id": string,
     *      "apartment": string,
     *      "unsubscribe": string,
     *      "in_blacklist": string,
     *      "last_visit_date": string,
     *      "number_of_journal": string,
     *      "phone_prefix": ?string
     * } $originalData
     */
    protected readonly array $originalData;

    /** @throws VetmanagerApiGatewayException */
    public function __construct(protected ApiGateway $apiGateway, array $originalData)
    {
        parent::__construct($apiGateway, $originalData);

        $this->id = (int)$this->originalData['id'];
        $this->address = (string)$this->originalData['address'];
        $this->homePhone = (string)$this->originalData['home_phone'];
        $this->workPhone = (string)$this->originalData['work_phone'];
        $this->note = (string)$this->originalData['note'];
        $this->typeId = $this->originalData['type_id'] ? (int)$this->originalData['type_id'] : null;
        $this->howFind = $this->originalData['how_find'] ? (int)$this->originalData['how_find'] : null;
        $this->balance = (float)$this->originalData['balance'];
        $this->email = (string)$this->originalData['email'];
        $this->cityTitle = (string)$this->originalData['city'];
        $this->cityId = $this->originalData['city_id'] ? (int)$this->originalData['city_id'] : null;
        $this->dateRegister = (DateTimeContainer::fromFullDateTimeString($this->originalData['date_register']))->dateTimeNullable;
        $this->cellPhone = (string)$this->originalData['cell_phone'];
        $this->zip = (string)$this->originalData['zip'];
        $registrationIndex = $this->originalData['registration_index'];
        $this->registrationIndex = !is_null($registrationIndex) ? (string)$registrationIndex : null;
        $this->isVip = (bool)$this->originalData['vip'];
        $this->lastName = (string)$this->originalData['last_name'];
        $this->firstName = (string)$this->originalData['first_name'];
        $this->middleName = (string)$this->originalData['middle_name'];
        $this->status = Status::from($this->originalData['status']);
        $this->discount = (int)$this->originalData['discount'];
        $this->passportSeries = (string)$this->originalData['passport_series'];
        $this->labNumber = (string)$this->originalData['lab_number'];
        $this->streetId = (int)$this->originalData['street_id'];
        $this->apartment = (string)$this->originalData['apartment'];
        $this->isUnsubscribed = (bool)$this->originalData['unsubscribe'];
        $this->isBlacklisted = (bool)$this->originalData['in_blacklist'];
        $this->lastVisitDate = (DateTimeContainer::fromFullDateTimeString($this->originalData['last_visit_date']))->dateTimeNullable;
        $this->numberOfJournal = (string)$this->originalData['number_of_journal'];
        $this->phonePrefix = (string)$this->originalData['phone_prefix'];
    }

    /** @throws VetmanagerApiGatewayException
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'self' => DAO\Client::getById($this->apiGateway, $this->id),
            'medcards' => $this->getMedcards(),
            'petsAlive' => $this->getPetsAlive(),
            'street' => $this->streetId ? DAO\Street::getById($this->apiGateway, $this->streetId) : null,
            'city' => $this->cityId ? DAO\City::getById($this->apiGateway, $this->cityId) : null,
            default => $this->$name
        };
    }

    /** @return DAO\MedicalCardsByClient[]
     * @throws VetmanagerApiGatewayException
     */
    private function getMedcards(): array
    {
        return DAO\MedicalCardsByClient::getByClientId($this->apiGateway, $this->id);
    }

    /** @return DAO\Pet[]
     * @throws VetmanagerApiGatewayException
     */
    private function getPetsAlive(): array
    {
        $pets = $this->apiGateway->getWithQueryBuilder(
            ApiRoute::Pet,
            (new Builder())
                ->where('owner_id', (string)$this->id)
                ->where('status', Enum\Pet\Status::Alive->value)
        );

        return DAO\Pet::fromResponse($this->apiGateway, $pets);
    }
}