<!DOCTYPE html>
	<html lang="ja">
    <head>
    	<?php echo $html->charset();?>
    	<?php echo $html->meta('icon', '../icon/rental.ico'); ?>
        <title>
        <?php echo $title_for_layout;//タイトル,省略の場合はクラス名 ?>
        </title>
		<?php echo $html->css('bootstrap'); //bootstrap ?>
		<?php echo $html->css('rental'); //rental ?>
		<?php //echo $html->css('popupwindow'); //アラート用 ?>
       	<?php echo $this->Html->script('jquery-1.7.min'); //jquery本体 ?>
       	<?php echo $this->Html->script('bootstrap'); //bootstrap ?>
       	<?php //echo $this->Html->script('popupwindow'); //アラート用 ?>
    </head>
    <body>
    <div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<ul class="nav pull">
				<li><a href=""></a></li><li><a href=""></a></li><li><a href=""></a></li><li><a href=""></a></li><li><a href=""></a></li><li><a href=""></a></li>								
				<li><a href="index">借りる</a></li>
				<!--<li><a href="request">申請中</a></li>
				<li class="active"><a href="index">物品一覧</a></li>-->
				<li><a href="toReturn">返す</a></li>
				<li><a href="lend">貸す</a></li>
			</ul>
			<ul class="nav pull-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<?php echo $user['SrUser']['username']; ?>
					<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="myItem">自分の持ち物</a></li>
						<li><a href="setting">アカウント設定</a></li>
						<li><a href="help">ヘルプ</a></li>
						<li class="divider"></li>
						<li><a href="logout">ログアウト</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	</div>
    
    <div id="content-main">
    	<div class="container-fluid">
    		<div class="row-fluid">
    			<div class="span1 well">
    				<a target="_blank" href="http://www.facebook.com/371437352881431"><img src="../icon/rental.ico" /> </a>
			   		レンタル支援システム
    				<h1 style="font:bold 30px 'Times New Roman';">Srental<small> ver.β</small></h1>    			
    				<div class="alert alert-block alert-success">
						<small><?php echo $user['SrUser']['point']; ?>RP</small>
					</div>
					<?php if($req_c){echo '<div class="alert alert-block alert-error"><small>申請' .$req_c. '件アリ</small></div>';}?>
					<?php echo '<a href="http://www.facebook.com/profile.php?id='. $user['SrUser']['facebook_id'] .'"><img src="https://graph.facebook.com/'. $user['SrUser']['facebook_id'] .'/picture" /></a>'; ?>
    			</div>
    			<div class="span10">
    			<?php echo $content_for_layout;?>    				
    			</div>
    		</div>
    	</div>
    </div>
    
    <script type="text/javascript" language="javascript">
		<!--
		//facebookのURLの末尾に#_=_が入るので対処
		if(document.URL.match(/#_=_/)){
			location.href='http://n0.x0.to/rsk/rentals/index';
		}
		// -->
	</script>
    </body>
</html>