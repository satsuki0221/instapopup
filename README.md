# instapopup

インスタグラムのAPIを使用して、  
Wordpress内に、リスト形式で並べて、  
クリックするとポップアップ(モーダルウィンドウ)が開きます。  
ポップアップには、colorbox( http://www.jacklmoore.com/colorbox/ )  
を利用しました。  

# 使用方法

管理画面で、
username,  
access token,  
表示数  ,
を登録します。  

Access Tokenの取得に関してはGoogleにて検索をかけてください。  
  
その後、表示したい箇所に  
<?php echo $instapopup->create(); ?>  
と記述してください。
