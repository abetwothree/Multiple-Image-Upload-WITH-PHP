# multipleImageUploadPHP

PHP code to uploade multiple images to a webserver and save the information to a database while making a six point check to each file uploaded. 

It checks for the following:

Checks to make sure there's no errors in the image upload

Checks to make sure it is an image

Checks to make sure it is a certain type of image

Checks the file size of the image

Checks the file name to limit it to 225 characters

Changes the file name to a fully unique file name to avoid over writing files with same names.
