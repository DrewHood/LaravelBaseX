<?php

namespace Theplatform\BaseX;

use Exception;
use Httpful;

class BaseX
{
    private $baseXConf;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->baseXConf = config('baseX.baseX');
    }

    /**
     * Creates a BaseX database with the provided content.
     *
     * @param String $databaseTitle
     * @param String $content
     * @param String[] $options
     */
    public function createDatabase($databaseTitle, $content, $options = [])
    {
        // BaseX doesn't like periods
        $databaseTitle = str_replace(".", "", $databaseTitle);

        // Construct the URL.
        $url = $this->baseXConf['basePath'].$databaseTitle.$this->baseXConf['options'];

        try {

            // Construct the POST request.
            $response = Httpful\Request::put($url)
                ->sendsXml()
                ->authenticateWith($this->baseXConf['user'], $this->baseXConf['password'])
                ->body($content)
                ->send();

            if ($response->code > 201) {
                throw new Exception("Exception creating BaseX database. Received BaseX response: ".$response->raw_body, $response->code);
            } 
            
        } catch (Exception $e) {
            throw new Exception("Exception creating BaseX database: ".$e->getMessage(), 503);
            
        }
    }

    /**
     * Perform database query
     * 
     * @param String $databaseTitle
     * @param String $query
     * 
     * @return SimpleXMLElement
     */
    public function queryDatabase($databaseTitle, $query)
    {
        // BaseX doesn't like periods
        $databaseTitle = str_replace(".", "", $databaseTitle);

        $url = $this->baseXConf['basePath'].$databaseTitle;
        $baseXResponse = Httpful\Request::post($url)
            ->body('<query xmlns="http://basex.org/rest"><text><![CDATA['.$query.']]></text></query>')
            ->sendsXml()
            ->authenticateWith($this->baseXConf['user'], $this->baseXConf['password'])
            ->parseWith(function($body) {
                return "<xml>".$body."</xml>";
            })
            ->send();

        // Were we successful?
        if ($baseXResponse->code > 200) {
            throw new Exception("Exception during BaseX query: ".$url."\n".$baseXResponse->raw_body, $baseXResponse->code);
            
        }

        return simplexml_load_string($baseXResponse->body);
    }

    /**
     * Drops a BaseX database.
     * 
     * @param String $databaseTitle
     */
    public function dropDatabase($databaseTitle)
    {
        // BaseX doesn't like periods
        $databaseTitle = str_replace(".", "", $databaseTitle);

        // Construct the URL.
        $url = $this->baseXConf['basePath'].$databaseTitle;

        // Send delete request. 
        $response = Httpful\Request::delete($url)
            ->authenticateWith($this->baseXConf['user'], $this->baseXConf['password'])
            ->send();

        // Check the status
        if ($response->code != 200) {
            // Throw an exception
            throw new Exception("Exception deleting BaseX database. Received BaseX response: ".$response->raw_body, $response->code);
        }
    }
}