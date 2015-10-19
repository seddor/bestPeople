<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraint as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity()
 */
class Image extends BaseEntity
{
    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\Image(
     *      maxSize="5M"
//     *      mimeTypes='image/gif'
//     *      mimeTypes='image/png'
//     *      mimeTypes='image/jpeg'
//     *      mimeTypes='image/pjpeg'
     *      mimeTypes=array('image/gif',image/png',image/jpeg,image/pjpeg)
     *
     *
     *
     */
    private $file;

    /**
     * @param UploadedFile|null $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    private function getFile() {
        return $this->file;
    }

    private function getUploadDir() {
        return 'images/users';
    }

    private function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    public function getAbsolutePath() {
        return null === $this->path ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPatch() {
        return null === $this->path ? null
            : $this->getUploadRootDir().'/'.$this->getUploadDir();
    }

    public function upload($directory) {
        if (null === $this->getFile()) {
            return;
        }
        $this->getFile()->move(
            $this->getUploadRootDir().'/'.$directory,
            $this->getFile()->getClientOriginalName()
        );

        $this->path = $this->getFile()->getClientOriginalName();

        $this->file = null;
    }


}