<?php
/**
 *  Copyright 2012 GroupDocs.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

class RequestSignerTest extends PHPUnit_Framework_TestCase {

	private $basePath = "https://api.groupdocs.com/v2.0/storage/2721ad21bcf0d71e/folders/test.docx?description=";
	private $privateKey = "8d8a7d642a807a31c2741c101a60cef2";
	private $signer;
	
	private function sign($url) {
		return APIClient::encodeURI($this->signer->signUrl($url));
	}
	
	protected function setUp() {
        $this->signer = new GroupDocsRequestSigner($this->privateKey);
	}
	
	public function testSimplePath() {
		$requestUrl = $this->basePath . "uploaded";
		$expected = $requestUrl . "&signature=OT6eFQYsZulqFDTsv4XSNlmc3FY";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}

	public function testPathEndingWithSpace() {
		$requestUrl = $this->basePath . "test DOC file ";
		$expected = $this->basePath . "test%20DOC%20file%20&signature=rdw%2F%2FiP0mwN7Bme2sJ99AZmOpq4";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}
	
	public function testPathWithAtSymbol() {
		$requestUrl = $this->basePath . "with @ symbol";
		$expected = $this->basePath . "with%20@%20symbol&signature=ar7cFk0RSaVukMIUbvJB%2FYp5oHs";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}
	
	public function testPathWithStarSymbol() {
		$requestUrl = $this->basePath . "with * symbol";
		$expected = $this->basePath . "with%20*%20symbol&signature=iwzySzo6jbCXhf4lMB3r%2FtcV8Kc";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}
	
	public function testPathWithBrackets() {
		$requestUrl = $this->basePath . "with ( and )";
		$expected = $this->basePath . "with%20(%20and%20)&signature=rDzSggRSTkBFOi%2F0P%2Bta6j%2BvYzY";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}
	
	public function testPathWithSquareBrackets() {
		$requestUrl = $this->basePath . "with [ and ]";
		$expected = $this->basePath . "with%20%5B%20and%20%5D&signature=hfLg0YTTGdXpvdqV%2B7y2YJkJZqo";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}
	
	public function testPathWithEncodeURIComponent() {
		// https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/encodeURIComponent
		$requestUrl = $this->basePath . "alpha123 - _ . ! ~ * ' ( )";
		$expected = $this->basePath . "alpha123%20-%20_%20.%20!%20~%20*%20'%20(%20)&signature=6ZTSvVrJ3Wvp9aZ93wtp5oH2hJ4";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}
	
	public function testPathWithEncodeURI() {
		// https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/encodeURI
		$requestUrl = $this->basePath . "alpha123 ; , / ? : @ & = + $"; // omitted # 
		$expected = $this->basePath . "alpha123%20;%20,%20/%20?%20:%20@%20&%20=%20%2B%20$&signature=zqj1XJtWE0%2F%2FMon%2FiSJN%2FC6Yyco";
		$this->assertEquals($expected, $this->sign($requestUrl));
	}

}
