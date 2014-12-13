<?php
namespace Dandelion;

require_once 'lib/bootstrap.php';

if (!Gatekeeper\authenticated()) {
	header( 'Location: index.php' );
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="static/images/favicon.ico" />
		<?php echo loadCssSheets(); ?>
		<title>Dandelion Web Log</title>
	</head>
	<body onLoad="CategoryManage.grabNextLevel('0:0');">
        <header>
            <?php include 'views/header.php'; ?>
        </header>
		
        <h3>Category Management</h3>
        
        <div id="message"></div>

        <form method="post">
        	<div name="categorySelects" id="categorySelects"></div><br><br>
        	
        	<?php
        	if ($User_Rights->authorized('addcat')) {
				echo '<input type="button" class="dButton" onClick="CategoryManage.createNew();" value="Add Category">';
			}
			
			if ($User_Rights->authorized('editcat')) {
				echo '<input type="button" class="dButton" onClick="CategoryManage.editCat();" value="Edit Category">';
			}
			
			if ($User_Rights->authorized('deletecat')) {
				echo '<input type="button" class="dButton" onClick="CategoryManage.deleteCat();" value="Delete Category">';
			}
        	?>
        </form>
        
        <footer>
            <?php include_once 'views/footer.php'; ?>
        </footer>
	</body>
    
    <?php echo loadJS("jquery", "catmanage");?>
</html>
