<?php

/*
* Adds a setting field to the settings area of the CMS
**/
class PingConfig extends DataExtension {
 
    // Adds pingUrls to the database
    public static $db = array(            
        'pingUrls' => "TEXT",          
    );
 
    /*
    * Updates the fields for the CMS
    **/
    public function updateCMSFields(FieldList $fields) {
        // Adds the field pingUrls to settings
        $fields->insertBefore(new Tab('Ping', 'Ping Settings'), 'Main');
        $fields->addFieldToTab("Root.Ping", new TextareaField("pingUrls", "Enter a list of URLs to ping. <br /><br />(Warning: adding a lot of URLs to this list will slow down saving of pages)"));
    }
}