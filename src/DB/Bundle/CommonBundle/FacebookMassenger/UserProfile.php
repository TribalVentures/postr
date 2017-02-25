<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

class UserProfile {
    protected $data = [];

    public function __construct($data) {
        $this->data = $data;
    }

    public function getFirstName() {
        return $this->data['first_name'];
    }

    public function getLastName() {
        return $this->data['last_name'];
    }

    public function getPicture()  {
        return $this->data['profile_pic'];
    }

    public function getLocale() {
        return $this->data['locale'];
    }

    public function getTimezone() {
        return $this->data['timezone'];
    }

    public function getGender() {
        return $this->data['gender'];
    }
    
    public function getProfileDetail() {
    	$userDetail = array();
    	$userDetail['firstName'] = $this->data['first_name'];
    	$userDetail['lastName'] = $this->data['last_name'];
    	$userDetail['gender'] = $this->data['gender'];
    	$userDetail['profilePic'] = $this->data['profile_pic'];
    	$userDetail['locale'] = $this->data['locale'];
    	$userDetail['timezone'] = $this->getTimezone();
    	
    	return $userDetail;
    }
}