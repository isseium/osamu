<?php

/**
 * A PHP class that makes it easy to get your data from the Nike+ service 
 * 
 * NikePlusPHP v3.x requires PHP 5 with SimpleXML and cURL.
 * To get started you will need your Nike account information.
 * 
 * @author Charanjit Chana
 * @link http://nikeplusphp.org
 * @version 3.1.1
 */
 
class NikePlusPHP {

	/**
	 * public variables
	 */
	public $idErrorMessage = 'The login details you supplied are incorrect.', $feedErrorMessage = 'There was an error fetching the feed from the Nike servers.';

	/**
	 * private variables
	 */
	private $userId, $cookie, $body, $profile;

	/**
	 * __construct()
	 * Called when you initiate the class and keeps a cookie that allows you to keep authenticating
	 * against the Nike+ website.
	 * 
	 * @param string $username your Nike username, should be an email address
	 * @param string $password your Nike password 
	 */
	public function __construct($username, $password) {
		$this->login($username, $password);
        $this->checkUserId();
        $this->getProfile();
	}
	
	/**
	 * login()
	 * Called by __construct and performs the actual login action.
	 * 
	 * @param string $username
	 * @param string $password
	 * 
	 * @return string
	 */
	private function login($username, $password) {
		$url = 'https://secure-nikerunning.nike.com/services/profileService?_plus=true';
		$loginDetails = 'action=login&login='.urlencode($username).'&password='.$password;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $loginDetails);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		$noDoubleBreaks = str_replace(array("\n\r\n\r", "\r\n\r\n", "\n\n", "\r\r", "\n\n\n\n", "\r\r\r\r"), '||', $data);
		$sections = explode('||', $noDoubleBreaks);
		$headerSections = explode('Set-Cookie: ', $sections[0]);
		$this->body = $sections[1].'';
		for($i=1; $i<=4; $i++) {
			$allheaders[] = @str_replace(array("\n\r", "\r\n", "\r", "\n\n", "\r\r"), "", $headerSections[$i]);
		}
		foreach($allheaders as $h) {
			$exploded[] = explode('; ', $h);
		}
		foreach($exploded as $e) {
			$string[] = $e[0];
		}
		$header = implode(';', $string);
		if($contents = @simplexml_load_string($this->body)) {
			$this->userId = (integer) $contents->profile->id;
		} else {
			throw new ErrorException('The XML feed could not be read.');
		}
		$this->cookie = $header;
	}

	/**
	 * cookieValue()
	 * Available for debugging purposes. Using this function in your code can make your
	 * cookie values publicly available
	 * 
	 * @return string
	 */
	public function cookieValue() {
		return $this->cookie;
	}

	/**
	 * checkUserId()
	 * Check that the userId exists and is the correct type
	 * 
	 * @return true
	 */
	private function checkUserId() {
		if(!$this->userId || gettype($this->userId) != 'integer') {
			throw new ErrorException($this->idErrorMessage);
		}
		return true;
	}

	/**
	 * getNikePlusFile()
	 * Gets the contents of a file from the Nike+ service,
	 * 
	 * @param string $filePath the path to the file that needs to be read
	 * 
	 * @return object
	 */
	private function getNikePlusFile($filePath) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($ch, CURLOPT_URL, $filePath);
		$data = curl_exec($ch);
		curl_close($ch);
		if($content = @simplexml_load_string($data)) {
			return $content;
		} else {
			throw new ErrorException('The XML feed could not be read.');
		}
	}

    /**
     * profile()
     * Get the profile of the current user
     * 
     * @return object
     */
    private function getProfile() {
        if(!$this->profile = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/get_user_data.jsp?_plus=true')) {
            throw new Exception($this->feedErrorMessage);
        }
    }

    /**
     * profile()
     * Get the profile of the current user
     * 
     * @return object
     */
    public function profile() {
        return $this->profile;
    }

	/**
	 * getRuns()
	 * Get ALL run data for the current user
	 * 
	 * @return object
	 */
	public function getRuns() {
		if(!$data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/run_list.jsp?_plus=true')) {
			throw new ErrorException($this->feedErrorMessage);
		}
		return $data->runList;
	}

    /**
     * getRun()
     * Get the data for a single run
     * 
     * @param int|string $runId the numeric ID of the run to retrieve
     * 
     * @return object
     */
    public function getRun($runId) {
        if(!$data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/get_run.jsp?_plus=true&id='.$runId)) {
            throw new Exception($this->feedErrorMessage);
        }
        return $data;
    }

    /**
     * getMostRecentRunId()
     * Get the id for a the latest run
     * 
     * @return int the numeric ID of the last run
     */
    public function getMostRecentRunId() {
        return (float) $this->profile->mostRecentRun->attributes()->id;
    }

	/**
	 * getPersonalRecords()
	 * Get the personal records for the current user
	 * 
	 * @return object
	 */
	public function getPersonalRecords() {
		if(!$data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/personal_records.jsp?_plus=true')) {
			throw new Exception($this->feedErrorMessage);
		}
		return $data;
	}

	/**
	 * getGoals()
	 * Get the goals set for the current user
	 * 
	 * @return object
	 */
	public function getGoals() {
		if(!$data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/goal_list.jsp?_plus=true')) {
			throw new Exception($this->feedErrorMessage);
		}
		return $data;
	}

	/**
	 * getCompleteGoals()
	 * Get the completed goals for the current user
	 * 
	 * @return object
	 */
	public function getCompletedGoals() {
		if(!$data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/completed_goal_list.jsp?_plus=true')) {
			throw new Exception($this->feedErrorMessage);
		}
		return $data;
	}

	/**
	 * getUserEvents()
	 * Get a list of events for the current user
	 * 
	 * @return object
	 */
	public function getUserEvents() {
		if(!$data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/userevent/get_user_events.jsp?_plus=true')) {
			throw new Exception($this->feedErrorMessage);
		}
		return $data;
	}
    
    /**
     * getTotalDistance()
     * Get the total distance covered by the current user
     * 
     * @return float|string
     */
    public function getTotalDistance() {
        return (float) $this->profile->userTotals->totalDistance;
    }
    
    /**
     * getTotalTime()
     * Get the total amount of time spent running by the current user
     * 
     * @return float|string
     */
    public function getTotalTime() {
        return (float) $this->profile->userTotals->totalDuration;
    }
    
    /**
     * getTotalCalories()
     * Get the total number of calories burned by the current user
     * 
     * @return float|string
     */
    public function getTotalCalories() {
        return (float) $this->profile->userTotals->totalCalories;
    }
    
    /**
     * getNumberOfRuns()
     * Get the total number of runs made by the current user
     * 
     * @return float|string
     */
    public function getNumberOfRuns() {
        return (float) $this->profile->userTotals->totalRuns;
    }
	
	/**
	 * toMiles()
	 * Convert a value from Km in to miles
	 * 
	 * @param float|string $distance
	 * 
	 * @return int
	 */
	public function toMiles($distance) {
        return number_format(((float) $distance * 0.621371192), 2, '.', ',');
	}
	
}