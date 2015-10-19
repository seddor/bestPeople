<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=15,unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $gender;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $karma;

    /**
     * @ORM\OneToMany(targetEntity="Comment",mappedBy="user")
     */
    private $coments;

    /**
     * @ORM\OneToMany(targetEntity="History",mappedBy="user")
     */
    private $histories;

    /**
     * @ORM\OneToOne(targetEntity="Image",cascade={"all"},orphanRemoval=true,fetch="LAZY")
     * @ORM\JoinColumn(name="image_id",referencedColumnName="id",nullable=true)
     */
    private $avatar;

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->coments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->histories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set karma
     *
     * @param integer $karma
     *
     * @return User
     */
    public function setKarma($karma)
    {
        $this->karma = $karma;

        return $this;
    }

    /**
     * Get karma
     *
     * @return integer
     */
    public function getKarma()
    {
        return $this->karma;
    }

    /**
     * Add coment
     *
     * @param \AppBundle\Entity\Comment $coment
     *
     * @return User
     */
    public function addComent(\AppBundle\Entity\Comment $coment)
    {
        $this->coments[] = $coment;

        return $this;
    }

    /**
     * Remove coment
     *
     * @param \AppBundle\Entity\Comment $coment
     */
    public function removeComent(\AppBundle\Entity\Comment $coment)
    {
        $this->coments->removeElement($coment);
    }

    /**
     * Get coments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComents()
    {
        return $this->coments;
    }

    /**
     * Add history
     *
     * @param \AppBundle\Entity\Comment $history
     *
     * @return User
     */
    public function addHistory(\AppBundle\Entity\Comment $history)
    {
        $this->histories[] = $history;

        return $this;
    }

    /**
     * Remove history
     *
     * @param \AppBundle\Entity\Comment $history
     */
    public function removeHistory(\AppBundle\Entity\Comment $history)
    {
        $this->histories->removeElement($history);
    }

    /**
     * Get histories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistories()
    {
        return $this->histories;
    }

    /**
     * @return String
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param \AppBundle\Entity\Image $avatar
     */
    public function setAvatar($avatar = null)
    {
        $this->avatar = $avatar;
    }


}
