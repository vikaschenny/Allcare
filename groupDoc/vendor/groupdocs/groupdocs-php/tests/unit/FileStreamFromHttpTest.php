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

class FileStreamFromHttpTest extends PHPUnit_Framework_TestCase {

	public function test_size() {
		$filename = "out.doc";
		$fs = FileStream::fromHttp(dirname(__FILE__), $filename);
		$fs->bodyCallback(null, file_get_contents(dirname(__FILE__)."/resources/test.doc"));
		
		$expected = 29696;
		$this->assertEquals($expected, $fs->getSize());
		$this->assertEquals(true, fclose($fs->getInputStream()));
		unlink(dirname(__FILE__)."/".$filename);
	}

	public function test_contentType() {
		$fs = FileStream::fromHttp(dirname(__FILE__), "out.doc");
		$fs->headerCallback(null, "Content-Type: application/msword");
		
		$expected = "application/msword";
		$this->assertEquals($expected, $fs->getContentType());
	}
	
	public function test_fileNameFromHeader() {
		$fs = FileStream::fromHttp(dirname(__FILE__));
		$fs->headerCallback(null, "Content-Disposition: attachment; filename='test.doc'");
		
		$expected = "test.doc";
		$this->assertEquals($expected, $fs->getFileName());
	}
	
	public function test_fileNameFromUrl() {
		$requestUrl = "https://api.groupdocs.com/v2.0/storage/e50280a09d8188e3/files/ad9080c0d33c9ef8954aec2d16d8d5694dfba8233622ac2c8f077a667c07ae77?signature=m5eCNhZwRv8KlUKeeZmfVrtnSb4";
		$fs = $this->getMockBuilder('FileStream')->disableOriginalConstructor()->setMethods(array('getCurlInfo'))->getMock();
		$fs->expects($this->once())->method('getCurlInfo')->will($this->returnValue($requestUrl));
		$fs->requestUrl = null;
		$fs->filePath = null;
		$fs->downloadDirectory = "something";
		$fs->headerCallback(null, "Some-Header: value");
		
		$expected = "ad9080c0d33c9ef8954aec2d16d8d5694dfba8233622ac2c8f077a667c07ae77";
		$this->assertEquals($expected, $fs->getFileName());
	}
	
}
