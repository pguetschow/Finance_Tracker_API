<?php
declare(strict_types=1);


namespace App\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Paul Martin Gütschow <guetschow@esonewmedia.de>
 * @ORM\Entity
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

}
