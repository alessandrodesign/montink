<?php

namespace App\Controllers;

use App\Services\Product\ProductService;
use CodeIgniter\HTTP\ResponseInterface;

class HomeController extends BaseController
{
    protected ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function index()
    {
        $data = [
            'title' => 'Home',
            'products' => $this->productService->getAllProducts(true),
        ];

        return view('home/index', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About Us',
        ];

        return view('home/about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Contact Us',
        ];

        return view('home/contact', $data);
    }

    public function view()
    {
        $uri = uri_string();

        if (str_starts_with($uri, 'image/')) {
            $pathImage = substr($uri, strlen('image/'));

            $pathImage = str_replace(['..', './', '\\'], '', $pathImage);

            return $this->processImage($pathImage);
        }

        return $this->response->setStatusCode(404)->setBody('Recurso não encontrado.');
    }

    private function processImage(string $pathImage): ResponseInterface
    {
        $basePath = WRITEPATH;
        $fullPath = realpath($basePath . $pathImage);

        if ($fullPath === false || !is_file($fullPath) || !str_starts_with($fullPath, realpath($basePath))) {
            return $this->response->setStatusCode(404)->setBody('Imagem não encontrada.');
        }

        $mime = mime_content_type($fullPath);
        $lastModified = filemtime($fullPath);
        $etag = md5_file($fullPath);

        // Cabeçalhos para cache (cache por 1 dia)
        $this->response->setHeader('Content-Type', $mime);
        $this->response->setHeader('Cache-Control', 'public, max-age=86400');
        $this->response->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        $this->response->setHeader('ETag', $etag);

        // Verifica se o cliente já tem a versão em cache
        $ifModifiedSince = $this->request->getHeaderLine('If-Modified-Since');
        $ifNoneMatch = $this->request->getHeaderLine('If-None-Match');

        if (($ifNoneMatch && $ifNoneMatch === $etag) ||
            ($ifModifiedSince && strtotime($ifModifiedSince) >= $lastModified)) {
            return $this->response->setStatusCode(304); // Not Modified
        }

        // Retorna o conteúdo da imagem
        $this->response->setBody(file_get_contents($fullPath));
        return $this->response;
    }
}