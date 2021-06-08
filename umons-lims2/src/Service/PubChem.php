<?php


namespace App\Service;


use mysql_xdevapi\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PubChem
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public  function getHazards($cid) {
        $response = $this->client->request(
            'GET',
            'https://pubchem.ncbi.nlm.nih.gov/rest/pug_view/data/compound/'.$cid.'/JSON', [
                'query' => [
                    'heading' => 'Chemical Safety',
                ],
        ]);

        //        $contentType = $response->getHeaders()['content-type'][0];

//        $content = $response->getContent()->toArray();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'

        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]


        try {
            $hazards = array_map(function ($e) {
                $s = explode("/",$e['URL']);
                return [
                    'symbol' => $e['URL'],
                    'code'=>explode(".", end($s))[0],
                    'text' => $e['Extra']
                ];
            },$response->toArray()['Record']['Section'][0]['Information'][0]['Value']['StringWithMarkup'][0]['Markup']);
            return [
                'status' => $response->getStatusCode(),
                'hazards' => $hazards
            ];
        } catch ( Exception $e) {
            return [
                'status' => 500,
                'hazards' => null
            ];
        }
    }


    public  function getCid($ncas) {

        $response = $this->client->request(
            'GET',
            'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/name/'.$ncas.'/cids/JSON'
        );

        try {
            $cid = $response->toArray()['IdentifierList']['CID'][0];
            return [
                'status' => $response->getStatusCode(),
                'cid' => $cid
            ];
        } catch ( Exception $e) {
            return [
                'status' => 500,
                'cid' => null
            ];
        }
    }



}