<?php declare(strict_types=1);
namespace Uma\Domain\Model;

use DateTime;
use DateTimeZone;
use JsonSerializable;
use Uma\Domain\Exceptions\DomainException;

/**
 * Details of an actor.
 *
 * @package Uma\Domain\Model
 */
class Actor extends PersistentId implements JsonSerializable
{
    /** @var string */
    private $name;
    /** @var DateTime */
    private $birth;
    /** @var string|null */
    private $bio;
    /** @var string|null */
    private $image;

    /**
     * Actor constructor.
     *
     * @param string $name
     * @param DateTime $birth
     */
    public function __construct(string $name, DateTime $birth)
    {
        $this->setName($name);
        $this->setBirth($birth);
    }

    /**
     * The name of the actor.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * The date of birth of the actor.
     *
     * @return DateTime
     */
    public function getBirth(): DateTime
    {
        return $this->birth;
    }

    /**
     * The biography of the actor.
     *
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * The image of the actor.
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Validates that the name is between 1 and 255 characters.
     *
     * @param string $name
     */
    public function setName(string $name)
    {
        $length = strlen($name);

        // You could also test that the name is at least two words, each word has x characters
        if ($length < 1 || $length > 255)
        {
            throw new DomainException('Actor name invalid');
        }

        $this->name = $name;
    }

    /**
     * Validates that the birth is in the past. Date is in UTC.
     *
     * @param DateTime $birth
     */
    public function setBirth(DateTime $birth)
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));

        if ($birth > $now)
        {
            throw new DomainException('Birth must be in the past');
        }

        $this->birth = $birth;
    }

    /**
     * Provides 3000 characters for biography.
     *
     * @param string|null $bio
     */
    public function setBio(?string $bio)
    {
        if ($bio !== null && strlen($bio) > 3000)
        {
            throw new DomainException('Actor biography too long');
        }

        $this->bio = $bio;
    }

    /**
     * Allows up to ~500kB images.
     *
     * @param string|null $image
     */
    public function setImage(?string $image)
    {
        // Could run the image through an image normalisation service
        if ($image !== null && strlen($image) > 512000)
        {
            throw new DomainException('Actor image too large');
        }

        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'name'  => $this->name,
            'birth' => $this->birth->format(DateTime::ATOM),
            'bio'   => $this->bio,
            'image' => $this->image,
        ];
    }
}
