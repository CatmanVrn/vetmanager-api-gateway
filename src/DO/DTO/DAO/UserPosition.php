<?php

declare(strict_types=1);

namespace VetmanagerApiGateway\DO\DTO\DAO;

use DateInterval;
use VetmanagerApiGateway\ApiGateway;
use VetmanagerApiGateway\DO\DateIntervalContainer;
use VetmanagerApiGateway\DO\DTO\AbstractDTO;
use VetmanagerApiGateway\DO\DTO\DAO\Interface\AllGetRequestsInterface;
use VetmanagerApiGateway\DO\DTO\DAO\Trait\AllGetRequestsTrait;
use VetmanagerApiGateway\DO\DTO\DAO\Trait\BasicDAOTrait;
use VetmanagerApiGateway\DO\Enum\ApiRoute;
use VetmanagerApiGateway\DO\IntContainer;
use VetmanagerApiGateway\DO\StringContainer;
use VetmanagerApiGateway\Exception\VetmanagerApiGatewayException;

final class UserPosition extends AbstractDTO implements AllGetRequestsInterface
{
    use BasicDAOTrait;
    use AllGetRequestsTrait;

    public int $id;
    public string $title;
    /** Default: '00:30:00'. Type in DB: 'time'. Null if '00:00:00' */
    public ?DateInterval $admissionLength;

    /** @param array{
     *     "id": string,
     *     "title": string,
     *     "admission_length": string,
     * } $originalData
     * @throws VetmanagerApiGatewayException
     */
    public function __construct(protected ApiGateway $apiGateway, array $originalData)
    {
        parent::__construct($apiGateway, $originalData);

        $this->id = IntContainer::fromStringOrNull($originalData['id'])->positiveInt;
        $this->title = StringContainer::fromStringOrNull($originalData['title'])->string;
        $this->admissionLength = DateIntervalContainer::fromStringHMS($originalData['admission_length'])->dateIntervalOrNull;
    }

    /** @return ApiRoute::UserPosition */
    public static function getApiModel(): ApiRoute
    {
        return ApiRoute::UserPosition;
    }
}
