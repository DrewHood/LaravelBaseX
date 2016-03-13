<?php

namespace Theplatform\BaseX;

use Exception;
use Httpful;

class BaseX
{
	private $baseXConf = config('baseX.baseX');

	/**
     * Creates a BaseX database with the provided content.
     *
     * @param String $databaseTitle
     * @param String $content
     * @param String[] $options
     */
    static public function createDatabase($databaseTitle, $content, $options = [])
    {
    	// Construct the URL.
        $url = $basexConf['basePath'].$dbTitle.$basexConf['options'];

        try {

            // Construct the POST request.
            $response = Httpful\Request::put($url)
                ->sendsXml()
                ->authenticateWith($basexConf['user'], $basexConf['password'])
                ->body($content)
                ->send();

            if ($response->code > 201) {
                throw new Exception("Exception creating BaseX database. Received BaseX response: ".$response->raw_body, $response->code);
            } 
            
        } catch (Exception $e) {
            throw new Exception("Exception creating BaseX database: ".$e->getMessage(), $e->code);
            
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
    static public function queryDatabase($databaseTitle, $query)
    {
    	$url = $baseXConf['basePath'].$databaseTitle."?query=".urlencode($query);
    	$baseXResponse = Httpful\Request::get($url)
    		->authenticateWith($baseXConf['user'], $baseXConf['password'])
    		->parseWith(function($body) {
    			return "<xml>".$body."</xml>";
    		})
    		->send();

        // Were we successful?
    	if ($baseXResponse->code > 200) {
    		throw new Exception("Exception during BaseX query: ".$baseXResponse->raw_body, $baseXResponse->code);
    		
    	}

        return $baseXResponse->body;
    }

	/**
     * Drops a BaseX database.
     * 
     * @param String $databaseTitle
     */
    static public function dropDatabase($databaseTitle)
    {
        // Construct the URL.
        $url = $basexConf['basePath'].$databaseTitle;

        // Send delete request. 
        $respnse = Httpful\Request::delete($url)
            ->authenticateWith($basexConf['user'], $basexConf['password'])
            ->send();

        // Check the status
        if ($response->code != 200) {
        	// Throw an exception
        	throw new Exception("Exception deleting BaseX database. Received BaseX response: ".$response->raw_body, $response->code);
        }
    }
}