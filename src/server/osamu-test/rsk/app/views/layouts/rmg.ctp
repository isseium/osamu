<!DOCTYPE html>
<html>
    <head>
    	<?php echo $html->charset();?>
    	<?php echo $html->meta('icon', '../icon/RMG.ico'); ?>
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />
        <title>RMG レンタルマネジメント</title>
       	<?php echo $html->css('jquery.mobile-1.0.1.min'); //jqm css ?>
        <style></style>
       	<?php echo $this->Html->script('jquery-1.7.min'); //jquery本体 ?>
       	<?php echo $this->Html->script('jquery.mobile-1.0.1.min'); //jqm本体 ?>
    </head>
    <body>
        <div data-role="page" id="page1">
            <div data-theme="a" data-role="header"><h1>RMG</h1></div>

            <div data-role="content">
            	<?php echo $content_for_layout;?>
            </div>
        </div>
        <script>
            //App custom javascript
        </script>
    </body>
</html>
