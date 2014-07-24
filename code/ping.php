<?php
/*
* With the Ping module you can notify web services like http://rpc.weblogs.com/RPC2
* of pages that have been updated.
*
* Add the list of services to the settings area of the website, separated by a line break
* @package ping
**/

class Ping extends SiteTreeExtension {
 
    private $pingUrls = "http://rpc.weblogs.com/RPC2";
    
    /**
    * Initialize function
    */
    public function init()
    {
		parent::init();
    }
    
    /**
    * Set the $pingUrls variable
    */
    public function setPingUrls($pingUrls) 
    {
        $this->pingUrls = $pingUrls;
    }
    
    /**
    * Get the $pingUrls variable
    */
    public function getPingUrls() 
    {
        return $this->pingUrls;
    }
 
    /**
    * After publishing or updating a page, this function POSTS to let 
    * blog services know your website has been updated.
    * Uses @webPingXML
    * Uses @doWebPing
    */
    public function onAfterPublish($original)
    {
        // Get the XML to post
        $postString     = $this->webPingXML($original);
        // Set the site configuration settings to $config
        $config         = SiteConfig::current_site_config();
        
        // Check if the user has entered any urls to ping
        if( !empty($config->pingUrls) ) {
            $this->setPingUrls($config->pingUrls);
        }
        
        // Convert new lines to br tags and then replace any <br> tags with xhtml <br /> tags
        $pingUrls = str_replace("<br>", "<br />", nl2br($this->getPingUrls()));
        // Explode the $pingUrls into an array, removing any <br /> tags
        $pingUrls = explode("<br />", $pingUrls);
        
        // Check to make sure the $pingUrls have some value
        if( !empty($pingUrls) ) {
            // Check if there is more than one url to ping
            if( is_array($pingUrls) ) {
                // Loop through the urls
                foreach($pingUrls as $url) {
                    // Post the XML to the url
                    $this->doWebPing($url, $postString);
                }
            } else {
                // Post the XML to the url
                $this->doWebPing($pingUrls, $postString);
            }
        }
    
		parent::onAfterPublish($original);
    }
    
    /**
    * Returns the XML for a web ping.
    */
    public function webPingXML($original) 
    {
        $postString   = '<?xml version="1.0"?>'.
                        '<methodCall>'.
                        ' <methodName>weblogUpdates.ping</methodName>'.
                        '  <params>'.
                        '   <param>'.
                        '    <value>'.$original->Title.'</value>'.
                        '   </param>'.
                        '   <param>'.
                        '    <value>'.Director::absoluteBaseURL().'</value>'.
                        '   </param>'.
                        ' </params>'.
                        '</methodCall>';
        return $postString;
    }
    
    /**
    * Sends a POST to the pingUrl provided.
    */
    public function doWebPing($pingUrl, $postString)
    {
        // Initialize CURL with the URL provided.
        $curl_connection = curl_init($pingUrl);

        // Set the time out for the CURL
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        // Set user agent string for the CURL
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "PHP");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);

        // Set the fields for the CURL POST
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $postString);

        // Execute the CURL POST
        $result = curl_exec($curl_connection);

        // Close the connection
        curl_close($curl_connection);
    }
    
}
