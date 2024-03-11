#　アプリケーション名：Atte（アッテ）
    ある企業の勤怠管理システム
    <img width="1694" alt="Atte　トップ画面" src="https://github.com/atsukonakazawa/20240128_atsukonakazawa_atte2/assets/140526473/718fb8e1-0bce-4998-82e7-61c66796a5cc">



##　作成した目的  
    人事評価のため

##　アプリケーションURL  
    ⚫︎開発環境：      https://github.com/atsukonakazawa/20240226_atsukonakazawa_atte4.git  
    ⚫︎phpMyAdmin:   http://localhost:8080/  
    ⚫︎mailhog:      http://localhost:8025/  
    ⚫︎デプロイのURL:  ec2-43-207-193-146.ap-northeast-1.compute.amazonaws.com

##AWS   
    ⚫︎AWSにおける進捗状況  
     ①EC2インスタンス、S3ロール・バケット、RDSデータベース作成かつそれぞれ接続済  
     ②Amazon LinuxにてNGINX、My SQL、php、Composerインストール済  
     ③一度はgithubからEC2インスタンスにgit clone、その後S3バケットに転送にも成功したが、ローカルでファイルを手直しした際にgit pushできなかったためにEC2、S3からclone済みデータを削除したところ、エラー続きで再びcloneできなくなってしまった。  

##　他のリポジトリ  
    特になし  

##　機能一覧  
      <img width="673" alt="0310機能一覧" src="https://github.com/atsukonakazawa/20240226_atsukonakazawa_atte4/assets/140526473/903114f2-bb39-4a1b-80a9-3ab082822d2c">


##　テーブル設計  
     <img width="729" alt="0310テーブル仕様書" src="https://github.com/atsukonakazawa/20240226_atsukonakazawa_atte4/assets/140526473/d1bf0939-0182-4efa-820a-3d3e0011fcd5">


##　ER図  
    <img width="754" alt="0310ER図" src="https://github.com/atsukonakazawa/20240226_atsukonakazawa_atte4/assets/140526473/34925ee8-7272-487c-b82d-ff85805a69b1">



##　使用技術  
    ⚫︎PHP 8.2.11 (cli)  
    ⚫︎Laravel Framework 8.83.27  
    ⚫︎mysql  8.0.26 - MySQL Community Server - GPL   

##　ローカル環境構築    
    Dockerビルド  
    1.git clone git@github.com:coachtech-material/laravel-docker-template.git  
    2.docker compose up -d --build  
    ※MySQLは、OSによっては起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。  
    Laravel環境構築  
    1.docker compose exec php bash  
    2.composer install  
    3..env.exampleファイルから.envを作成し、環境変数を変更  
    4.php artisan key:generate  
    5.php artisan migrate  
    

