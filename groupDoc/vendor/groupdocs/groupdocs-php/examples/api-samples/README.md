## Examples

### GroupDocs PHP SDK Api samples

This is the GroupDocs PHP SDK Api samples application written with FatFree framework. Here you can find a lot of samples of GroupDocs SDK API functions using.

### How to deploy and run samples

 1. Download api-samples folder or full SDK (you can find api-samples under examples folder).
 2. Copy all files from api-samples folder to web root folder.
 3. Configure composer.json to use required PHP SDK version.
 4. Open console, cd to web root folder and run command: php composer.phar install (this will download GroupDocs PHP SDK into vendor folder and create autoload.php).
 5. Restart apache and open "VIRTUALHOST_NAME"/index.php.

### Requirements:

* PHP 5.3
* Apache ModRewrite
* PHP Curl extension
* PHP Sockets extension (php_sockets.dll)
* composer.phar (http://getcomposer.org/download/ or use included version)

### How to configure composer.json

To download required version of PHP SDK with composer it's enough to set this setting to composer.json

     {
         "require": {
             "groupdocs/groupdocs-php": "v1.4.0"
         }
      }

To update sdk: php composer.phar update

To see all available PHP SDK versions tags visit this page - https://packagist.org/packages/groupdocs/groupdocs-php

### List of samples:

1. How to login to GroupDocs using the API
2. How to list files within GroupDocs Storage using the Storage API
3. How to upload a file to GroupDocs using the Storage API
4. How to download a file from GroupDocs Storage using the Storage API
5. How to copy / move a file using the GroupDocs Storage API
6. How to add a Signature to a document in GroupDocs Signature
7. How to create a list of thumbnails for a document
8. How to return a URL representing a single page of a Document
9. How to generate an embedded Viewer/Annotation URL for a Document
10. How to share a document to other users
11. How to programmatically create and post an annotation into document. How to delete the annotation
12. How to list all annotations from document
13. How to add collaborator to doc with annotations
14. How to check the list of shares for a folder
15. How to check the number of document's views
16. How to insert Assembly questionary into webpage
17. How to upload a file into the storage and compress it into zip archive
18. How to convert Doc to Docx, Docx to Doc, Docx to PDF, PPT to PDF
19. How to Compare documents using PHP SDK
20. How to Get Compare Change list for document using PHP SDK
21. How to Create and Upload Envelop to GroupDocs account using PHP SDK
22. How to create or update user and add him to collaborators using PHP SDK
23. How to View Document pages as images using PHP SDK
24. How to upload file from URL to GroupDocs account using PHP SDK
25. How to convert DOCX with template fields file into PDF file
26. How to use login method in the API
27. How to create your own questionary using forms and show the result document using PHP SDK
28. How to delete all annotations from document
29. How to use Filepicker.io to upload document and get it's URL
30. How to delete file from GroupDocs Storage
31. How to dinamically create Signature Form using data from HTM form
32. How to create signature form, publish it and configure notification when it was signed
33. How to convert several HTML documents to PDF and merge them to one document
34. How to create folder in the GroupDOcs account
35. How to create assembly from document and merge fields
36. How to download document after sign envelope
37. How to Create and Upload Envelop to GroupDocs account and get signed document
38. How to create new user and add him as collaborator to doc with annotations
39. How to add a Signature to a document and redirect after signing with GroupDocs widget
40. How to set callback for signature form and re-direct when it was signed
41. How to set callback for Annotation and manage user rights using PHP SDK
42. How to add numeration in the doc file using PHP SDK
43. How to download document with annotations using PHP SDK


###[View, Sign, Manage, Annotate, Assemble, Compare and Convert Documents with GroupDocs](http://groupdocs.com)
* [View and Annotate Doc, PDF, Docx, PPT and other documents online with GroupDocs Viewer](http://groupdocs.com/apps)
* [All GroupDocs SDK] (http://groupdocs.com/api/sdk-platforms)
* [All GroupDocs SDK examples] (http://groupdocs.com/api/sdk-examples)

###Created by [Marketplace Team](http://groupdocs.com/marketplace/).
