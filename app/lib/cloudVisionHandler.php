<?php

class CloudVisionHandler
{
	const MAXRESULTS = 5;

	protected $url = 'https://vision.googleapis.com/v1/images:annotate';
	protected $path = '';
	protected $type = '';

	protected $detectionType = array(
		'LABEL_DETECTION'       => 'labelAnnotations',
		'TEXT_DETECTION'        => 'textAnnotations',
		'FACE_DETECTION'        => 'faceAnnotations',
		'LANDMARK_DETECTION'    => 'landmarkAnnotations',
		'LOGO_DETECTION'        => 'logoAnnotations',
		'SAFE_SEARCH_DETECTION' => 'safeSearchAnnotation',
		'IMAGE_PROPERTIES'      => 'imagePropertiesAnnotation',
	);

	public function __construct()
	{
		$this->_setUrl();
	}

	/**
	 * set CloudVisionAPI URL for call
	 * @return void
	 */
	protected function _setUrl()
	{
		$apiFilePath = __DIR__.'/../.credential';
		$apiKey = file_get_contents($apiFilePath);
		$query = http_build_query(['key' => $apiKey]);
		$this->url .= '?'.$query;
	}

	public function setFile($path)
	{
		$this->path = $path;
	}

	/**
	 * Set Detection type
	 * @param  string  $type  detection type
	 * @return void
	 */
	public function setDetection($type)
	{
		if (!in_array($type, array_keys($this->detectionType))) {
			throw new Exception('Invalid Detection Type ERROR!');
		}

		$this->type = $type;
	}

	/**
	 * Call CloudVisionAPI and return results
	 * @return array|false
	 */
	public function call()
	{
		$fileBase64 = $this->_getFileBase64();
		$requestJson = $this->_buildRequestJson($fileBase64);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $this->url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $requestJson);

		$result = curl_exec($ch);
		if ($result === false || empty($result)) {
			return false;
		}
		return $this->_parseResult(json_decode($result));
	}

	protected function _getFileBase64()
	{
		$fileBase64 = base64_encode(file_get_contents($this->path));
		if (!$fileBase64) {
			throw new Exception('Something ERROR for base64 Encoding');
		}
		return $fileBase64;
	}

	/**
	 * Build Json for requesting CloudVisionAPI
	 * @param  string  $fileBase64  file contents as Base64
	 * @return json
	 */
	protected function _buildRequestJson($fileBase64)
	{
		$request = [
			'requests' => [
				[
					'image' => [
						'content' => $fileBase64,
					],
					'features' => [
						[
							'type' => $this->type,
							'maxResults' => static::MAXRESULTS,
						],
					],
				],
			],
		];

		return json_encode($request);
	}

	/**
	 * Parse result from Cloud Vision API
	 * @return array  contains stdObject
	 */
	protected function _parseResult($result)
	{
		$typeKey = $this->detectionType[$this->type];
		if (!property_exists($result->responses[0], $typeKey)) {
			throw new Exception('Invalid Annotation ERROR!');
		}

		return $result->responses[0]->{$typeKey};
	}
}
