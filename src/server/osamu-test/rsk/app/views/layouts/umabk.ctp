<!DOCTYPE html>
<html>
    <head>
    	<?php echo $html->charset();?>
    	<?php echo $html->meta('icon', '../icon/RMG.ico'); ?>
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />
        <title>うまぶ</title>
		<link rel="stylesheet"  href="http://code.jquery.com/mobile/1.0.1/jquery.mobile-1.0.1.min.css" />
       	<?php //echo $html->css('jquery.mobile-1.0.1.min'); //jqm css ?>
       	<?php echo $this->Html->script('jquery-1.7.min'); //jquery本体 ?>
       	<?php echo $this->Html->script('jquery.mobile-1.0.1.min'); //jqm本体 ?>
    </head>
    <body>
        <div data-role="page" id="page1">
            <div data-role="header" data-position="inline" data-backbtn="false">
				<h1>うまぶ</h1>
				<a href="setting" data-icon="gear" class="ui-btn-right">Options</a>
			</div>            
            
            <div data-role="content">
            	<?php echo $content_for_layout;?>
            </div>
            
            <?php
            /*if(($this->action != 'index') || ($this->action != 'add')){//ログイン前は非表示
            echo '
            <div data-role="footer">
				<div data-role="navbar" data-iconpos="bottom">
					<ul>
						<li><a href="#search" data-icon="search">Search</a></li>
						<li><a href="#home" data-icon="home">Home</a></li>
						<li><a href="#index" data-icon="forward">Logout</a></li>
					</ul>
				</div><!-- /navbar -->
			</div><!-- /footer -->
			';
			}*/
 			?>
     
        </div>
        <script>
            //App custom javascript
        </script>
    </body>
</html>
