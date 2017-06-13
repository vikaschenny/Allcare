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

class AbstractIntegrationTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		$privateKey = "28090f3458bc1f97d9e0262a0768c308"; //TODO get it from command line
		$this->apiClient = new APIClient(new GroupDocsRequestSigner($privateKey));
		$this->apiClient->setDebug(true);
		$this->userId = "a9f81d75a3a7df86";
	}
	
	public function test_dumy() {
                
        }
}

