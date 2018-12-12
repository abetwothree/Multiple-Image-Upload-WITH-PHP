<?php
  //be sure to include your database connection code or file up here

    //begins looping through each image uploaded
    foreach ($_FILES['Image']['tmp_name'] as $key=>$tmp_name) {
        //this message is solely for debugging purposes. You can delete or or comment it out when implemented on your code
        echo 'Inside foreach loop '.$key.' File name: '.$_FILES['Image']['name'][$key].' <br />';

        //test if there are errors with the image
        if ($_FILES['Image']['error'][$key] > 0) {
            $imageError = true;
            $imageMessage = 'There was a problem with the image '.$_FILES['Image']['error'][$key].' File number: '.$key.' File name: '.$_FILES['Image']['name'][$key];
            echo $imageMessage.'<br />';

        //test if it is an image. Important to prevent malicious software from being uploaded to your server
        } elseif (getimagesize($_FILES['Image']['tmp_name'][$key]) == false) {
            $imageError = true;
            $imageMessage = 'You did not uploaded an image.'.' File number: '.$key.' File name: '.$_FILES['Image']['name'][$key];
            echo $imageMessage.'<br />';
        } else {
            //set variables from image. The image name is changed to prevent duplicates over writing other images when it is uploaded to the server.
            $temp = explode('.', $_FILES['Image']['name'][$key]);
            $imageName = round(microtime(true)).$key.'.'.end($temp);
            //$imageName = $_FILES["Image"]["name"];
            $imageType = $_FILES['Image']['type'][$key];
            $imageSize = $_FILES['Image']['size'][$key];
            $imageTempName = $_FILES['Image']['tmp_name'][$key];

            //Check if it is a valid image type. You can add extensions to the array to expand the valid types of images you want uploaded.
            $validTypes = ['gif', 'jpg', 'jpe', 'jpeg', 'png'];
            $typeExt = pathinfo($imageName);
            $ext = strtolower($typeExt['extension']);
            //extension check
            if (!in_array($ext, $validTypes)) {
                $imageError = true;
                $imageMessage = 'File uploaded is not a valid image type.'.' File number: '.$key.' File name: '.$_FILES['Image']['name'][$key];
                echo $imageMessage.'<br />';

            //check the image size of the image. You can set this number to whatever limit your server has or whatever limit you want it to be.
            //my server is set to 16 megabytes as limit.
            } elseif ($imageSize > 16777216) {
                $imageError = true;
                $imageMessage = 'Sorry, the image file size is too big. Shorten it and try again.'.' File number: '.$key.' File name: '.$_FILES['Image']['name'][$key];
                echo $imageMessage.' '.$imageSize.'<br />';

            //After all those checks, this is where the image is uploaded to server
            } else {

                //where we will save the image. The first part grabs your base directory of your server. Edit the /img/ part to store images in whatever directory you want.
                $directory = dirname(__FILE__).'/img/';

                //check the length of file name and change it if it is too long. Important because file names can't have a file name longer than 250 characters.
                if (strlen($imageName) > 225) {
                    $imageName = substr($imageName, -225);
                }

                //this variable is solely for debugging purposes. I highly suggest commenting it out when you implement it in your code for users.
                $file_result =
                'Upload: '.$imageName.'<br />'.
                'Type: '.$imageType.'<br />'.
                'Size: '.$imageSize.' kb <br />'.
                'Temp file: '.$imageTempName.'<br />'.
                'Upload Directory: '.$directory.$imageName.'<br />';
                echo $file_result;

                //Now we move the image to the server and check if it was successful
                if (move_uploaded_file($_FILES['Image']['tmp_name'][$key], $directory.$imageName) === true) {
                    $imageError = false;
                    //message for debuggin to let you know the image was moved to the server
                    $imageMessage = 'Image uploaded successfully.'.' File number: '.$key.' File name: '.$_FILES['Image']['name'][$key];
                    echo $imageMessage.'<br />';

                    //This variable tells the database where you saved your image on the server and the name of it
                    $dbDirectory = 'img/'.$imageName;

                    //store info into database
                    try {
                        //this is the array with the variables to store the image info on the database
                        $imageArray = [$imageName, $dbDirectory, $imageType, $imageSize];

                        /*this is the prepare statement to insert the info in the database. I'm leaving out the Image_ID table column in the
                        database because I have it set to autoincrement on row insertion. My database PDO handle is '$dbPDO' so change that
                        to whatever you have yours as.
                        My tabe is structure in the following way:
                            Image_ID int(11) AUTO_INCREMENT
                            Name varchar(250)
                            Path varchar(350)
                            Type varchar(10)
                            Size int(11)
                        */
                        $insertImage = $dbPDO->prepare('INSERT INTO Images (Name, Path, Type, Size) VALUES (?, ?, ?, ?)');
                        $insertImage->execute($imageArray);

                        //debuggin echo statement to let you know the database had the info inserterd. Delete or comment out on production implementation
                        echo 'Image inserted into DB.'.' File number: '.$key.' File name: '.$_FILES['Image']['name'][$key].'<br /> <br />';
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                } else {
                    //if the image is not uploaded to the server this will let you know. Delete or comment out on production version.
                    $imageError = true;
                    $imageMessage = 'File was not uploaded successfully.'.' File number: '.$key.' File name: '.$_FILES['Image']['name'][$key];
                    echo $imageMessage.'<br /><br />';
                }
            }
        }
    }
