<?php 
namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class ImageOptimizer
{
    private $slugger;
    private $params;
    private $serializer;
    private $photoDir;
    private $projectDir;
    private $imagine;
    private $cloudinary;
    private $uploadApi;

    public function __construct(
        SluggerInterface $slugger,
        ContainerBagInterface $params,
        SerializerInterface $serializer,
        )
        {      
            $this->slugger = $slugger;
            $this->params = $params;
            $this->serializer = $serializer;
            $this->photoDir =  $this->params->get('app.imgDir');
            $this->projectDir =  $this->params->get('app.projectDir');
            $this->imagine = new Imagine();
            $this->uploadApi = Configuration::instance();
            $this->uploadApi->cloud->cloudName = $_ENV['CLOUD_NAME'];
            $this->uploadApi->cloud->apiKey = $_ENV['CLOUD_API_KEY'];
            $this->uploadApi->cloud->apiSecret = $_ENV['CLOUD_API_SECRET'];
            $this->uploadApi->url->secure = true;
            $this->uploadApi = new UploadApi();
    }

    public function setPicture( $brochureFile, $slug ): void
    {   
        // Save Local File
        $img = $this->imagine->open($brochureFile)
            ->thumbnail(new Box(1000, 1000))
            ->save($this->photoDir.$slug.'.webp', ['webp_quality' => 80]);
        $heightSmall = $img->getSize()->getHeight();
        $widthSmall = $img->getSize()->getWidth();

        $newForm = '{"path": "'.$this->projectDir.$this->photoDir.$slug.'.webp", "width": '.$widthSmall.', "height": '.$heightSmall.'}';

        // $post->$setName($this->serializer->decode($newForm, 'json'));

        // Save Cloudinary File

        $this->uploadApi->upload($this->photoDir.$slug.'.webp', array(
            "public_id" => $slug,
            "folder" => "unetaupechezvous",
            "overwrite" => true,
            "resource_type" => "auto",
            "quality" => "auto",
            "fetch_format" => "webp",
            "width" => 1000,
            "height" => 1000,
            "crop" => "limit",
            "secure" => true));
    }

    public function setThumbnail( $brochureFile, $post, $setName, $slug ): void
    {   
        $newForm = $this->projectDir.$this->photoDir.$slug.'-miniature.webp';
        $post->$setName($newForm);
        list($iwidth, $iheight) = getimagesize($brochureFile);
        $ratio = $iwidth / $iheight;
        $width =  250;
        $height = 250;
        if ($width / $height < $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }
        $thumbnail = $this->imagine->open($brochureFile)
        ->thumbnail(new Box($width, $height))
        ->save($this->photoDir.$slug.'-miniature.webp', ['webp_quality' => 80]);


        
    }

    public function setThumbnailJpg( $brochureFile, $post, $setName, $slug ): void
    {   
        $newForm = $this->projectDir.$this->photoDir.$slug.'.png';
        $post->$setName($newForm);
        list($iwidth, $iheight) = getimagesize($brochureFile);
        $ratio = $iwidth / $iheight;
        $width =  250;
        $height = 250;
        if ($width / $height < $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }
        
        #dd($width, $height);
        
        $thumbnail = $this->imagine->open($brochureFile)
        ->thumbnail(new Box($width, $height), 'inset')
        ->save($this->photoDir.$slug.'.png', ['png_quality' => 80]);
        
    }
        
}



