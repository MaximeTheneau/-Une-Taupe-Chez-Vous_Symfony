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
use Symfony\Component\HttpClient\HttpClient;

class ImageOptimizer
{
    private $slugger;
    private $params;
    private $serializer;
    private $photoDir;
    private $projectDir;
    private $imagine;
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

    public function setPicture( $brochureFile, $slug, $post ): void
    {   
        // Save Local File

        $img = $this->imagine->open($brochureFile)
            ->strip()
            ->thumbnail(new Box(2560, 1200))
            ->save($this->photoDir.$slug.'.webp', ['webp_quality' => 80]);

        // Size Image
        $post->setImgWidth($img->getSize()->getWidth());
        $post->setImgHeight($img->getSize()->getHeight());

        // Delete File if exists
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET',  'https://res.cloudinary.com/dsn2zwbis/image/upload/fl_getinfo/unetaupechezvous/' . $slug . '.webp');

        if ($response->getStatusCode() === 200) {
            $this->uploadApi->destroy($slug);
        }
        
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
        
        // Delete Local File

        unlink($this->photoDir . $slug . '.webp');

    }

}



