<?php declare(strict_types=1);

namespace DH\Essentials;

class EasyTemplate {

	private $html = "";

	public function __construct($file = '')
	{
		if (is_file($file)) {
			$this->html = file_get_contents($file);
		}
	}

	public function getSlice(string $marker): mixed
	{
		$start = strpos($this->html, $marker);
		if ($start === false) {
			return '';
		}
		$start += strlen($marker);
		$stop = strpos($this->html, $marker, $start);
		if ($stop === false) {
			return '';
		}
		$html = substr($this->html, $start, $stop - $start);

		$matches = array();
		if (preg_match('/^([^\<]*\-\-\>)(.*)(\<\!\-\-[^\>]*)$/s', $html, $matches) === 1 || preg_match('/^([^\<]*\-\-\>)(.*)$/s', $html, $matches) === 1) {
			return self::slice($matches[2]);
		}
		$matches = array();
		if (preg_match('/(.*)(\<\!\-\-[^\>]*)$/s', $html, $matches) === 1) {
			return self::slice($matches[1]);
		}
		return self::slice($html);
	}

	private static function slice(string $html): EasyTemplate
	{
		$tmpl = new self();
		$tmpl->html = $html;
		return $tmpl;
	}

	private function replaceMarker(string $html, array $markerArr): string
	{
		foreach ($markerArr as $key => $val) {
			$html = str_replace($key, strval($val), $html);
		}
		return $html;
	}

	private function replaceSlice(string $html, array $slicesArr): string
	{
		foreach ($slicesArr as $key => $val) {
			$html = self::trimSlice($html, $key, $val);
		}
		return $html;
	}

	private static function trimSlice(string $html, string $marker, string $slice, int $recursive = 1): string
	{
		$start = strpos($html, $marker);
		if ($start === false) {
			return $html;
		}
		$startAM = $start + strlen($marker);
		$stop = strpos($html, $marker, $startAM);
		if ($stop === false) {
			return $html;
		}
		$stopAM = $stop + strlen($marker);
		$before = substr($html, 0, $start);
		$after = substr($html, $stopAM);
		$between = substr($html, $startAM, $stop - $startAM);
		if ($recursive) {
			$after = self::trimSlice($after, $marker, $slice, $recursive);
		}
		$matches = array();
		if (preg_match('/^(.*)\<\!\-\-[^\>]*$/s', $before, $matches) === 1) {
			$before = $matches[1];
		}
		if (is_array($slice)) {
			$matches = array();
			if (preg_match('/^([^\<]*\-\-\>)(.*)(\<\!\-\-[^\>]*)$/s', $between, $matches) === 1) {
				$between = $matches[2];
			} elseif (preg_match('/^(.*)(\<\!\-\-[^\>]*)$/s', $between, $matches) === 1) {
				$between = $matches[1];
			} elseif (preg_match('/^([^\<]*\-\-\>)(.*)$/s', $between, $matches) === 1) {
				$between = $matches[2];
			}
		}
		$matches = array();
		if (preg_match('/^[^\<]*\-\-\>(.*)$/s', $after, $matches) === 1) {
			$after = $matches[1];
		}
		if (is_array($slice)) {
			$between = $slice[0] . $between . $slice[1];
		} else {
			$between = $slice;
		}
		return $before . $between . $after;
	}

	public function parse(array $markerArr = array(), array $slicesArr = array()): string
	{
		$html = $this->html;
		if (!empty($markerArr)) {
			$html = $this->replaceMarker($html, $markerArr);
		}
		if (!empty($slicesArr)) {
			$html = $this->replaceSlice($html, $slicesArr);
		}
		return $html;
	}
}