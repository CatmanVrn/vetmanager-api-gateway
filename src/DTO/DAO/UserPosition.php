<?php

declare(strict_types=1);

namespace VetmanagerApiGateway\DTO\DAO;

use DateInterval;
use Exception;
use VetmanagerApiGateway\ApiGateway;
use VetmanagerApiGateway\DTO\AbstractDTO;
use VetmanagerApiGateway\DTO\DAO\Interface\AllGetRequestsInterface;
use VetmanagerApiGateway\DTO\DAO\Trait\AllGetRequestsTrait;
use VetmanagerApiGateway\DTO\DAO\Trait\BasicDAOTrait;
use VetmanagerApiGateway\DTO\Enum\ApiRoute;
use VetmanagerApiGateway\Exception\VetmanagerApiGatewayException;

class UserPosition extends AbstractDTO implements AllGetRequestsInterface
{
    use BasicDAOTrait, AllGetRequestsTrait;

    public int $id;
    public string $title;
    /** Default: '00:30:00'. Type in DB: 'time' */
    public DateInterval $admissionLength;

    /** @var array{
     *     "id": string,
     *     "title": string,
     *     "admission_length": string,
     * } $originalData
     */
    protected readonly array $originalData;

    /** @throws VetmanagerApiGatewayException
     * @throws Exception
     */
    public function __construct(protected ApiGateway $apiGateway, array $originalData)
    {
        parent::__construct($apiGateway, $originalData);

        $this->id = (int)$this->originalData['id'];
        $this->title = (string)$this->originalData['title'];
        list($hours, $minutes, $seconds) = sscanf($this->originalData['admission_length'], '%d:%d:%d');
        $this->admissionLength = new DateInterval(sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds));
    }


    public static function getApiModel(): ApiRoute
    {
        return ApiRoute::UserPosition;
    }
}