<?php
namespace LetsEat\Helper;

class Response
{
	/**
	 * All went well, and (usually) some data was returned.
	 */
	const STATUS_SUCCESS = 'success';

	/**
	 * There was a problem with the data submitted, or some pre-condition of the API call wasn't satisfied.
	 */
	const STATUS_FAIL = 'fail';

	/**
	 * An error occurred in processing the request, i.e. an exception was thrown
	 */
	const STATUS_ERROR = 'error';

	public $status;

	public $message;

	public $data;

	public $code;

	/**
	 * @return Response
	 */
	public static function get()
	{
		$response = new self();
		return $response;
	}

	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
	public function setCode($code)
	{
		$this->code = $code;
		return $this;
	}

	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

	public function setStatusSuccess()
	{
		$this->status = self::STATUS_SUCCESS;
		return $this;
	}

	public function setStatusFail()
	{
		$this->status = self::STATUS_FAIL;
		return $this;
	}

	public function setStatusError()
	{
		$this->status = self::STATUS_ERROR;
		return $this;
	}
} 