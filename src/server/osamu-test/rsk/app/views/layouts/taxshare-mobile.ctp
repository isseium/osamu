<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>タクシェア！</title>
        <?php echo $html->css('jquery.mobile-1.2.0.min'); //rental ?>  
        <link rel="stylesheet" type="text/css" href="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.min.css" /> 

        <?php echo $this->Html->script('jquery-1.8.2.min'); //jquery本体 ?>
     	<?php echo $this->Html->script('jquery.mobile-1.2.0.min'); //jquerymobile本体 ?>
     	<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.core.min.js"></script>
<!-- 以下はCalBox modeの時に必要 -->
<script type="text/javascript" src="http://www31092u.sakura.ne.jp/~g031j080/jqm-datebox.mode.calbox.custom.js"></script>
<!-- 以下は日本語表記のために必要 -->
<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/i18n/jquery.mobile.datebox.i18n.ja.utf8.js"></script>

     	
    </head>
    <body>
    	<div data-role="page" id="page1">
    		<!--ヘッダー-->
            <div data-theme="a" data-role="header" data-position="inline">
            	<h3>タクシェア</h3>
            	<a href="option" data-icon="gear" class="ui-btn-right">Options</a>
            </div>
            <div data-role="content" style="padding: 15px">
        	<!-- Home -->	
        	<?php echo $content_for_layout;?>  
        	
        		<div data-role="footer">
        			<div data-role="navbar" data-iconpos="left">
						<ul>
							<li><a href="driver">乗せる</a></li>
							<li><a href="main" class="ui-btn-active" data-icon="home">ホーム</a></li>
							<li><a href="passenger">乗る</a></li>
						</ul>
					</div><!-- /navbar -->
				</div><!-- /footer -->
        		<a href="logout">ログアウト</a> <?php echo $user['TsUser']['username'];?>
     		</div>
     		<!--
            <div data-theme="a" data-role="footer" data-position="fixed">
                <h3>
                    Footer
                </h3>
            </div>
           -->
        </div>
     
        <script>
            //App custom javascript
        </script>
    </body>
</html>