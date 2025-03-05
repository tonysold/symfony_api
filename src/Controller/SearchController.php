<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/', name: 'search', methods: ['GET', 'POST'])]
    public function index(Request $request)
    {
        $result = null;
        $secondResult = null;

        if ($request->isMethod('POST')) {
            $query = $request->request->get('query');
            if ($query) {
                $client = HttpClient::create();
                
                // Первое подключение (Адреса)
                $response = $client->request('POST', 'http://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Token d36468430304e194ebb9915841ed5748aa52692c',
                    ],
                    'json' => [
                        'query' => $query,
                    ],
                ]);

                $result = $response->getContent();

                // Второе подключение (ИНН)
                $secondResponse = $client->request('POST', 'http://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Token d36468430304e194ebb9915841ed5748aa52692c',
                    ],
                    'json' => [
                        'query' => $query,
                    ],
                ]);

                $secondResult = $secondResponse->getContent();
            }
        }

        return $this->render('search/index.html.twig', [
            'result' => $result,
            'secondResult' => $secondResult,
        ]);
    }
}
