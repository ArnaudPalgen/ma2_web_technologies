<?php


namespace App\Service;


use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PubChem
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getHazardsByNcas($ncas)
    {
        return $this->getHazards($this->getCid($ncas));
    }

    public function getHazards($cid)
    {
        if ($cid == null) {
            return null;
        }

        try {

            $response = $this->client->request(
                'GET',
                'https://pubchem.ncbi.nlm.nih.gov/rest/pug_view/data/compound/' . $cid . '/JSON', [
                'query' => [
                    'heading' => 'Chemical Safety',
                ],
            ]);

            return array_map(function ($e) {
                $s = explode("/", $e['URL']);
                return [
                    'symbol' => $e['URL'],
                    'code' => explode(".", end($s))[0],
                    'text' => $e['Extra']
                ];
            }, $response->toArray()['Record']['Section'][0]['Information'][0]['Value']['StringWithMarkup'][0]['Markup']);

        } catch (Exception $e) {
            return null;
        }
    }

    public function getCid($ncas)
    {
        if ($ncas == null) {
            return null;
        }

        try {

            $response = $this->client->request(
                'GET',
                'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/name/' . $ncas . '/cids/JSON'
            );

            return $response->toArray()['IdentifierList']['CID'][0];
        } catch (Exception $e) {
            return null;
        }
    }


}