<?php

class ParallelRequest extends Request{

	private $urls;
	private $mh;
	private $handles = array();
	private $result = array();

	public function __construct($urls) {
		$this->urls = $urls;
		$this->mh = curl_multi_init();
		$this->defaults();
		$this->setup();
	}

	public function __destruct() {
		$this->close();
	}

	public function awaitAll() {
		$this->result = $this->exec();
		return $this;
	}

	public function response() {
		return $this->result;
	}

	private function setup() {
		foreach ($this->urls as $id => $data) {
			$ch = $this->init();
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
			$this->setupCurl($ch);
			$this->handles[$id] = $ch;
			curl_multi_add_handle($this->mh, $ch);
		}
	}

	private function exec() {
		$running = null;
		$results = array();

		do {
			curl_multi_exec($this->mh, $running);
		} while ($running);

		foreach ($this->handles as $id => $handle) {
			$results[$id] = curl_multi_getcontent($handle);
		}

		return $results;
	}

	private function close() {
		foreach ($this->handles as $handle) {
			curl_multi_remove_handle($this->mh, $handle);
		}
		curl_multi_close($this->mh);
	}
}

?>