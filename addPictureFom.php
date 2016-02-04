<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
</head>

  <body>
		<form enctype="multipart/form-data" action="addPictureSubmit.php" method="post">
					
		  <strong>Select your image(s) to upload:</strong>
			<input type="file" name="Image[]" id="Image" multiple/>
					
			<input type="submit" name="submit" value="Upload Image(s)" />
			
					
		</form>
	
  </body>
</html>
