# connect-cms_option
Connect-CMSのオプションプラグイン

「connect-cms_option」リポジトリは、Connect-CMS の標準パッケージには含まれない、オプションのプラグインなどを格納するためのリポジトリです。  
Connect-CMS の標準パッケージは以下を参照してください。  
https://github.com/opensource-workshop/connect-cms  
  
データベースの migration は以下のコマンドで行います。  
php artisan migrate --path=database/migrations_option  

## Dronstudy

Connect-CMS v1.8.0（予定）からBlocklyが本体に同梱されなくなりました。
DronStudyを利用する場合は、当リポジトリのblockly.zipを解凍して、Connect-CMSのpublic/jsディレクトリに追加してください。
