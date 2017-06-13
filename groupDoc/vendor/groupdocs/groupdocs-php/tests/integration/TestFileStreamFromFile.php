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

class TestFileStreamFromFile extends PHPUnit_Framework_TestCase {

	protected function setUp() {
        $this->fs = FileStream::fromFile(dirname(__FILE__)."/resources/test.doc");
	}
	
	
	public function test_size() {
		$expected = 29696;
		$this->assertEquals($expected, $this->fs->getSize());
	}

	public function test_contentType() {
		$expected = "application/msword";
		$this->assertEquals($expected, $this->fs->getContentType());
	}
	
	public function test_fileName() {
		$expected = "test.doc";
		$this->assertEquals($expected, $this->fs->getFileName());
	}
	
	public function test_inputStream() {
		$expected = true;
		$this->assertEquals($expected, fclose($this->fs->getInputStream()));
	}

}
