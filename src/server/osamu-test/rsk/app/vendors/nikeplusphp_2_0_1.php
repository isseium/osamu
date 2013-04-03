<?php

/*******************************************************
/*
/* NikePlusPHP v2.0
/* http://nikeplusphp.org
/* Created by Charanjit Chana, http://charanj.it
/* Requires PHP 5 with SimpleXML and cURL
/*
/* Your Nike login information is required
/*
/*******************************************************/
 
class NikePlusPHP {

	/* public variables */
	public $idErrorMessage = '<p class="nikeplusphp-error">The login details you supplied are incorrect.</p>';
	public $feedErrorMessage = '<p class="nikeplusphp-error">There was an error fetching the feed from the Nike servers.</p>';

	/* private variables */
	private $data, $userId, $cookie, $header, $body, $json;

	/*
	** __construct
	** called when you start the class, pass unique userID & true to show distances in miles
	*/
	public function __construct($username, $password, $miles = false, $json = false) {
		$this->cookie = $this->login($username, $password);
		$this->miles = $miles;
		$this->json = $json;
	}
	
	/*
	** login
	** called at the start to get cookie information
	*/
	private function login($username, $password) {
		$url = 'https://secure-nikerunning.nike.com/services/profileService?_plus=true';
		$login_details = 'action=login&login='.urlencode($username).'&password='.$password;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $login_details);
		curl_setopt($ch, CURLOPT_URL, $url);
		$this->data = curl_exec($ch);
		curl_close($ch);
		$no_double_breaks = str_replace(array("\n\r\n\r", "\r\n\r\n", "\n\n", "\r\r", "\n\n\n\n", "\r\r\r\r"), '||', $this->data);
		$sections = explode('||', $no_double_breaks);
		$header_sections = explode('Set-Cookie: ', $sections[0]);
		$this->body = $sections[1];
		for($i=1; $i<=4; $i++) {
			$allheaders[] = str_replace(array("\n\r", "\r\n", "\r", "\n\n", "\r\r"), "", $header_sections[$i]);
		}
		foreach($allheaders as $h) {
			$exploded[] = explode('; ', $h);
		}
		foreach($exploded as $e) {
			$string[] = $e[0];
		}
		$header = implode(';', $string);
		if($body = @simplexml_load_string($this->body)) {
			$this->userId = (integer) $body->profile->id;
		} else {
			return false;
		}
		$this->header = $header;
		return $header;
	}

	/*
	** cookieValue()
	** a function for debugging, returns the value of the cookie (made publicly available)
	*/
	public function cookieValue() {
		return $this->cookie;
	}

	/*
	** checkUserId()
	** check if the userId is valid, no parameters
	*/
	private function checkUserId() {
		if(!$this->userId) {
			return false;
		} else {
			if(gettype($this->userId) != 'integer') {
				return false;
			}
		}
		return true;
	}

	/*
	** getNikePlusFile()
	** get the contents of a file from Nike, filePath required
	** returns false if it can't load an XML string
	*/
	private function getNikePlusFile($filePath) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Set curl to return the data instead of printing it to the browser.
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array($this->header));
		curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
		curl_setopt($ch, CURLOPT_URL, $filePath);
		$this->data = curl_exec($ch);
		curl_close($ch);
		if($content = @simplexml_load_string($this->data)) {
			return $content;
		} else {
			return false;
		}
	}

	/*
	** kmToMiles
	** Automatically convert the default km values into miles, distance required
	*/
	private function kmToMiles($distance) {
		if($this->miles) {
			return ($distance * 0.621371192);
		} else {
			return $distance;
		}
	}

	/*
	** json
	** Function that decides (based on user input) if the output should be a JSON object or an array
	*/
	private function json($input) {
		if($this->json) {
			return json_encode($input);
		} else {
			return $input;
		}
	}

	/*
	** profile
	** build a profile, no parameters
	*/
	public function profile() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		if(!$this->data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/get_user_data.jsp?_plus=true')) {
			echo $this->feedErrorMessage;
			return false;
		}
		$profile = array(
			'externalProfileID' => (float) $this->data->user->attributes()->externalProfileId,
			'userID' => (float) $this->data->user->attributes()->id,
            'status' => (string) $this->data->user->status,
            'gender' => (string) $this->data->user->gender,
            'state' => (string) $this->data->user->state,
			'city' => (string) $this->data->user->city,
            'country' => (string) $this->data->user->country,
            'plusLevel' => (float) $this->data->user->plusLevel,
            'activeOwnedChallengeCount' => (float) $this->data->user->activeOwnedChallengeCount,
			'totalDistance' => $this->kmToMiles((float) $this->data->userTotals->totalDistance),
            'totalDuration' => (float) $this->data->userTotals->totalDuration,
            'totalRunsWithRoutes' => (float) $this->data->userTotals->totalRunsWithRoutes,
            'totalRuns' => (float) $this->data->userTotals->totalRuns,
            'totalCalories' => (float) $this->data->userTotals->totalCalories,
            'totalWorkouts' => (float) $this->data->userTotals->totalWorkouts,
            'totalCardioDistance' => $this->kmToMiles((float) $this->data->userTotals->totalCardioDistance),
            'averageRunsPerWeek' => (float) $this->data->userTotals->averageRunsPerWeek,
            'preferredRunDayOfWeek' => (float) $this->data->userTotals->preferredRunDayOfWeek,
            'pedoWorkouts' => (float) $this->data->userTotals->pedoWorkouts,
            'totalSteps' => (float) $this->data->userTotals->totalSteps,
            'longestStepcount' => (float) $this->data->userTotals->longestStepcount,
            'caloriesPedometer' => (float) $this->data->userTotals->caloriesPedometer,
            'totalCaloriesPedometer' => (float) $this->data->userTotals->totalCaloriesPedometer,
			'screenName' => (string) $this->data->userOptions->screenName,
			'distanceUnit' => (string) $this->data->userOptions->distanceUnit,
            'dateFormat' => (string) $this->data->userOptions->dateFormat,
            'startWeek' => (string) $this->data->userOptions->startWeek,
            'avatar' => (string) $this->data->userOptions->avatar,
            'uploadedAvatar' => (string)  $this->data->userOptions->uploadedAvatar,
			'isPublic' => (string) $this->data->userOptions->isPublic,
            'emailGoalEnding' => (string) $this->data->userOptions->emailGoalEnding,
            'emailGoalComplete' => (string) $this->data->userOptions->emailGoalComplete,
            'emailWeeklyTraining' => (string) $this->data->userOptions->emailWeeklyTraining,
            'emailChallengeEnding' => (string) $this->data->userOptions->emailChallengeEnding,
            'emailChallengeStarting' => (string) $this->data->userOptions->emailChallengeStarting,
            'emailChallengeWinner' => (string) $this->data->userOptions->emailChallengeWinner,
			'mostRecentRunId' => (float) $this->data->mostRecentRun->attributes()->mostRecentRunId,
            'startTime' => (float) strtotime($this->data->mostRecentRun->startTime),
            'distance' => $this->kmToMiles((float) $this->data->mostRecentRun->distance),
            'duration' => (float) $this->data->mostRecentRun->duration,
            'workoutType' => (string) $this->data->mostRecentRun->workoutType,
            'equipment' => (string) $this->data->mostRecentRun->equipment
        );
        if($this->json) {
			return $this->json($profile);
		} else {
			return $profile;
		}
	}

	/*
	** getRuns
	** get ALL run data, no parameters
	*/
	private function getRuns() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		if(!$this->data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/run_list.jsp?_plus=true')) {
			echo $this->feedErrorMessage;
			return false;
		}
		return $this->data->runList;
	}

	/*
	** fullRunInfo,
	** get all the information on each run, no parameters
	*/
	public function fullRunInfo() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$nikePlusRunList = $this->getRuns();
		foreach($nikePlusRunList->run as $run) {
			$attr = $run->attributes();
			$runArray[] = array(
				'runId'=>(float) $attr->id,
				'startTime'=> (string) $run->startTime,
				'distance'=> $this->kmToMiles((float) $run->distance),
				'duration'=> (float) $run->duration,
				'synctime'=> (float) $run->syncTime,
				'calories'=> (float) $run->calories,
				'name'=> (string) $run->name,
				'description'=> (string) $run->description,
				'howFelt'=> (string) $run->howFelt,
				'weather'=> (string) $run->weather,
				'terrain'=> (string) $run->terrain,
				'equipmentType'=> (string) $run->equipmentType
			);
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** basicRunInfo,
	** only return basic run information (distance, duration and calories), no parameters
	*/
	public function basicRunInfo() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$nikePlusRunList = $this->getRuns();
		foreach($nikePlusRunList->run as $run) {
			$attr = $run->attributes();
			$runArray[] = array(
				'runId'=> (float) $attr->id,
				'distance'=> $this->kmToMiles((float) $run->distance),
				'duration'=> (float) $run->duration,
				'calories'=> (float) $run->calories
			);
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** getRun
	** get SINGLE run data, run ID
	*/
	public function getRun($runId, $internalCall = false) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		if(!$this->data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/get_run.jsp?_plus=true&id='.$runId)) {
			echo $this->feedErrorMessage;
			return false;
		}
		$sportsData = $this->data->sportsData;
		$runArray = array(
			'weight'=> (float) $sportsData->userInfo->weight,
			'device'=> (string) $sportsData->userInfo->device,
			'empedID'=> (float) $sportsData->userInfo->empedID,
			'workoutType'=> (string) $sportsData->runSummary->attributes()->workoutType,
			'distance'=> $this->kmToMiles((float) $sportsData->runSummary->distance),
			'duration'=> (float) $sportsData->runSummary->duration,
			'calories'=> (float) $sportsData->runSummary->calories,
			'equipmentType'=> (string) $sportsData->runSummary->equipmentType,
			'batteryLifeTime'=> (float) $sportsData->batteryLifetime,
			'startTime'=> (float) strtotime($sportsData->startTime),
			'extendedDataList'=> (string) $sportsData->extendedDataList->extendedList,
			'bestComparableRun'=> (float) $sportsData->bestComparableRun,
			'name'=> (string) $sportsData->name,
			'description'=> (string) $sportsData->description,
			'signatureValidationStatus'=> (string) $sportsData->signatureValidationStatus,
			'howFelt'=> (string) $sportsData->howFelt,
			'weather'=> (string) $sportsData->weather,
			'terrain'=> (string) $sportsData->terrain,
			'previousRunId'=> (float) $sportsData->previousRun->attributes()->id,
			'nextRunId'=> (float) $sportsData->nextRun->attributes()->id,
			'extendedData'=> str_replace(' ', '', (string) $sportsData->extendedDataList->extendedData),
			'routeId' => (float) $sportsData->route->routeId,
			'routeName' => (string) $sportsData->route->name,
			'name'=> (string) $sportsData->name,
			'isHumanRaceRun' => (string) $sportsData->isHumanRaceRun,
			'isFirstHeartRun' => (string) $sportsData->isFirstHeartRun
		);
		if($sportsData->playListList) {
				$runArray['playListName'] = (string) $sportsData->playListList->playList->playListName;
		}
		if($this->json && !$internalCall) {
			return $this->json($runArray);
		} else {
			return $runArray;
		}
	}

	/*
	** basicFirstRun
	** get all the information on the last run
	*/
	public function basicFirstRun() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		$run = $this->data->run[0];
		$runArray = array(
			'runId'=>(float) $run->attributes()->id,
			'startTime'=> (string) $run->startTime,
			'distance'=> $this->kmToMiles((float) $run->distance),
			'duration'=> (float) $run->duration,
			'synctime'=> (float) $run->syncTime,
			'calories'=> (float) $run->calories,
			'name'=> (string) $run->name,
			'description'=> (string) $run->description,
			'howFelt'=> (string) $run->howFelt,
			'weather'=> (string) $run->weather,
			'terrain'=> (string) $run->terrain,
			'equipmentType'=> (string) $run->equipmentType
		);
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** basicLastRun
	** get all the information on the last run
	*/
	public function basicLastRun() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		$run = $this->data->run[count($this->data->run) - 1];
		$runArray = array(
			'runId'=>(float) $run->attributes()->id,
			'startTime'=> (string) $run->startTime,
			'distance'=> $this->kmToMiles((float) $run->distance),
			'duration'=> (float) $run->duration,
			'synctime'=> (float) $run->syncTime,
			'calories'=> (float) $run->calories,
			'name'=> (string) $run->name,
			'description'=> (string) $run->description,
			'howFelt'=> (string) $run->howFelt,
			'weather'=> (string) $run->weather,
			'terrain'=> (string) $run->terrain,
			'equipmentType'=> (string) $run->equipmentType
		);
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsBetwenDates
	** get all run data between two dates, start and end date
	*/
	public function runsBetweenDates($start, $end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			$runTime = strtotime($run->startTime);
			if($runTime > strtotime($start) && $runTime < strtotime($end)) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsBetwenDistances
	** get all run data between two distances, start and end distances
	*/
	public function runsBetweenDistances($start, $end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			$distance = (float) $run->distance;
			if($distance > (float) $start && $distance < (float) $end) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);		
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsBetwenDuration
	** get all run data between two durations, start and end durations
	*/
	public function runsBetweenDurations($start, $end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			$duration = (float) $run->duration;
			if($duration > (float) $start && $duration < (float) $end) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);	
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsSinceDate
	** get all run data since a date, start date
	*/
	public function runsSinceDate($start) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			if(strtotime($run->startTime) > strtotime($start)) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsBeforeDate
	** get all run data before a date, end date
	*/
	public function runsBeforeDate($end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			$runTime = strtotime($run->startTime);
			if($runTime < strtotime($end)) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsOverDistance
	** get all run data over a distance, shortest distance
	*/
	public function runsOverDistance($end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			if((float) $run->distance > (float) $end) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsUnderDistance
	** get all run data under a distance, furthest distance
	*/
	public function runsUnderDistance($end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			if((float) $run->distance < (float) $end) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsOverDuration
	** get all run data over a duration, shortest duration
	*/
	public function runsOverDuration($end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			if((float) $run->duration > (float) $end) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** runsUnderDuration
	** get all run data under a duration, furthest duration
	*/
	public function runsUnderDuration($end) {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$this->data = $this->getRuns();
		foreach($this->data->run as $run) {
			if((float) $run->duration < (float) $end) {
				$runArray[] = array(
					'runId'=>(float) $run->attributes()->id,
					'startTime'=> (string) $run->startTime,
					'distance'=> $this->kmToMiles((float) $run->distance),
					'duration'=> (float) $run->duration,
					'synctime'=> (float) $run->syncTime,
					'calories'=> (float) $run->calories,
					'name'=> (string) $run->name,
					'description'=> (string) $run->description,
					'howFelt'=> (string) $run->howFelt,
					'weather'=> (string) $run->weather,
					'terrain'=> (string) $run->terrain,
					'equipmentType'=> (string) $run->equipmentType
				);
			}
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** getPersonalRecords
	** get your personal records
	*/
	public function getPersonalRecords() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		if(!$this->data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/personal_records.jsp?_plus=true')) {
			echo $this->feedErrorMessage;
			return false;
		}
		$personalRecords = $this->data->PersonalRecordList->PersonalRecord;
		$recordsArray = array(
			'farthestRun'=> (float) $personalRecords[0]->value,
			'fastest1Mile'=> (float) $personalRecords[1]->value,
			'fastest5K'=> (float) $personalRecords[2]->value,
			'fastest10K'=> (float) $personalRecords[3]->value
		);
		if($this->json) {
			return $this->json($recordsArray);
		} else {
			return $recordsArray;
		}
	}

	/*
	** getGoals
	** get your goals set with Nike+
	*/
	public function getGoals() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		if(!$this->data = $this->getNikePlusFile('http://nikerunning.nike.com/nikeplus/v2/services/app/goal_list.jsp?_plus=true')) {
			echo $this->feedErrorMessage;
			return false;
		}
		$goals = $this->data->goalList->goal;
		foreach($goals as $goal) {
			$goalsArray[] = array(
				'goalId' => (float) $goal->attributes()->id,
				'complete' => (boolean) $goal->complete,
				'level' => (string) $goal->level,
				'type' => (string) $goal->definition->type,
				'numberOfRuns' => (float) $goal->definition->numberOfRuns,
				'totalProgress' => (float) $goal->definition->totalProgress,
				'schedule' => array()
			);
		}
		if($this->json) {
			return $this->json($goalsArray);
		} else {
			return $goalsArray;
		}
	}

	/*
	** getTimes
	** get time information for each run (start, duration, end, sync), no parameters
	*/
	public function getTimes() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$nikePlusRunList = $this->getRuns();
		foreach($nikePlusRunList->run as $run) {
			$attr = $run->attributes();
			$runArray[] = array(
				'runId'=>(float) $attr->id,
				'startTime'=> (float) strtotime($run->startTime),
				'duration'=> (float) $run->duration,
				'endTime'=> (float) strtotime($run->startTime) + (float) $run->duration,
				'synctime'=> (float) $run->syncTime
			);
		}
		if(!empty($runArray)) {
			if($this->json) {
				return $this->json($runArray);
			} else {
				return $runArray;
			}
		} else {
			return false;
		}
	}

	/*
	** totalRunTime
	** get the total time spent running
	*/
	public function totalRunTime() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$nikePlusRunList = $this->getRuns();
		$runTime = 0;
		foreach($nikePlusRunList->run as $run) {
			$runTime += $run->duration;
		}
		if($this->json) {
			return $this->json($runTime);
		} else {
			return $runTime;
		}
	}

	/*
	** totalDistance
	** get the total distance run
	*/
	public function totalDistance() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$nikePlusRunList = $this->getRuns();
		$distance = 0;
		foreach($nikePlusRunList->run as $run) {
			$distance += (float) $run->distance;
		}
		return $this->kmToMiles($distance);
	}

	/*
	** totalCalories
	** get the total calories used
	*/
	public function totalCalories() {
		if(!$this->checkUserId()) {
			echo $this->idErrorMessage;
			return false;
		}
		$nikePlusRunList = $this->getRuns();
		$calories = 0;
		foreach($nikePlusRunList->run as $run) {
			$calories += (float) $run->calories;
		}
		return $calories;
	}

	/*
	** getGraphPoints
	** workout points you can plot on a graph
	*/
	public function getGraphPoints($runId) {
		$runData = $this->getRun($runId, true);
		$graphData = explode(',', $runData['extendedData']);
		$minDistance = min($graphData);
		$maxDistance = max($graphData);
		$numPoints = count($graphData);
		$graphPoints = '0,';
		for($i = 0; $i < $numPoints; $i++) {
			if($i < ($numPoints - 1)) {
				$graphPoints .= (($graphData[$i + 1] - $graphData[$i]) * 1000).',';
			} else {
				$graphPoints .= '0';
			}
		}
		if($this->json) {
			return $this->json($graphPoints);
		} else {
			return $graphPoints;
		}
	}
}

?>