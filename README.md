#　アプリケーション名：Atte（アッテ）
    ある企業の勤怠管理システム
    <img width="1694" alt="Atte　トップ画面" src="https://github.com/atsukonakazawa/20240128_atsukonakazawa_atte2/assets/140526473/718fb8e1-0bce-4998-82e7-61c66796a5cc">



##　作成した目的  
    人事評価のため

##　アプリケーションURL  
    ⚫︎開発環境：      https://github.com/atsukonakazawa/20240128_atsukonakazawa_atte2.git  
    ⚫︎phpMyAdmin:   http://localhost:8080/  
    ⚫︎デプロイのURL:  
##AWS  　　  　　
    ⚫︎AWSアカウント  　　  　ルートユーザーEメールアドレス：tsqe8qm1bmqztbxbjre9@docomo.ne.jp  
                            　パスワード：Atte4atte4    
    ⚫︎進捗状況  　　  　　　①EC2アカウント、S3ロール・バケット、RDSデータベース作成済  
        　　　　　　　　②Amazon LinuxにてNGINX、My SQL、php、Composerインストール済  
            ③Amazon Linux ~/var/www/にgithubからクローンしたAtteのファイルを配置済  

##　他のリポジトリ  
    特になし  

##　機能一覧  
    <img width="479" alt="Atte  機能一覧" src="https://github.com/atsukonakazawa/20240128_atsukonakazawa_atte2/assets/140526473/8180aa2b-cd49-4701-9bce-e932d6084bf0">  

##　テーブル設計  
    <img width="735" alt="Atte　テーブル仕様書" src="https://github.com/atsukonakazawa/20240128_atsukonakazawa_atte2/assets/140526473/f8c5b721-24a2-404f-89ce-5c2263f060df">  

##　ER図  
    <img width="708" alt="Atte ER図" src="https://github.com/atsukonakazawa/20240128_atsukonakazawa_atte2/assets/140526473/619f32e8-66e9-4517-8935-752c86444cdf">  

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
    

